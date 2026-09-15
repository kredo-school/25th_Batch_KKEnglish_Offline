<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PointTransaction;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'point_balance',
        'birthday',
    ];

    protected function casts(): array
    {
        return [
            'point_balance' => 'integer',
            'birthday' => 'date',
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(
            PointTransaction::class,
            'student_id'
        );
    }

}
