<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftPatternRelated extends Model
{
    use HasFactory;

    protected $table = 'shift_pattern_related_tables';

    protected $fillable = ['shift_pattern_id', 'weekday', 'start_time', 'end_time', 'lesson_type'];

    public function shiftPattern(): BelongsTo
    {
        return $this->belongsTo(ShiftPattern::class);
    }
}
