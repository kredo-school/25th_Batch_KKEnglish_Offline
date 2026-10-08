<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\Student;
use App\Models\TeacherSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use RuntimeException;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $students = Student::query()
            ->orderBy('id')
            ->limit(50)
            ->get();

        if ($students->isEmpty()) {
            throw new RuntimeException(
                'Studentがありません。StudentSeederを先に実行してください。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Reservation Statuses
        |--------------------------------------------------------------------------
        */

        $statuses = ReservationStatus::query()
            ->whereIn('status_code', [
                'confirmed',
                'completed',
                'absent',
                'cancelled',
            ])
            ->get()
            ->keyBy('status_code');

        foreach (
            [
                'confirmed',
                'completed',
                'absent',
                'cancelled',
            ] as $statusCode
        ) {
            if (!$statuses->has($statusCode)) {
                throw new RuntimeException(
                    "ReservationStatus: {$statusCode} がありません。"
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Teacher Schedules
        |--------------------------------------------------------------------------
        */

        $schedules = TeacherSchedule::query()
            ->with([
                'teacher.materials',
            ])
            ->where('status', 'confirmed')
            ->whereBetween('available_date', [
                '2026-09-25',
                '2026-10-31',
            ])
            ->orderBy('available_date')
            ->orderBy('teacher_id')
            ->get();

        if ($schedules->isEmpty()) {
            throw new RuntimeException(
                'TeacherScheduleがありません。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 予約可能枠を作成
        |--------------------------------------------------------------------------
        |
        | 30分間隔で25分レッスン + 5分休憩
        |
        | 例:
        | 09:00 - 09:25
        | 09:30 - 09:55
        | 10:00 - 10:25
        |
        */

        $slots = collect();

        foreach ($schedules as $schedule) {

            if (!$schedule->teacher) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Teacherが担当できる教材
            |--------------------------------------------------------------------------
            */

            $materials = $schedule->teacher->materials;

            if ($materials->isEmpty()) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Schedule Date / Time
            |--------------------------------------------------------------------------
            */

            $date = Carbon::parse(
                $schedule->available_date
            )->toDateString();

            $startTime = Carbon::parse(
                $schedule->start_time
            )->format('H:i:s');

            $endTime = Carbon::parse(
                $schedule->end_time
            )->format('H:i:s');

            $shiftStart = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                "{$date} {$startTime}"
            );

            $shiftEnd = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                "{$date} {$endTime}"
            );

            /*
            |--------------------------------------------------------------------------
            | 夜勤など日付をまたぐ場合
            |--------------------------------------------------------------------------
            */

            if ($shiftEnd->lte($shiftStart)) {
                $shiftEnd->addDay();
            }

            /*
            |--------------------------------------------------------------------------
            | 25分レッスン + 5分休憩
            |--------------------------------------------------------------------------
            */

            $current = $shiftStart->copy();

            while (
                $current
                    ->copy()
                    ->addMinutes(30)
                    ->lte($shiftEnd)
            ) {
                $slots->push([
                    'schedule' => $schedule,
                    'start_at' => $current->copy(),
                    'end_at' => $current
                        ->copy()
                        ->addMinutes(30),
                    'materials' => $materials,
                ]);

                // 次のレッスンまで5分休憩を含めて30分
                $current->addMinutes(30);
            }
        }

        if ($slots->isEmpty()) {
            throw new RuntimeException(
                '予約可能な時間枠を生成できませんでした。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Reservation Settings
        |--------------------------------------------------------------------------
        */

        $targetCount = 15000;

        $completedPerTeacher = 30;

        $futurePerTeacher = 30;

        $createdCount = 0;

        /*
        |--------------------------------------------------------------------------
        | Phase 1
        |--------------------------------------------------------------------------
        |
        | Teacher 30人全員に
        | completed予約を最低30件作成
        |
        | 30 teachers × 30 = 900 reservations
        |
        */

        $teacherIds = $schedules
            ->pluck('teacher_id')
            ->unique()
            ->sort()
            ->values();

        foreach ($teacherIds as $teacherId) {

            /*
            |--------------------------------------------------------------------------
            | このTeacherの過去枠だけ取得
            |--------------------------------------------------------------------------
            |
            | completedは過去の授業だけにする
            |
            */

            $teacherPastSlots = $slots
                ->filter(function ($slot) use ($teacherId) {

                    return
                        $slot['schedule']->teacher_id == $teacherId
                        && $slot['start_at']->isPast();
                })
                ->shuffle();

            /*
            |--------------------------------------------------------------------------
            | 過去枠が10件未満なら警告
            |--------------------------------------------------------------------------
            */

            if (
                $teacherPastSlots->count()
                < $completedPerTeacher
            ) {
                $this->command?->warn(
                    "Teacher {$teacherId}: "
                    . "過去枠が{$teacherPastSlots->count()}件しかありません。"
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Teacherごとに10件作成
            |--------------------------------------------------------------------------
            */

            $teacherCreatedCount = 0;

            foreach ($teacherPastSlots as $slot) {

                if (
                    $teacherCreatedCount
                    >= $completedPerTeacher
                ) {
                    break;
                }

                if ($createdCount >= $targetCount) {
                    break 2;
                }

                $schedule = $slot['schedule'];

                $startAt = $slot['start_at'];

                $endAt = $slot['end_at'];

                /*
                |--------------------------------------------------------------------------
                | Teacher Conflict
                |--------------------------------------------------------------------------
                */

                $teacherConflict = Reservation::query()
                    ->where(
                        'teacher_id',
                        $schedule->teacher_id
                    )
                    ->where(
                        'start_at',
                        '<',
                        $endAt
                    )
                    ->where(
                        'end_at',
                        '>',
                        $startAt
                    )
                    ->exists();

                if ($teacherConflict) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Student
                |--------------------------------------------------------------------------
                |
                | 予約数が少ないStudentを優先
                |
                */

                $student = $students
                    ->sortBy(function ($student) {

                        return Reservation::query()
                            ->where(
                                'student_id',
                                $student->id
                            )
                            ->count();
                    })
                    ->first(
                        function ($student) use (
                            $startAt,
                            $endAt
                        ) {

                            return !Reservation::query()
                                ->where(
                                    'student_id',
                                    $student->id
                                )
                                ->where(
                                    'start_at',
                                    '<',
                                    $endAt
                                )
                                ->where(
                                    'end_at',
                                    '>',
                                    $startAt
                                )
                                ->exists();
                        }
                    );

                if (!$student) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Material
                |--------------------------------------------------------------------------
                */

                $material =
                    $slot['materials']->random();

                /*
                |--------------------------------------------------------------------------
                | Point Cost
                |--------------------------------------------------------------------------
                */

                $pointCost =
                    $schedule
                        ->teacher
                        ->point_consumed
                    ?? 100;

                /*
                |--------------------------------------------------------------------------
                | Completed Reservation
                |--------------------------------------------------------------------------
                */

                Reservation::create([
                    'student_id' =>
                        $student->id,

                    'teacher_id' =>
                        $schedule->teacher_id,

                    'schedule_id' =>
                        $schedule->schedule_id,

                    'material_id' =>
                        $material->material_id,

                    'status_id' =>
                        $statuses['completed']
                            ->status_id,

                    'start_at' =>
                        $startAt,

                    'end_at' =>
                        $endAt,

                    'point_cost' =>
                        $pointCost,

                    'cancelled_by' =>
                        null,

                    'cancelled_at' =>
                        null,

                    'cancellation_reason' =>
                        null,
                ]);

                $createdCount++;

                $teacherCreatedCount++;
            }

            /*
            |--------------------------------------------------------------------------
            | Teacher Result
            |--------------------------------------------------------------------------
            */

            $this->command?->info(
                "Teacher {$teacherId}: "
                . "{$teacherCreatedCount} completed reservations"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Phase 1 Result
        |--------------------------------------------------------------------------
        */

        $this->command?->info(
            "Guaranteed completed reservations: {$createdCount}"
        );

        /*
        |--------------------------------------------------------------------------
        | Phase 2
        |--------------------------------------------------------------------------
        |
        | Teacher 30人全員に
        | 未来(confirmed)の予約を最低X件作成
        |
        */

        foreach ($teacherIds as $teacherId) {

            /*
            |--------------------------------------------------------------------------
            | このTeacherの未来枠だけ取得
            |--------------------------------------------------------------------------
            */
            $futureLimit = now()->copy()->addDays(14);

            $teacherFutureSlots = $slots
                ->filter(function ($slot) use ($teacherId, $futureLimit) {
                    return
                        $slot['schedule']->teacher_id == $teacherId
                        && $slot['start_at']->isFuture()
                        && $slot['start_at']->lte($futureLimit);
                })
                ->shuffle();

            /*
            |--------------------------------------------------------------------------
            | 未来枠が少ないなら警告
            |--------------------------------------------------------------------------
            */
            if ($teacherFutureSlots->count() < $futurePerTeacher) {
                $this->command?->warn(
                    "Teacher {$teacherId}: "
                    . "未来枠が{$teacherFutureSlots->count()}件しかありません。"
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Teacherごとに作成
            |--------------------------------------------------------------------------
            */
            $teacherCreatedCount = 0;

            foreach ($teacherFutureSlots as $slot) {

                if ($teacherCreatedCount >= $futurePerTeacher) {
                    break;
                }

                if ($createdCount >= $targetCount) {
                    break 2;
                }

                $schedule = $slot['schedule'];
                $startAt = $slot['start_at'];
                $endAt = $slot['end_at'];

                /*
                |--------------------------------------------------------------------------
                | Teacher Conflict
                |--------------------------------------------------------------------------
                */
                $teacherConflict = Reservation::query()
                    ->where('teacher_id', $schedule->teacher_id)
                    ->where('start_at', '<', $endAt)
                    ->where('end_at', '>', $startAt)
                    ->exists();

                if ($teacherConflict) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Student
                |--------------------------------------------------------------------------
                */
                $student = $students
                    ->sortBy(function ($student) {
                        return Reservation::query()
                            ->where('student_id', $student->id)
                            ->count();
                    })
                    ->first(function ($student) use ($startAt, $endAt) {
                        return !Reservation::query()
                            ->where('student_id', $student->id)
                            ->where('start_at', '<', $endAt)
                            ->where('end_at', '>', $startAt)
                            ->exists();
                    });

                if (!$student) {
                    continue;
                }

                $material = $slot['materials']->random();
                $pointCost = $schedule->teacher->point_consumed ?? 100;

                /*
                |--------------------------------------------------------------------------
                | Confirmed Reservation (未来なので確定)
                |--------------------------------------------------------------------------
                */
                Reservation::create([
                    'student_id' => $student->id,
                    'teacher_id' => $schedule->teacher_id,
                    'schedule_id' => $schedule->schedule_id,
                    'material_id' => $material->material_id,
                    'status_id' => $statuses['confirmed']->status_id, // ★ confirmed固定
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'point_cost' => $pointCost,
                    'cancelled_by' => null,
                    'cancelled_at' => null,
                    'cancellation_reason' => null,
                ]);

                $createdCount++;
                $teacherCreatedCount++;
            }

            $this->command?->info(
                "Teacher {$teacherId}: "
                . "{$teacherCreatedCount} confirmed(future) reservations"
            );
        }

        $this->command?->info(
            "Guaranteed future reservations added. Total so far: {$createdCount}"
        );

        /*
        |--------------------------------------------------------------------------
        | Phase 3
        |--------------------------------------------------------------------------
        |
        | 残りをランダム生成
        |
        | Phase 1 = 約240件
        | Phase 2 = 約160件
        | 合計 = 400件
        |
            */
        $slots = $slots->shuffle();

        foreach ($slots as $slot) {

            if ($createdCount >= $targetCount) {
                break;
            }

            $schedule = $slot['schedule'];

            $startAt = $slot['start_at'];

            $endAt = $slot['end_at'];

            /*
            |--------------------------------------------------------------------------
            | Teacher Conflict
            |--------------------------------------------------------------------------
            |
            | Phase 1で使用済みの枠もここで除外される
            |
            */

            $teacherConflict = Reservation::query()
                ->where(
                    'teacher_id',
                    $schedule->teacher_id
                )
                ->where(
                    'start_at',
                    '<',
                    $endAt
                )
                ->where(
                    'end_at',
                    '>',
                    $startAt
                )
                ->exists();

            if ($teacherConflict) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            |
            | 予約数が少ないStudentを優先
            |
            */

            $student = $students
                ->sortBy(function ($student) {

                    return Reservation::query()
                        ->where(
                            'student_id',
                            $student->id
                        )
                        ->count();
                })
                ->first(
                    function ($student) use (
                        $startAt,
                        $endAt
                    ) {

                        return !Reservation::query()
                            ->where(
                                'student_id',
                                $student->id
                            )
                            ->where(
                                'start_at',
                                '<',
                                $endAt
                            )
                            ->where(
                                'end_at',
                                '>',
                                $startAt
                            )
                            ->exists();
                    }
                );

            if (!$student) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Material
            |--------------------------------------------------------------------------
            */

            $material =
                $slot['materials']->random();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            |
            | 過去
            | → completed / absent / cancelled
            |
            | 未来
            | → confirmed
            |
            */

            if ($startAt->isPast()) {

                $statusCode =
                    fake()->randomElement([
                        'completed',
                        'completed',
                        'completed',
                        'completed',
                        'absent',
                        'cancelled',
                    ]);

            } else {

                $statusCode = 'confirmed';
            }

            /*
            |--------------------------------------------------------------------------
            | Cancel Information
            |--------------------------------------------------------------------------
            */

            $cancelledBy = null;

            $cancelledAt = null;

            $cancellationReason = null;

            if ($statusCode === 'cancelled') {

                $cancelledBy =
                    $student->user_id;

                $cancelledAt =
                    $startAt
                        ->copy()
                        ->subDay();

                $cancellationReason =
                    'Cancelled by student (Seeder data)';
            }

            /*
            |--------------------------------------------------------------------------
            | Point Cost
            |--------------------------------------------------------------------------
            */

            $pointCost =
                $schedule
                    ->teacher
                    ->point_consumed
                ?? 100;

            /*
            |--------------------------------------------------------------------------
            | Create Reservation
            |--------------------------------------------------------------------------
            */

            Reservation::create([
                'student_id' =>
                    $student->id,

                'teacher_id' =>
                    $schedule->teacher_id,

                'schedule_id' =>
                    $schedule->schedule_id,

                'material_id' =>
                    $material->material_id,

                'status_id' =>
                    $statuses[$statusCode]
                        ->status_id,

                'start_at' =>
                    $startAt,

                'end_at' =>
                    $endAt,

                'point_cost' =>
                    $pointCost,

                'cancelled_by' =>
                    $cancelledBy,

                'cancelled_at' =>
                    $cancelledAt,

                'cancellation_reason' =>
                    $cancellationReason,
            ]);

            $createdCount++;
        }

        /*
        |--------------------------------------------------------------------------
        | Final Result
        |--------------------------------------------------------------------------
        */

        $this->command?->info(
            "Reservations created: {$createdCount}"
        );

        if ($createdCount < $targetCount) {

            $this->command?->warn(
                "目標{$targetCount}件に対して"
                . "{$createdCount}件しか作成できませんでした。"
            );
        }
    }
}
