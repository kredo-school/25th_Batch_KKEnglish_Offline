<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationHistory extends Model
{
    protected $fillable = [
        'reservation_id', 'from_status_id', 'to_status_id',
        'changed_by', 'reason',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(ReservationStatus::class, 'from_status_id', 'status_id');
    }

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(ReservationStatus::class, 'to_status_id', 'status_id');
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
