<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherStationAssignment extends Model
{
    protected $fillable = [
        'teacher_id',
        'station_id',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }
    
    /**
     * Teacher
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Station
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
