<?php

namespace App\Services;

use App\Models\ScheduleException;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScheduleExceptionService
{
    public function createForTeacher(
        Teacher $teacher,
        User $actor,
        array $data
    ): ScheduleException {
        return DB::transaction(function () use (
            $teacher,
            $actor,
            $data
        ) {
            $schedule = TeacherSchedule::query()
                ->whereKey($data['schedule_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $this->validateOwner($schedule, $teacher);
            $this->validateConfirmed($schedule);

            $startAt = CarbonImmutable::parse($data['start_at']);
            $endAt = CarbonImmutable::parse($data['end_at']);

            $this->validateFuture($startAt);
            $this->validateWithinSchedule(
                $schedule,
                $startAt,
                $endAt
            );
            $this->validateThirtyMinuteBoundary(
                $startAt,
                $endAt
            );
            $this->validateNoReservation(
                $schedule,
                $startAt,
                $endAt
            );
            $this->validateNoException(
                $teacher,
                $startAt,
                $endAt
            );

            return ScheduleException::create([
                'teacher_id' => $teacher->id,
                'schedule_id' => $schedule->schedule_id,
                'exception_type_id' => $data['exception_type_id'],
                'start_at' => $startAt,
                'end_at' => $endAt,
                'status' => 'active',
                'reason' => $data['reason'] ?? null,
                'created_by' => $actor->id,
            ]);
        });
    }

    public function cancelForTeacher(
        ScheduleException $exception,
        Teacher $teacher,
        User $actor
    ): ScheduleException {
        return DB::transaction(function () use (
            $exception,
            $teacher,
            $actor
        ) {
            $exception = ScheduleException::query()
                ->whereKey($exception->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $exception->teacher_id !== (int) $teacher->id) {
                abort(403, '他の講師の休日設定は変更できません。');
            }

            if ($exception->status !== 'active') {
                throw ValidationException::withMessages([
                    'exception' => 'この休日設定はすでに取り消されています。',
                ]);
            }

            $exception->update([
                'status' => 'cancelled',
                'cancelled_by' => $actor->id,
                'cancelled_at' => now(),
            ]);

            return $exception->refresh();
        });
    }

    private function validateOwner(
        TeacherSchedule $schedule,
        Teacher $teacher
    ): void {
        if ((int) $schedule->teacher_id !== (int) $teacher->id) {
            abort(403, '他の講師の勤務シフトは変更できません。');
        }
    }

    private function validateConfirmed(
        TeacherSchedule $schedule
    ): void {
        if ($schedule->status !== 'confirmed') {
            throw ValidationException::withMessages([
                'schedule_id' => '確定済みの勤務シフトだけ操作できます。',
            ]);
        }
    }

    private function validateFuture(
        CarbonImmutable $startAt
    ): void {
        if ($startAt->isPast()) {
            throw ValidationException::withMessages([
                'start_at' => '過去の時間には休日を設定できません。',
            ]);
        }
    }

    private function validateWithinSchedule(
        TeacherSchedule $schedule,
        CarbonImmutable $startAt,
        CarbonImmutable $endAt
    ): void {
        $date = $schedule->available_date->format('Y-m-d');

        $scheduleStart = CarbonImmutable::parse(
            $date.' '.$schedule->start_time
        );

        $scheduleEnd = CarbonImmutable::parse(
            $date.' '.$schedule->end_time
        );

        if (
            $startAt->lt($scheduleStart)
            || $endAt->gt($scheduleEnd)
        ) {
            throw ValidationException::withMessages([
                'start_at' => '勤務シフトの範囲内で設定してください。',
            ]);
        }
    }

    private function validateThirtyMinuteBoundary(
        CarbonImmutable $startAt,
        CarbonImmutable $endAt
    ): void {
        if (
            !$this->isThirtyMinuteBoundary($startAt)
            || !$this->isThirtyMinuteBoundary($endAt)
        ) {
            throw ValidationException::withMessages([
                'start_at' => '開始・終了日時は30分単位で指定してください。',
            ]);
        }
    }

    private function isThirtyMinuteBoundary(
        CarbonImmutable $dateTime
    ): bool {
        return in_array($dateTime->minute, [0, 30], true)
            && $dateTime->second === 0;
    }

    private function validateNoReservation(
        TeacherSchedule $schedule,
        CarbonImmutable $startAt,
        CarbonImmutable $endAt
    ): void {
        $hasReservation = $schedule->reservations()
            ->whereHas('status', function ($query) {
                $query->where('status_code', 'reserved');
            })
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->exists();

        if ($hasReservation) {
            throw ValidationException::withMessages([
                'start_at' => '予約済みの時間には休日を設定できません。',
            ]);
        }
    }

    private function validateNoException(
        Teacher $teacher,
        CarbonImmutable $startAt,
        CarbonImmutable $endAt
    ): void {
        $hasException = ScheduleException::query()
            ->where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->exists();

        if ($hasException) {
            throw ValidationException::withMessages([
                'start_at' => '既存の休日・休止時間と重複しています。',
            ]);
        }
    }
}
