<?php

namespace Database\Seeders;

use App\Models\ExceptionType;
use App\Models\ScheduleException;
use App\Models\TeacherSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use RuntimeException;

class ScheduleExceptionSeeder extends Seeder
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
                '管理者ユーザーが必要です。'
            );
        }

        // ========================================
        // ExceptionTypeを取得
        // ========================================
        $exceptionType = ExceptionType::query()
            ->first();

        if (!$exceptionType) {
            throw new RuntimeException(
                '先にExceptionTypeSeederを実行してください。'
            );
        }

        // ========================================
        // テスト用Exception
        // ========================================
        $testCases = [
            [
                'teacher_id' => 1,
                'date' => '2026-09-05',
                'offset' => 60,
                'duration' => 60,
                'reason' => 'Seeder test exception',
            ],
            [
                'teacher_id' => 2,
                'date' => '2026-09-08',
                'offset' => 30,
                'duration' => 30,
                'reason' => 'Seeder test exception',
            ],
            [
                'teacher_id' => 3,
                'date' => '2026-09-10',
                'offset' => 90,
                'duration' => 60,
                'reason' => 'Seeder test exception',
            ],
            [
                'teacher_id' => 5,
                'date' => '2026-09-15',
                'offset' => 120,
                'duration' => 90,
                'reason' => 'Seeder test exception',
            ],
            [
                'teacher_id' => 8,
                'date' => '2026-10-01',
                'offset' => 60,
                'duration' => 120,
                'reason' => 'Seeder test exception',
            ],
        ];

        foreach ($testCases as $case) {

            // 対象TeacherScheduleを取得
            $schedule = TeacherSchedule::query()
                ->where('teacher_id', $case['teacher_id'])
                ->whereDate('available_date', $case['date'])
                ->where('status', 'confirmed')
                ->first();

            // Scheduleが存在しない場合はスキップ
            if (!$schedule) {
                continue;
            }

            // シフト開始日時
            $scheduleStart = Carbon::parse(
                $schedule->available_date->format('Y-m-d')
                . ' '
                . $schedule->start_time
            );

            // シフト開始から指定時間後にException開始
            $startAt = $scheduleStart
                ->copy()
                ->addMinutes($case['offset']);

            // Exception終了
            $endAt = $startAt
                ->copy()
                ->addMinutes($case['duration']);

            ScheduleException::updateOrCreate(
                [
                    'teacher_id' => $case['teacher_id'],
                    'schedule_id' => $schedule->schedule_id,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                ],
                [
                    'exception_type_id' => $exceptionType->exception_type_id,
                    'status' => 'active',
                    'reason' => $case['reason'],
                    'created_by' => $adminUser->id,
                    'cancelled_by' => null,
                    'cancelled_at' => null,
                ]
            );
        }
    }
}
