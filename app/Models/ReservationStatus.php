<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReservationStatus extends Model
{
    protected $primaryKey = 'status_id';
    public $timestamps = false;

    protected $fillable = ['status_code', 'status_name', 'description'];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'status_id', 'status_id');
    }

    public function historiesFrom(): HasMany
    {
        return $this->hasMany(ReservationHistory::class, 'from_status_id', 'status_id');
    }

    public function historiesTo(): HasMany
    {
        return $this->hasMany(ReservationHistory::class, 'to_status_id', 'status_id');
    }
}
