<?php

namespace Database\Seeders;

use App\Models\ShiftPattern;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class TeacherScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = Teacher::query()
            ->with('user')
            ->first();

        if (!$teacher || !$teacher->user) {
            throw new RuntimeException(
                '先に講師ユーザーを作成してください。'
            );
        }

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

        $pattern = ShiftPattern::query()
            ->where('pattern_code', 'morning')
            ->first();

        if (!$pattern) {
            throw new RuntimeException(
                '先にShiftPatternSeederを実行してください。'
            );
        }

        foreach ([2, 3, 4] as $daysLater) {
            $availableDate = today()
                ->addDays($daysLater)
                ->toDateString();

            TeacherSchedule::updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'available_date' => $availableDate,
                    'start_time' => $pattern->start_time,
                    'end_time' => $pattern->end_time,
                ],
                [
                    'shift_pattern_id' => $pattern->id,
                    'status' => 'confirmed',
                    'created_by' => $adminUser->id,
                    'confirmed_by' => $adminUser->id,
                    'confirmed_at' => now(),
                    'cancelled_by' => null,
                    'cancelled_at' => null,
                ]
            );
        }
    }
}
