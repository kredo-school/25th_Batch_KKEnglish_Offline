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
        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Teachers
        |--------------------------------------------------------------------------
        |
        | 先頭30人のTeacherを取得
        |
        */

        $teachers = Teacher::query()
            ->orderBy('id')
            ->limit(30)
            ->get();

        if ($teachers->count() < 30) {
            throw new RuntimeException(
                "Teacherが30人必要です。現在: {$teachers->count()}人"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Shift Patterns
        |--------------------------------------------------------------------------
        |
        | Pattern 1～3を使用
        |
        */

        $patterns = ShiftPattern::query()
            ->whereIn('id', [1, 2, 3])
            ->orderBy('id')
            ->get();

        if ($patterns->count() < 3) {
            throw new RuntimeException(
                'ShiftPattern ID 1～3を先に作成してください。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Schedule Period
        |--------------------------------------------------------------------------
        |
        | 2026-09-04 ～ 2026-10-15
        |
        */

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

        /*
        |--------------------------------------------------------------------------
        | Teacher Schedules
        |--------------------------------------------------------------------------
        |
        | Teacherごとに勤務シフトを固定
        |
        | 1人目  → Pattern 1
        | 2人目  → Pattern 2
        | 3人目  → Pattern 3
        | 4人目  → Pattern 1
        | ...
        | 30人目 → Pattern 3
        |
        | 30人なので
        |
        | Pattern 1 → 10人
        | Pattern 2 → 10人
        | Pattern 3 → 10人
        |
        */

        foreach ($teachers as $index => $teacher) {

            /*
            |--------------------------------------------------------------------------
            | Fixed Shift Pattern
            |--------------------------------------------------------------------------
            |
            | $index
            | 0 → Pattern 1
            | 1 → Pattern 2
            | 2 → Pattern 3
            | 3 → Pattern 1
            | ...
            |
            */

            $patternIndex =
                $index % $patterns->count();

            $pattern =
                $patterns[$patternIndex];

            /*
            |--------------------------------------------------------------------------
            | Date
            |--------------------------------------------------------------------------
            */

            $date = $startDate->copy();

            while ($date->lte($endDate)) {

                TeacherSchedule::updateOrCreate(
                    [
                        'teacher_id' =>
                            $teacher->id,

                        'available_date' =>
                            $date->toDateString(),
                    ],
                    [
                        'shift_pattern_id' =>
                            $pattern->id,

                        'start_time' =>
                            $pattern->start_time,

                        'end_time' =>
                            $pattern->end_time,

                        'status' =>
                            'confirmed',

                        'created_by' =>
                            $adminUser->id,

                        'confirmed_by' =>
                            $adminUser->id,

                        'confirmed_at' =>
                            now(),

                        'cancelled_by' =>
                            null,

                        'cancelled_at' =>
                            null,
                    ]
                );

                $date->addDay();
            }

            /*
            |--------------------------------------------------------------------------
            | Result
            |--------------------------------------------------------------------------
            */

            $this->command?->info(
                "Teacher {$teacher->id}: "
                . "Shift Pattern {$pattern->id}"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Final Result
        |--------------------------------------------------------------------------
        */

        $this->command?->info(
            'Teacher schedules created for 30 teachers.'
        );
    }
}
