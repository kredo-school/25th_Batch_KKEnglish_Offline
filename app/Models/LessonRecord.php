<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonRecord extends Model
{
    protected $fillable = [
        'reservation_id',
        'lesson_date',
        'subject',
        'progress_note',
        'completed_by',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'lesson_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(
            Reservation::class,
            'reservation_id'
        );
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'completed_by'
        );
    }
}
