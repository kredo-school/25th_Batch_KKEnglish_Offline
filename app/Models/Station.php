<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function teacherStationAssignments(): HasMany
    {
        return $this->hasMany(TeacherStationAssignment::class);
    }

    public function lessonStationOverrides(): HasMany
    {
        return $this->hasMany(LessonStationOverride::class);
    }
}
