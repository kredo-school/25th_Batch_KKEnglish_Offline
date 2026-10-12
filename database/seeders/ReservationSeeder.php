<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * KKEnglish presentation demo data (2026-10-14).
 *
 * Seed from scratch (migrate:fresh --seed) or add only missing reservations.
 * Existing reservations are preserved and will not be reduced to a target rate.
 * ReservationStatus, Student, Teacher, Materials and TeacherSchedule must exist.
 * Occupancy targets on a FRESH database:
 * - Sep25-Oct13: daily variation between 97% and 100%.
 * - Oct14: 15% reserved for live presentation booking.
 * - Oct15-Oct18: 90% reserved.
 * - Oct19-Oct25: 50% reserved.
 * - Oct26 onward: 0% reserved.
 * Presentation protection:
 * - Kurt John and Kirby Kredo: one reservation each on Oct12 and Oct13,
 *   neither has any seeded reservations on Oct14.
 * - Kurt receives 30+ completed past lessons plus ten upcoming from Oct15.
 * - The ten most recent completed Kurt lessons favor non-Kirby Kredo teachers.
 * - Kirby is protected on Oct12-14 from regular random-fill bookings.
 * Designed for php artisan migrate:fresh --seed; existing reservations are not removed.
 */
class ReservationSeeder extends Seeder
{
    private const FIRST_DATE = '2026-09-25';
    private const LAST_DATE = '2026-11-14';
    private const PRESENTATION_DATE = '2026-10-14';
    private const DEMO_PAST_LESSONS = 5;
    private const KURT_EXTRA_HISTORY = 20;
    private const DEMO_TODAY_LESSONS = 0;
    private const DEMO_LIGHT_DAYS = ['2026-10-12', '2026-10-13'];
    private const DEMO_LIGHT_DAY_LESSONS = 1;
    private const HISTORY_KREDO_TARGET = 10;
    private const UPCOMING_KREDO_TARGET = 10;

    // Fixed demo date instead of a moving relative date; repeatable on Oct 12.
    private const DEMO_SEED_DATE = '2026-10-12';

