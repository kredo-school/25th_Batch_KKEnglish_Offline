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
    public function run(): void
    {
        // ========================================
        // 管理者を取得
        // ========================================
        $adminUser = User::query()
            ->whereHas('role', function ($query) {
                $query->where('role_code', 'admin');
            })
            ->first();

        if (!$adminUser) {
            throw new RuntimeException(
                '先に管理者ユーザーを作成してください。'
            );
        }

        // ========================================
        // Teacher ID 1～10 を取得
        // ========================================
        $teachers = Teacher::query()
            ->whereBetween('id', [1, 10])
            ->orderBy('id')
            ->get();

        if ($teachers->isEmpty()) {
            throw new RuntimeException(
                'Teacher ID 1～10の講師を作成してください。'
            );
        }

        // ========================================
        // ShiftPattern ID 1～3 を取得
        // ========================================
        $patterns = ShiftPattern::query()
            ->whereIn('id', [1, 2, 3])
            ->orderBy('id')
            ->get();

        if ($patterns->count() < 3) {
            throw new RuntimeException(
                'ShiftPattern ID 1～3を先に作成してください。'
            );
        }

        // ========================================
        // 期間
        // 2026-09-04 ～ 2026-10-15
        // ========================================
        $startDate = Carbon::create(
            2026,
            9,
            4
        )->startOfDay();

        $endDate = Carbon::create(
            2026,
            10,
            15
        )->startOfDay();

        $date = $startDate->copy();

        // ========================================
        // 毎日の勤務シフトを作成
        // ========================================
        while ($date->lte($endDate)) {

            foreach ($teachers as $teacher) {

                // Pattern 1～3を順番に分散
                $patternIndex = (
                    $teacher->id
                    + $date->dayOfYear
                ) % $patterns->count();

                $pattern = $patterns[$patternIndex];

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
            }

            $date->addDay();
        }
    }
}
