<?php

namespace App\Services;

use App\Models\TeacherSchedule;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * 指定した先生・日付の予約可能枠を取得
     */
    public function getAvailability(
        int $teacherId,
        string $date
    ): array {
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
                $this->generateSlots($schedule)
            );
        }

        return $slots;
    }
    /**
     * 1つの勤務シフトを30分単位に分割
     */

    public function generateSlots(
        TeacherSchedule $schedule
    ): array {
        $slots = [];

        $date = $schedule->available_date
            ->format('Y-m-d');

        $current = Carbon::parse(
            $date . ' ' . $schedule->start_time
        );

        $end = Carbon::parse(
            $date . ' ' . $schedule->end_time
        );

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

        while ($current < $end) {

            $slotStart = $current->copy();

            $slotEnd = $current
                ->copy()
                ->addMinutes(30);

            // 最初は予約可能と考える
            $available = true;

            // ScheduleExceptionとの重複判定
            foreach ($exceptions as $exception) {

                $exceptionStart = Carbon::parse(
                    $exception->start_at
                );

                $exceptionEnd = Carbon::parse(
                    $exception->end_at
                );

                if (
                    $slotStart < $exceptionEnd &&
                    $slotEnd > $exceptionStart
                ) {
                    $available = false;
                    break;
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

                    if ($slotStart < $reservationEnd && $slotEnd > $reservationStart) {
                        $available = false;
                        break;
                    }
                }
            }

            $slots[] = [
                'schedule_id' => $schedule->schedule_id,

                'start_at' => $slotStart
                    ->format('Y-m-d H:i:s'),

                'end_at' => $slotEnd
                    ->format('Y-m-d H:i:s'),

                'available' => $available,
            ];

            $current->addMinutes(30);
        }

        return $slots;
    }
}
