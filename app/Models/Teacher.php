<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'biography',
        'point_consumed',
        'career',
        'graduation_school',
        'certification',
        'about_me',
        'specialty',
        'rating_average',
    ];

    public function user()
    {
        // users.id と teachers.user_id(string) を紐づけ
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
    public function schedules()
    {
        return $this->hasMany(TeacherSchedule::class);
    }
}
