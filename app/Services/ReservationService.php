<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\TeacherSchedule;
use App\Support\AppTime;
use Carbon\CarbonImmutable;
use DomainException;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function __construct(
        private readonly BookingPolicyService $bookingPolicy
    ) {}

    public function reserve(
        int $studentId,
        int $teacherId,
        int $scheduleId,
        string $lessonType,
        int $usePoints
    ): Reservation {
        return DB::transaction(function () use ($studentId, $teacherId, $scheduleId, $lessonType, $usePoints) {
            /** @var TeacherSchedule $schedule */
            $schedule = TeacherSchedule::query()
                ->where('schedule_id', $scheduleId)
                ->where('teacher_id', $teacherId)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($schedule->status, ['available', 'confirmed'], true)) {
                throw new DomainException('この枠は予約できません。');
            }

            $startBiz = $schedule->startAtBusinessTz();
            $endBiz   = $schedule->endAtBusinessTz();

            $startUtc = $startBiz->setTimezone('UTC');
            $endUtc   = $endBiz->setTimezone('UTC');
            $nowUtc   = CarbonImmutable::now('UTC');

            $this->bookingPolicy->assertReservable($nowUtc, $startUtc, $endUtc, $lessonType);

            $activeCount = $schedule->reservations()
                ->whereIn('status', ['reserved']) // 実ステータス名に合わせて調整
                ->lockForUpdate()
                ->count();

            if ($activeCount >= 1) {
                throw new DomainException('この枠はすでに予約済みです。');
            }

            $reservation = Reservation::query()->create([
                'schedule_id'  => $schedule->schedule_id,
                'student_id'   => $studentId,
                'teacher_id'   => $teacherId,
                'lesson_type'  => $lessonType,
                'start_at'     => $startUtc,
                'end_at'       => $endUtc,
                'used_points'  => $usePoints,
                'status'       => 'reserved',
            ]);

            $schedule->status = 'booked';
            $schedule->save();

            return $reservation;
        });
    }
}
