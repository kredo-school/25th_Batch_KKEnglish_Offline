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

        foreach ([
            'confirmed',
            'completed',
            'absent',
            'cancelled',
        ] as $statusCode) {

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
                '2026-09-04',
                '2026-10-15',
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
        | 30分間隔で25分レッスンを作成
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

            // このTeacherが担当できる教材
            $materials = $schedule->teacher->materials;

            if ($materials->isEmpty()) {
                continue;
            }

            $shiftStart = Carbon::parse(
                $schedule->available_date
                . ' '
                . $schedule->start_time
            );

            $shiftEnd = Carbon::parse(
                $schedule->available_date
                . ' '
                . $schedule->end_time
            );

            /*
             * 夜勤など日付をまたぐ場合
             */
            if ($shiftEnd->lte($shiftStart)) {
                $shiftEnd->addDay();
            }

            $current = $shiftStart->copy();

            while (
                $current->copy()->addMinutes(25)->lte($shiftEnd)
            ) {

                $slots->push([
                    'schedule' => $schedule,
                    'start_at' => $current->copy(),
                    'end_at' => $current
                        ->copy()
                        ->addMinutes(25),
                    'materials' => $materials,
                ]);

                // 次のレッスンまで5分
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
        | Shuffle
        |--------------------------------------------------------------------------
        */

        $slots = $slots->shuffle();

        /*
        |--------------------------------------------------------------------------
        | 目標100件
        |--------------------------------------------------------------------------
        */

        $targetCount = 100;
        $createdCount = 0;

        /*
        |--------------------------------------------------------------------------
        | Reservations
        |--------------------------------------------------------------------------
        */

        foreach ($slots as $slot) {

            if ($createdCount >= $targetCount) {
                break;
            }

            $schedule = $slot['schedule'];
            $startAt = $slot['start_at'];
            $endAt = $slot['end_at'];

            /*
            |--------------------------------------------------------------------------
            | Teacherの重複チェック
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
            | Studentを選択
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
                ->first(function ($student) use (
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

                });

            if (!$student) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Material
            |--------------------------------------------------------------------------
            |
            | Teacherが担当できる教材だけ使用
            |
            */

            $material = $slot['materials']->random();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            if ($startAt->isPast()) {

                $statusCode = fake()->randomElement([
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
            | Cancel情報
            |--------------------------------------------------------------------------
            */

            $cancelledBy = null;
            $cancelledAt = null;
            $cancellationReason = null;

            if ($statusCode === 'cancelled') {

                $cancelledBy = $student->user_id;

                $cancelledAt = $startAt
                    ->copy()
                    ->subDay();

                $cancellationReason =
                    'Cancelled by student (Seeder data)';
            }

            /*
            |--------------------------------------------------------------------------
            | Point Cost
            |--------------------------------------------------------------------------
            |
            | Teacherのpoint_consumedを使用
            | nullの場合は100
            |
            */

            $pointCost =
                $schedule->teacher->point_consumed
                ?? 100;

            /*
            |--------------------------------------------------------------------------
            | Create Reservation
            |--------------------------------------------------------------------------
            */

            Reservation::create([
                'student_id' => $student->id,

                'teacher_id' =>
                    $schedule->teacher_id,

                'schedule_id' =>
                    $schedule->schedule_id,

                'material_id' =>
                    $material->material_id,

                'status_id' =>
                    $statuses[$statusCode]->status_id,

                'start_at' => $startAt,
                'end_at' => $endAt,

                'point_cost' => $pointCost,

                'cancelled_by' =>
                    $cancelledBy,

                'cancelled_at' =>
                    $cancelledAt,

                'cancellation_reason' =>
                    $cancellationReason,
            ]);

            $createdCount++;
        }

        $this->command?->info(
            "Reservations created: {$createdCount}"
        );
    }
}
