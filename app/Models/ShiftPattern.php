<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShiftPattern extends Model
{
    use HasFactory;
    protected $fillable = [
        'pattern_code', 'pattern_name', 'start_time', 'end_time',
        'end_day_offset', 'slot_minutes', 'is_active',
        'display_order', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'end_day_offset' => 'integer',
            'slot_minutes' => 'integer',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    // slot実体
    public function schedules(): HasMany
    {
        return $this->hasMany(TeacherSchedule::class, 'shift_pattern_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(ShiftPatternRelated::class, 'shift_pattern_id');
    }

    public function breaks(): HasMany
    {
        return $this->hasMany(ShiftPatternBreak::class, 'shift_pattern_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TeacherShiftAssignment::class, 'shift_pattern_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_shift_assignments')
            ->withPivot(['effective_from', 'effective_to', 'priority'])
            ->withTimestamps();
    }
}
