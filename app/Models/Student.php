<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PointTransaction;
use App\Models\TeacherLike;

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

    public function teacherLikes()
    {
        return $this->hasMany(
            TeacherLike::class,
            'student_id'
        );
    }

}
