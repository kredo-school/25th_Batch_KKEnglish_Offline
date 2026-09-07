<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherShiftPatternAssignment extends Model
{
    use HasFactory;

    protected $table = 'teacher_shift_pattern_assignments';

    protected $fillable = [
        'teacher_id',
        'shift_pattern_id',
        'weekday',
        'start_date',
        'end_date',
        'priority',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
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
