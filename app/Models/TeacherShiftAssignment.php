<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherShiftAssignment extends Model
{
    protected $fillable = [
        'teacher_id',
        'shift_pattern_id',
        'effective_from',
        'effective_to',
        'priority',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'priority' => 'integer',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function shiftPattern(): BelongsTo
    {
        return $this->belongsTo(ShiftPattern::class);
    }
}
