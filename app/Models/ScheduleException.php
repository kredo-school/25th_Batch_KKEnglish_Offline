<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleException extends Model
{
    protected $fillable = [
        'teacher_id', 'schedule_id', 'exception_type_id',
        'start_at', 'end_at', 'status', 'reason',
        'created_by', 'cancelled_by', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(TeacherSchedule::class, 'schedule_id', 'schedule_id');
    }

    public function exceptionType(): BelongsTo
    {
        return $this->belongsTo(ExceptionType::class, 'exception_type_id', 'exception_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
