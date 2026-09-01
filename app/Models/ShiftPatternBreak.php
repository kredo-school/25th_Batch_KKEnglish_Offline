<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftPatternBreak extends Model
{
    protected $fillable = ['shift_pattern_id', 'weekday', 'start_time', 'end_time','reason'];

    public function shiftPattern(): BelongsTo
    {
        return $this->belongsTo(ShiftPattern::class);
    }
}