    public function run(): void
    {
        $this->ensureDemoStudentCapacity(80);
        $students = Student::query()->orderBy('id')->get();
        if ($students->isEmpty()) {
            throw new RuntimeException('Run StudentSeeder before ReservationSeeder.');
        }

        $statuses = ReservationStatus::query()
            ->whereIn('status_code', ['completed', 'absent', 'cancelled', 'confirmed'])
            ->get()
            ->keyBy('status_code');

        foreach (['completed', 'absent', 'cancelled', 'confirmed'] as $code) {
            if (! $statuses->has($code)) {
                throw new RuntimeException("Missing reservation status: {$code}");
            }
        }

        // Look up the demonstration users by full name, not by arbitrary IDs.
        $kurt = Student::query()
            ->whereHas('user', fn ($query) => $query
                ->where('first_name', 'Kurt')
                ->where('last_name', 'John'))
            ->first();

        $kirby = Teacher::query()
            ->whereHas('user', fn ($query) => $query
                ->where('first_name', 'Kirby')
                ->where('last_name', 'Kredo'))
            ->first();

        if (! $kurt || ! $kirby) {
            throw new RuntimeException(
                'Demo users missing: Student "Kurt John" and Teacher "Kirby Kredo" must exist.'
            );
        }

        $schedules = TeacherSchedule::query()
            ->with(['teacher.materials', 'teacher.user'])
            ->where('status', 'confirmed')
            ->whereBetween('available_date', [self::FIRST_DATE, self::LAST_DATE])
            ->orderBy('available_date')
            ->orderBy('teacher_id')
            ->get();

        if ($schedules->isEmpty()) {
            throw new RuntimeException('No confirmed teacher schedules were found.');
        }

        // Candidate slots: 25-minute lessons, on a 30-minute grid.
        // Each slot belongs to one teacher schedule; 5 minutes remain free.
        $slotsByTeacherDay = [];
        foreach ($schedules as $schedule) {
            if (! $schedule->teacher || $schedule->teacher->materials->isEmpty()) {
                continue;
            }

            $date = Carbon::parse($schedule->available_date)->toDateString();
            $startTime = Carbon::parse($schedule->start_time)->format('H:i:s');
            $endTime = Carbon::parse($schedule->end_time)->format('H:i:s');
            $shiftStart = Carbon::parse("{$date} {$startTime}");
            $shiftEnd = Carbon::parse("{$date} {$endTime}");

            if ($shiftEnd->lessThanOrEqualTo($shiftStart)) {
                $shiftEnd->addDay();
            }

            for ($start = $shiftStart->copy();
                 $start->copy()->addMinutes(30)->lessThanOrEqualTo($shiftEnd);
                 $start->addMinutes(30)) {
                $day = $start->toDateString();
                $key = $schedule->teacher_id . ':' . $day;
                $slotsByTeacherDay[$key][] = [
                    'schedule' => $schedule,
                    'start' => $start->copy(),
                    'end' => $start->copy()->addMinutes(25),
                ];
            }
        }

        if (! $slotsByTeacherDay) {
            throw new RuntimeException('No bookable 25-minute slots were generated.');
        }

        // Student double-booking prevention: only times created in this run.
        $studentBookings = [];
        $teacherBookings = [];

        // Load existing reservations so this Seeder can safely be run again.
        // Cancelled bookings do not occupy a slot; other statuses do.
        $existingReservations = Reservation::query()
            ->with('status')
            ->where('start_at', '<', Carbon::parse(self::LAST_DATE)->addDay())
            ->where('end_at', '>', Carbon::parse(self::FIRST_DATE))
            ->get();

        foreach ($existingReservations as $reservation) {
            if ($reservation->status?->status_code === 'cancelled') {
                continue;
            }
            $start = Carbon::parse($reservation->start_at)->timestamp;
            $end = Carbon::parse($reservation->end_at)->timestamp;
            $range = [$start, $end];
            $teacherBookings[$reservation->teacher_id][] = $range;
            $studentBookings[$reservation->student_id][] = $range;
        }

        if ($existingReservations->isNotEmpty()) {
            $this->command?->warn(
                'Existing reservations were preserved: ' . $existingReservations->count()
            );
        }
        $studentList = $students->values()->all();
        $studentCursor = 0;
        $created = 0;
        $demoCreated = Reservation::query()
            ->where('student_id', $kurt->id)
            ->where('teacher_id', $kirby->id)
            ->where('status_id', $statuses['completed']->status_id)
            ->where('end_at', '<', now())
            ->count();
        $now = now();

        // First create 5 actual past lessons between Kurt and Kirby when possible.
        // These are useful on the student learning history / teacher detail page.
        $kirbyPast = [];
        foreach ($slotsByTeacherDay as $group) {
            foreach ($group as $slot) {
                if ($slot['schedule']->teacher_id == $kirby->id
                    && $slot['end']->lessThan($now)
                    && $slot['start']->toDateString() < self::PRESENTATION_DATE
                    && $slot['start']->toDateString() <= '2026-10-01') {
                    $kirbyPast[] = $slot;
                }
            }
        }
        shuffle($kirbyPast);

        foreach ($kirbyPast as $slot) {
            if ($demoCreated >= self::DEMO_PAST_LESSONS) {
                break;
            }
            if (! $this->isSlotFree($slot, $kurt->id, $studentBookings, $teacherBookings)) {
                continue;
            }
            $this->saveReservation($slot, $kurt, 'completed', $statuses);
            $this->markBusy($slot, $kurt->id, $studentBookings, $teacherBookings);
            $demoCreated++;
            $created++;
        }

        if ($demoCreated < self::DEMO_PAST_LESSONS) {
            $this->command?->warn(
                "Kurt John / Kirby Kredo past lessons: {$demoCreated}/" . self::DEMO_PAST_LESSONS
            );
        }

        // HISTORY: Seed the 10 most recent completed lessons for Kurt with
        // non-Kirby teachers whose last name is Kredo. Use dates through Oct 11,
        // so records are already completed when seeding on Oct 12.
        $kredoTeachers = Teacher::query()
            ->whereHas('user', fn ($query) => $query->where('last_name', 'Kredo'))
            ->where('id', '!=', $kirby->id)
            ->pluck('id')
            ->all();

        if (! $kredoTeachers) {
            $this->command?->warn('No other teacher with last_name Kredo; cannot prioritize history/upcoming.');
        }

        $historyCandidates = [];
        foreach ($slotsByTeacherDay as $group) {
            foreach ($group as $slot) {
                if (in_array($slot['schedule']->teacher_id, $kredoTeachers, true)
                    && $slot['end']->lessThanOrEqualTo($now)
                    && $slot['start']->toDateString() <= '2026-10-11') {
                    $historyCandidates[] = $slot;
                }
            }
        }
        usort($historyCandidates, fn ($a, $b) =>
            $b['start']->timestamp <=> $a['start']->timestamp
        );

        // Only count completed lessons in the most recent ten by start time.
        $historyCount = 0;
        foreach ($historyCandidates as $slot) {
            if ($historyCount >= self::HISTORY_KREDO_TARGET) break;
            if (! $this->isSlotFree($slot, $kurt->id, $studentBookings, $teacherBookings)) continue;
            $this->saveReservation($slot, $kurt, 'completed', $statuses);
            $this->markBusy($slot, $kurt->id, $studentBookings, $teacherBookings);
            $historyCount++;
            $created++;
        }
        $this->command?->info("Kurt non-Kirby Kredo history generated: {$historyCount}/10");
        if ($historyCount < self::HISTORY_KREDO_TARGET) {
            $this->command?->warn('Fewer than 10 non-Kirby Kredo historical slots available.');
        }

        // Extra history for Kurt: 20 more completed lessons on or before Oct 4.
        // Keeping these older than the recent Kredo lessons preserves the
        // preferred non-Kirby Kredo teachers in Learning History top 10.
        $extraHistoryCandidates = [];
        foreach ($slotsByTeacherDay as $group) {
            foreach ($group as $slot) {
                if ($slot['start']->toDateString() <= '2026-10-04'
                    && $slot['end']->lessThanOrEqualTo($now)
                    && $slot['schedule']->teacher_id != $kirby->id) {
                    $extraHistoryCandidates[] = $slot;
                }
            }
        }
        shuffle($extraHistoryCandidates);
        $extraHistoryCount = 0;
        foreach ($extraHistoryCandidates as $slot) {
            if ($extraHistoryCount >= self::KURT_EXTRA_HISTORY) break;
            if (! $this->isSlotFree($slot, $kurt->id, $studentBookings, $teacherBookings)) continue;
            $this->saveReservation($slot, $kurt, 'completed', $statuses);
            $this->markBusy($slot, $kurt->id, $studentBookings, $teacherBookings);
            $extraHistoryCount++;
            $created++;
        }
        $this->command?->info("Kurt extra completed history: {$extraHistoryCount}/" . self::KURT_EXTRA_HISTORY);
        if ($extraHistoryCount < self::KURT_EXTRA_HISTORY) {
            $this->command?->warn('Not enough eligible shifts for all 20 extra Kurt past lessons.');
        }

        // October 12 and 13: ONE lesson for Kurt and ONE for Kirby each day.
        // Prefer other Kredo teachers for Kurt and reserve Kirby for
        // other students, leaving both students with reviewable lessons.
        foreach (self::DEMO_LIGHT_DAYS as $lightDay) {
            $dayStart = Carbon::parse($lightDay)->startOfDay();
            $dayEnd = $dayStart->copy()->addDay();
            $activeStatusIds = [
                $statuses['confirmed']->status_id,
                $statuses['completed']->status_id,
                $statuses['absent']->status_id,
            ];

            $kurtCount = Reservation::query()
                ->where('student_id', $kurt->id)
                ->whereIn('status_id', $activeStatusIds)
                ->where('start_at', '>=', $dayStart)
                ->where('start_at', '<', $dayEnd)
                ->count();

            $kirbyCount = Reservation::query()
                ->where('teacher_id', $kirby->id)
                ->whereIn('status_id', $activeStatusIds)
                ->where('start_at', '>=', $dayStart)
                ->where('start_at', '<', $dayEnd)
                ->count();

            $candidates = [];
            foreach ($slotsByTeacherDay as $group) {
                foreach ($group as $slot) {
                    if ($slot['start']->toDateString() === $lightDay) {
                        $candidates[] = $slot;
                    }
                }
            }
            usort($candidates, fn ($a, $b) =>
                $a['start']->timestamp <=> $b['start']->timestamp
            );

            $make = function (array $slot, Student $student) use (
                $statuses, $now, &$created, &$studentBookings, &$teacherBookings
            ): void {
                $status = $slot['end']->lessThanOrEqualTo($now)
                    ? 'completed' : 'confirmed';
                $this->saveReservation($slot, $student, $status, $statuses);
                $this->markBusy($slot, $student->id, $studentBookings, $teacherBookings);
                $created++;
            };

            // Complete Kirby's quota with other students where possible.
            foreach ($candidates as $slot) {
                if ($kirbyCount >= self::DEMO_LIGHT_DAY_LESSONS) break;
                if ($slot['schedule']->teacher_id != $kirby->id) continue;
                foreach ($studentList as $student) {
                    if ($student->id == $kurt->id) continue;
                    if (! $this->isSlotFree($slot, $student->id, $studentBookings, $teacherBookings)) continue;
                    $make($slot, $student);
                    $kirbyCount++;
                    break;
                }
            }

            // Complete Kurt's quota with Kredo teachers other than Kirby first.
            usort($candidates, function ($a, $b) use ($kredoTeachers) {
                $aPreferred = in_array($a['schedule']->teacher_id, $kredoTeachers, true) ? 0 : 1;
                $bPreferred = in_array($b['schedule']->teacher_id, $kredoTeachers, true) ? 0 : 1;
                return ($aPreferred <=> $bPreferred)
                    ?: ($a['start']->timestamp <=> $b['start']->timestamp);
            });
            foreach ($candidates as $slot) {
                if ($kurtCount >= self::DEMO_LIGHT_DAY_LESSONS) break;
                if ($slot['schedule']->teacher_id == $kirby->id
                    || ! $this->isSlotFree($slot, $kurt->id, $studentBookings, $teacherBookings)) {
                    continue;
                }
                $make($slot, $kurt);
                $kurtCount++;
            }

            $this->command?->info(
                "{$lightDay}: Kurt John {$kurtCount}/1; Kirby Kredo {$kirbyCount}/1"
            );
            if ($kurtCount !== 1 || $kirbyCount !== 1) {
                $this->command?->warn(
                    "{$lightDay}: Check existing reservations, teacher shifts and teaching materials."
                );
            }
        }

        // Oct 14: intentionally create ZERO reservations for both Kurt and Kirby.
        // This keeps the presentation-day demo schedule open.
        $todayCount = 0;
        $presentationStart = Carbon::parse(self::PRESENTATION_DATE)->startOfDay();
        $presentationEnd = $presentationStart->copy()->addDay();
        foreach ([$kurt->id] as $studentId) {
            $unexpected = Reservation::query()
                ->where('student_id', $studentId)
                ->where('start_at', '>=', $presentationStart)
                ->where('start_at', '<', $presentationEnd)
                ->count();
            if ($unexpected) {
                $this->command?->warn("Kurt already has {$unexpected} Oct14 reservation(s). Use migrate:fresh --seed for zero.");
            }
        }

        // UPCOMING: create 10 confirmed lessons between Oct15 and Oct25.
        // Oct14 and Oct26 onward stay empty for Kurt.
        // Do not book Kirby's Oct 14 slots; preserve live reservation demo.
        $presentationStart = Carbon::parse(self::PRESENTATION_DATE)->startOfDay();
        $futureExisting = Reservation::query()
            ->where('student_id', $kurt->id)
            ->where('status_id', $statuses['confirmed']->status_id)
            ->where('start_at', '>', Carbon::parse('2026-10-14 23:59:59'))
            ->where('start_at', '<', Carbon::parse('2026-10-26'))
            ->count();
        $futureCandidates = [];
        foreach ($slotsByTeacherDay as $group) {
            foreach ($group as $slot) {
                if ($slot['start']->greaterThan($now)
                    && $slot['start']->greaterThanOrEqualTo($presentationStart)
                    && $slot['start']->toDateString() > self::PRESENTATION_DATE
                    && $slot['start']->toDateString() < '2026-10-26'
                    && $slot['schedule']->teacher_id != $kirby->id) {
                    $futureCandidates[] = $slot;
                }
            }
        }
        usort($futureCandidates, function ($a, $b) use ($kredoTeachers) {
            $aPref = in_array($a['schedule']->teacher_id, $kredoTeachers, true) ? 0 : 1;
            $bPref = in_array($b['schedule']->teacher_id, $kredoTeachers, true) ? 0 : 1;
            return ($aPref <=> $bPref)
                ?: ($a['start']->timestamp <=> $b['start']->timestamp);
        });
        $futurePerDay = [];
        $futureTeacherPerDay = [];
        foreach ($futureCandidates as $slot) {
            if ($futureExisting >= self::UPCOMING_KREDO_TARGET) break;
            $date = $slot['start']->toDateString();
            $teacherId = $slot['schedule']->teacher_id;
            // Spread Kurt's ten upcoming lessons over multiple days.
            if (($futurePerDay[$date] ?? 0) >= 2) continue;
            if (isset($futureTeacherPerDay[$date][$teacherId])) continue;
            if (! $this->isSlotFree($slot, $kurt->id, $studentBookings, $teacherBookings)) continue;
            $this->saveReservation($slot, $kurt, 'confirmed', $statuses);
            $this->markBusy($slot, $kurt->id, $studentBookings, $teacherBookings);
            $futurePerDay[$date] = ($futurePerDay[$date] ?? 0) + 1;
            $futureTeacherPerDay[$date][$teacherId] = true;
            $futureExisting++;
            $created++;
        }
        $this->command?->info("Kurt confirmed lessons from Oct15-Oct25: {$futureExisting}/10");
        if ($futureExisting < self::UPCOMING_KREDO_TARGET) {
            $this->command?->warn('Not enough free non-Kirby Kredo slots for 10 upcoming lessons.');
        }

        // Daily targets use ACTUAL bookable teacher slots rather than a global
        // reservation count. Fill all times chronologically so one student
        // can attend sequential lessons without overlapping bookings.
        $slotsByDay = [];
        foreach ($slotsByTeacherDay as $group) {
            foreach ($group as $slot) {
                $slotsByDay[$slot['start']->toDateString()][] = $slot;
            }
        }
        ksort($slotsByDay);

        foreach ($slotsByDay as $day => $daySlots) {
            $totalSlots = count($daySlots);
            if ($day >= '2026-10-26') {
                $this->command?->info("{$day}: 0 / {$totalSlots} target=0% (no new reservations)");
                continue;
            }

            // Global daily target: e.g. 480 x 97% = 466 slots, rather
            // than rounding each 16-slot teacher independently to 100%.
            $desired = (int) round($totalSlots * $this->occupancyRate($day));
            $filled = 0;
            $candidates = [];
            foreach ($daySlots as $slot) {
                if ($this->teacherIsBusy($slot, $teacherBookings)) {
                    $filled++;
                    continue;
                }

                $isKirbyProtected = $slot['schedule']->teacher_id == $kirby->id
                    && ($day === self::PRESENTATION_DATE
                        || in_array($day, self::DEMO_LIGHT_DAYS, true));
                if ($isKirbyProtected) continue;
                $candidates[] = $slot;
            }

            // Randomize teacher ordering within each time slot; avoid
            // always favouring the lowest teacher ID.
            shuffle($candidates);
            usort($candidates, fn ($a, $b) =>
                $a['start']->timestamp <=> $b['start']->timestamp
            );

            $studentCount = count($studentList);
            foreach ($candidates as $slot) {
                if ($filled >= $desired) break;
                if ($slot['start']->lessThanOrEqualTo($now)
                    && $slot['end']->greaterThan($now)) continue;
                if ($this->teacherIsBusy($slot, $teacherBookings)) continue;

                $chosen = null;
                for ($i = 0; $i < $studentCount; $i++) {
                    $candidate = $studentList[($studentCursor + $i) % $studentCount];
                    // Kurt's special lessons are created before normal fill.
                    if ($candidate->id == $kurt->id) continue;
                    if (! $this->isSlotFree($slot, $candidate->id, $studentBookings, $teacherBookings)) continue;
                    $chosen = $candidate;
                    $studentCursor = ($studentCursor + $i + 1) % $studentCount;
                    break;
                }
                if (! $chosen) continue;

                $status = $slot['end']->lessThan($now)
                    ? $this->pastStatus() : 'confirmed';
                $this->saveReservation($slot, $chosen, $status, $statuses);
                $this->markBusy($slot, $chosen->id, $studentBookings, $teacherBookings);
                $filled++;
                $created++;
            }

            $percent = $totalSlots ? round(100 * $filled / $totalSlots, 1) : 0;
            $targetPercent = round(100 * $this->occupancyRate($day), 1);
            $this->command?->info("{$day}: booked={$filled}/{$totalSlots} ({$percent}%) target={$targetPercent}%");
            if ($filled < $desired - 2) {
                $this->command?->warn(
                    "{$day}: SHORTFALL ".($desired - $filled).
                    ' bookings. Check schedule/material eligibility, excluded demo slots and student conflicts.'
                );
            }
        }

        $this->command?->info("Reservations seeded: {$created}");
        $this->command?->info("Kurt John + Kirby Kredo completed lessons: {$demoCreated}");
        $this->command?->info('Kurt John presentation day Oct14: zero new bookings.');
        $this->command?->info('Kirby Kredo Oct14: zero NEW bookings (existing reservations, if any, remain).');
        $this->command?->info('Targets: Sep25-Oct13 vary 97-100%; Oct14 15%; Oct15-18 90%; Oct19-25 50%; Oct26+ 0%.');
        $this->command?->info('Occupancy is a target per teacher/day, subject to student conflicts.');
    }

