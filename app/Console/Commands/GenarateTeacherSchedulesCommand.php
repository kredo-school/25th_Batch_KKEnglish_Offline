<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Admin\TeacherScheduleGenerationService;

class GenerateTeacherSchedulesCommand extends Command
{
    protected $signature = 'schedule:generate-teacher {created_by : users.user_id} {--teacher_id=*}';
    protected $description = 'Generate teacher_schedules for next 28 days from assignments + patterns + breaks';

    public function handle(TeacherScheduleGenerationService $service): int
    {
        $createdBy = (int)$this->argument('created_by');
        $ids = collect($this->option('teacher_id'))->map(fn($v)=>(int)$v)->filter()->values()->all();
        $service->regenerateNext28Days($createdBy$ids ?: null);
        $this->info('teacher_schedules generated.');
        return self::SUCCESS;
    }
}
