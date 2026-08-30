<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherSchedule extends Model
{
    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'teacher_id', 'shift_pattern_id', 'available_date',
        'start_time', 'end_time', 'status', 'created_by',
        'confirmed_by', 'confirmed_at', 'cancelled_by', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'available_date' => 'date',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function shiftPattern(): BelongsTo
    {
        return $this->belongsTo(ShiftPattern::class);
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ScheduleException::class, 'schedule_id', 'schedule_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'schedule_id', 'schedule_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
