<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeasonPeriod extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'season_type',
    ];
    
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }
}
