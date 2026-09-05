<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\Student;
use App\Models\TeacherSchedule;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function createForStudent(
        Student $student,
        array $data
    ): Reservation {
        return DB::transaction(function () use (
            $student,
            $data
        ) {

            $student = Student::query()
                ->whereKey($student->getKey())
                ->lockForUpdate()
                ->firstOrFail();


            $schedule = TeacherSchedule::query()
                ->whereKey($data['schedule_id'])
                ->lockForUpdate()
                ->firstOrFail();


            /*
             * Teacher と Schedule が一致するか
             */
            if (
                (int) $schedule->teacher_id
                !==
                (int) $data['teacher_id']
            ) {
                throw ValidationException::withMessages([
                    'teacher_id' =>
                        '指定された講師と勤務シフトが一致しません。',
                ]);
            }


            /*
             * confirmed Scheduleのみ
             */
            if ($schedule->status !== 'confirmed') {
                throw ValidationException::withMessages([
                    'schedule_id' =>
                        'この勤務シフトは予約できません。',
                ]);
            }


            $teacher = $schedule
                ->teacher()
                ->firstOrFail();


            /*
             * TeacherがMaterialを教えられるか
             */
            $canTeachMaterial = $teacher
                ->materials()
                ->where(
                    'materials.material_id',
                    $data['material_id']
                )
                ->exists();


            if (!$canTeachMaterial) {
                throw ValidationException::withMessages([
                    'material_id' =>
                        'この講師は選択した教材を担当できません。',
                ]);
            }


            $startAt = CarbonImmutable::parse(
                $data['start_at']
            );

            $endAt = CarbonImmutable::parse(
                $data['end_at']
            );


            /*
             * 過去は不可
             */
            if ($startAt->isPast()) {
                throw ValidationException::withMessages([
                    'start_at' =>
                        '過去の時間は予約できません。',
                ]);
            }


            /*
             * 30分予約
             */
            if (
                !$endAt->equalTo(
                    $startAt->addMinutes(30)
                )
            ) {
                throw ValidationException::withMessages([
                    'end_at' =>
                        '予約時間は30分で指定してください。',
                ]);
            }


            /*
             * Scheduleの時間内か
             */
            $date = $schedule
                ->available_date
                ->format('Y-m-d');

            $scheduleStart =
                CarbonImmutable::parse(
                    $date . ' '
                    . $schedule->start_time
                );

            $scheduleEnd =
                CarbonImmutable::parse(
                    $date . ' '
                    . $schedule->end_time
                );


            if (
                $startAt->lt($scheduleStart)
                ||
                $endAt->gt($scheduleEnd)
            ) {
                throw ValidationException::withMessages([
                    'start_at' =>
                        '勤務時間外のため予約できません。',
                ]);
            }


            /*
             * ScheduleException確認
             */
            $hasException = $schedule
                ->exceptions()
                ->where('status', 'active')
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


            if ($hasException) {
                throw ValidationException::withMessages([
                    'start_at' =>
                        'この時間は講師が予約を受け付けていません。',
                ]);
            }


            /*
             * Teacherの予約重複確認
             */
            $hasTeacherReservation =
                $schedule
                    ->reservations()
                    ->whereHas(
                        'status',
                        function ($query) {
                            $query->whereIn(
                                'status_code',
                                [
                                    'pending',
                                    'confirmed',
                                ]
                            );
                        }
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


            if ($hasTeacherReservation) {
                throw ValidationException::withMessages([
                    'start_at' =>
                        'この時間はすでに予約されています。',
                ]);
            }


            /*
             * Student自身の重複予約
             */
            $hasStudentReservation =
                Reservation::query()
                    ->where(
                        'student_id',
                        $student->id
                    )
                    ->whereHas(
                        'status',
                        function ($query) {
                            $query->whereIn(
                                'status_code',
                                [
                                    'pending',
                                    'confirmed',
                                ]
                            );
                        }
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


            if ($hasStudentReservation) {
                throw ValidationException::withMessages([
                    'start_at' =>
                        '同じ時間に別の予約があります。',
                ]);
            }


            /*
             * confirmed status
             */
            $status = ReservationStatus::query()
                ->where(
                    'status_code',
                    'confirmed'
                )
                ->firstOrFail();


            /*
             * Reservation作成
             */
            $reservation = Reservation::create([
                'student_id' =>
                    $student->id,

                'teacher_id' =>
                    $teacher->id,

                'schedule_id' =>
                    $schedule->schedule_id,

                'material_id' =>
                    $data['material_id'],

                'status_id' =>
                    $status->status_id,

                'start_at' =>
                    $startAt,

                'end_at' =>
                    $endAt,

                'point_cost' =>
                    (int) $teacher->point_consumed,
            ]);


            return $reservation->refresh();
        });
    }
}