    /**
     * Presentation-only test students: 30 teachers may require 30 or more
     * concurrent students. A small StudentSeeder can otherwise leave hundreds
     * of available teacher slots empty. Existing student users are preserved.
     */
    private function ensureDemoStudentCapacity(int $minimum): void
    {
        $existing = Student::query()->count();
        if ($existing >= $minimum) return;

        $needed = $minimum - $existing;
        for ($n = 1; $n <= $needed; $n++) {
            $email = sprintf('kkenglish.demo.fill.%03d@example.test', $n);
            $user = User::query()->where('email', $email)->first();
            if (! $user) {
                $user = User::factory()->student()->create([
                    'first_name' => 'Demo',
                    'last_name' => sprintf('Student%03d', $n),
                    'email' => $email,
                ]);
            }
            if (! Student::query()->where('user_id', $user->id)->exists()) {
                Student::factory()->create([
                    'user_id' => $user->id,
                    'point_balance' => 3000,
                ]);
            }
        }
        $this->command?->info(
            'Student pool expanded from '.$existing.' to '.Student::query()->count().' for dense daily booking.'
        );
    }

    private function occupancyRate(string $date): float
    {
        if ($date <= '2026-10-13') {
            // Vary by calendar day but keep the same result on repeated runs.
            // Each day is 97%, 98%, 99% or 100% (not random per teacher).
            $dailyRates = [0.97, 1.00, 0.98, 0.99, 1.00, 0.97, 0.99, 0.98];
            $dayIndex = Carbon::parse(self::FIRST_DATE)->diffInDays(Carbon::parse($date));
            return $dailyRates[(int) $dayIndex % count($dailyRates)];
        }
        if ($date === self::PRESENTATION_DATE) return 0.15;
        if ($date <= '2026-10-18') return 0.90;
        if ($date <= '2026-10-25') return 0.50;
        return 0.0;
    }

