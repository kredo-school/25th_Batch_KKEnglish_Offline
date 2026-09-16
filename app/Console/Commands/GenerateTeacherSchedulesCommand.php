<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Admin\TeacherScheduleGenerationService;

class GenerateTeacherSchedulesCommand extends Command
{
    protected $signature = 'schedule:generate-teacher {created_by : user_id} {--teacher_id=* : Generate only for specific teacher IDs}';
    protected $description = 'Generate teacher_schedules for next 28 days from assignments + patterns + breaks';

    public function handle(TeacherScheduleGenerationService $service): int
    {
        /*
         * コマンドの {created_by} を取得
         *
         * 例：
         * php artisan schedule:generate-teacher 1
         *
         * ↓
         * $createdBy = 1
         */
        $createdBy = (int)$this->argument('created_by');
        /*
         * --teacher_id が指定されていた場合
         *
         * 例：
         * --teacher_id=5
         * --teacher_id=10
         */
        $teacherIds = collect($this->option('teacher_id'))->map(fn($v)=>(int)$v)->filter()->values()->all();
        /*
         * Teacher Scheduleを生成
         */
        $result = $service->regenerateWindow($createdBy, !empty($teacherIds) ? $teacherIds : null);
        /*
         * 結果を表示
         */
        $this->info("対象割当て: {$result['assignments']}件 / 生成: {$result['generated']}件 / スキップ: {$result['skipped']}件");
        return self::SUCCESS;
    }
}
