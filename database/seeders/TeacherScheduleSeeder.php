<?php

namespace Database\Seeders;

use App\Models\ShiftPattern;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use RuntimeException;

class TeacherScheduleSeeder extends Seeder
{
    private const START_DATE = '2026-09-25';
    private const END_DATE = '2026-11-14';
    private const PRESENTATION_DATE = '2026-10-14';

    public function run(): void
    {
        $adminUser = User::query()
            ->whereHas('role', fn ($query) => $query->where('role_code', 'admin'))
            ->first();

        if (! $adminUser) {
            throw new RuntimeException('先に管理者ユーザーを作成してください。');
        }

        $teachers = Teacher::query()
            ->with('user')
            ->orderBy('id')
            ->limit(30)
            ->get();

        if ($teachers->count() < 30) {
            throw new RuntimeException("Teacherが30人必要です。現在: {$teachers->count()}人");
        }

        $kirby = Teacher::query()
            ->whereHas('user', fn ($query) => $query
                ->where('first_name', 'Kirby')
                ->where('last_name', 'Kredo'))
            ->first();

        if (! $kirby) {
            throw new RuntimeException('デモ用講師 Kirby Kredo が見つかりません。');
        }

        if (! $teachers->contains('id', $kirby->id)) {
            throw new RuntimeException('Kirby Kredo が最初の30人のTeacherに含まれていません。');
        }

        $patterns = ShiftPattern::query()
            ->whereIn('id', [1, 2, 3])
            ->orderBy('id')
            ->get();

        if ($patterns->count() !== 3) {
            throw new RuntimeException('ShiftPattern ID 1～3を先に作成してください。');
        }

        $startDate = Carbon::parse(self::START_DATE)->startOfDay();
        $endDate = Carbon::parse(self::END_DATE)->startOfDay();
        $createdOrUpdated = 0;

        foreach ($teachers as $index => $teacher) {
            // 30 teachers / 3 patterns = 10 teachers per pattern.
            $pattern = $patterns[$index % $patterns->count()];

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                TeacherSchedule::updateOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'available_date' => $date->toDateString(),
                    ],
                    [
                        'shift_pattern_id' => $pattern->id,
                        'start_time' => $pattern->start_time,
                        'end_time' => $pattern->end_time,
                        'status' => 'confirmed',
                        'created_by' => $adminUser->id,
                        'confirmed_by' => $adminUser->id,
                        'confirmed_at' => now(),
                        'cancelled_by' => null,
                        'cancelled_at' => null,
                    ]
                );
                $createdOrUpdated++;
            }

            $this->command?->info(
                "Teacher {$teacher->id}: Shift Pattern {$pattern->id}"
            );
        }

        $kirbyPresentationSchedule = TeacherSchedule::query()
            ->where('teacher_id', $kirby->id)
            ->whereDate('available_date', self::PRESENTATION_DATE)
            ->where('status', 'confirmed')
            ->first();

        if (! $kirbyPresentationSchedule) {
            throw new RuntimeException('Kirby Kredo の発表当日の勤務スケジュールが生成されませんでした。');
        }

        $this->command?->info("Teacher schedules created/updated: {$createdOrUpdated}");
        $this->command?->info('Kirby Kredo: confirmed schedule exists on ' . self::PRESENTATION_DATE);
        $this->command?->warn('予約枠の確保はReservationSeeder側で実施してください。');
    }
}
