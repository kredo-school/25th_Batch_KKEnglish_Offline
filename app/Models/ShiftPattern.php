<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftPattern extends Model
{
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

    public function schedules(): HasMany
    {
        return $this->hasMany(TeacherSchedule::class, 'shift_pattern_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