    private function pastStatus(): string
    {
        $draw = random_int(1, 100);
        // Cancelled lessons are visible in history but do not occupy a
        // teacher's active booking slot. Use completed/absent for the 97%
        // target, so the actual occupied percentage is not reduced.
        return $draw <= 92 ? 'completed' : 'absent';
    }

    private function teacherIsBusy(array $slot, array $teacherBookings): bool
    {
        $id = $slot['schedule']->teacher_id;
        foreach ($teacherBookings[$id] ?? [] as [$start, $end]) {
            if ($start < $slot['end']->timestamp && $end > $slot['start']->timestamp) {
                return true;
            }
        }
        return false;
    }

    private function isSlotFree(array $slot, int $studentId, array $studentBookings, array $teacherBookings): bool
    {
        if ($this->teacherIsBusy($slot, $teacherBookings)) return false;
        foreach ($studentBookings[$studentId] ?? [] as [$start, $end]) {
            if ($start < $slot['end']->timestamp && $end > $slot['start']->timestamp) {
                return false;
            }
        }
        return true;
    }

    private function markBusy(array $slot, int $studentId, array &$studentBookings, array &$teacherBookings): void
    {
        $range = [$slot['start']->timestamp, $slot['end']->timestamp];
        $studentBookings[$studentId][] = $range;
        $teacherBookings[$slot['schedule']->teacher_id][] = $range;
    }

    private function saveReservation(array $slot, Student $student, string $code, $statuses): void
    {
        $schedule = $slot['schedule'];
        $material = $schedule->teacher->materials->random();
        $cancelled = $code === 'cancelled';

        Reservation::create([
            'student_id' => $student->id,
            'teacher_id' => $schedule->teacher_id,
            'schedule_id' => $schedule->schedule_id,
            'material_id' => $material->material_id,
            'status_id' => $statuses[$code]->status_id,
            'start_at' => $slot['start'],
            'end_at' => $slot['end'],
            'point_cost' => $schedule->teacher->point_consumed ?? 100,
            'cancelled_by' => $cancelled ? $student->user_id : null,
            'cancelled_at' => $cancelled ? $slot['start']->copy()->subDay() : null,
            'cancellation_reason' => $cancelled ? 'Cancelled by student (Seeder data)' : null,
        ]);
    }
}
