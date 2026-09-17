<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpectedReservationSetting extends Model
{
    protected $fillable = [
        'season_type',
        'day_type',
        'target_time',
        'expected_count',
    ];
}
