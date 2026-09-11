<?php

namespace App\Services;

use App\Models\TeacherSchedule;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * 指定した先生・日付の予約可能枠を取得
     */

    public function getAvailability(
        int $teacherId,
        string $date,
        int $studentId
    ): array {
        $dayStart = Carbon::parse($date)->startOfDay();
        $dayEnd = $dayStart->copy()->addDay();

        // 生徒の有効な予約を取得
        $studentReservations = Reservation::query()
            ->where('student_id', $studentId)
            ->whereHas('status', function($query) {
                $query->whereIn('status_code', [
                    'pending',
                    'confirmed',
                ]);
            })
            ->where('start_at', '<', $dayEnd)
            ->where('end_at', '>', $dayStart)
            ->get();

        // 先生の有効な予約を取得
        $schedules = TeacherSchedule::query()
            ->where('teacher_id', $teacherId)
            ->whereDate('available_date', $date)
            ->where('status', 'confirmed')
            ->orderBy('start_time')
            ->get();

        $slots = [];

        foreach ($schedules as $schedule) {
            $slots = array_merge(
                $slots,
                $this->generateSlots(
                    $schedule,
                    $studentReservations
                )
            );
        }

        return $slots;
    }
    /**
     * 1つの勤務シフトを30分単位に分割
     */

    public function generateSlots(
        TeacherSchedule $schedule,
        Collection $studentReservations
    ): array {
        $slots = [];

        $date = Carbon::parse($schedule->available_date)
            ->format('Y-m-d');

        $current = Carbon::parse(
            $date . ' ' . $schedule->start_time
        );

        $end = Carbon::parse(
            $date . ' ' . $schedule->end_time
        );

        // 日をまたぐシフトへの対応
        if ($end->lte($current)) {
            $end->addDay();
        }

        // 有効な休止時間
        $exceptions = $schedule->exceptions()
            ->where('status', 'active')
            ->get();

         // 枠を塞ぐ予約
        $reservations = $schedule->reservations()
            ->whereHas('status', function ($query) {
                $query->whereIn('status_code', [
                    'pending',
                    'confirmed',
                ]);
            })
            ->get();

        // 現在時刻から10分後
        $bookingDeadline = now()->addMinutes(10);

        while (
            $current->copy()->addMinutes(30) ->lte($end)
            ) {
            $slotStart = $current->copy();
            $slotEnd = $current
                    ->copy()
                    ->addMinutes(30);

            /*
            * 以下は予約不可
            * ・昨日以前
            * ・今日の過去時間
            * ・現在時刻から10分以内に開始する枠
            */
            $available = $slotStart->gt($bookingDeadline);


            if ($available) {
            // ScheduleExceptionとの重複判定
                foreach ($exceptions as $exception) {

                    $exceptionStart = Carbon::parse(
                        $exception->start_at
                    );

                    $exceptionEnd = Carbon::parse(
                        $exception->end_at
                    );

                    if (
                        $slotStart -> lt($exceptionEnd) &&
                        $slotEnd ->gt($exceptionStart)
                    ) {
                        $available = false;
                        break;
                    }
                }
            }

            // Exceptionで塞がれていない場合だけReservationを確認
            if ($available) {
                foreach ($reservations as $reservation) {

                    $reservationStart = Carbon::parse(
                        $reservation->start_at
                    );

                    $reservationEnd = Carbon::parse(
                        $reservation->end_at
                    );

                    if(
                        $slotStart -> lt($reservationEnd) &&
                        $slotEnd -> gt($reservationStart)
                    ) {
                        $available = false;
                        break;
                    }
                }
            }

            $studentConflict = $studentReservations->contains(
                function(Reservation $reservation) use ($slotStart, $slotEnd) {
                    $studentReservationStart = Carbon::parse($reservation->start_at);
                    $studentReservationEnd = Carbon::parse($reservation->end_at);

                    return $slotStart ->lt($studentReservationEnd) && $slotEnd ->gt($studentReservationStart);
                }
            );

            //生徒自身の予約と重複していたら予約不可

            if ($studentConflict) {
                $available = false;
            }

            $slots[] = [
                'schedule_id' => $schedule->schedule_id,

                'start_at' => $slotStart
                    ->format('Y-m-d H:i:s'),

                'end_at' => $slotEnd
                    ->format('Y-m-d H:i:s'),

                'available' => $available,

                'student_conflict' => $studentConflict,
            ];

            $current->addMinutes(30);
        }

        return $slots;
    }
}
