<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftPatternRule extends Model
{
    protected $fillable = ['shift_pattern_id', 'weekday', 'start_time', 'end_time', 'lesson_type'];

    public function shiftPattern(): BelongsTo
    {
        return $this->belongsTo(ShiftPattern::class);
    }
}
