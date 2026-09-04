<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\TeacherSchedule;
use App\Models\ScheduleException;
use App\Models\Reservation;

class Teacher extends Model
{
    use HasFactory;
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

    public function user(): BelongsTo
    {
        // users.id と teachers.user_id(string) を紐づけ
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TeacherSchedule::class);
    }

    public function scheduleExceptions(): HasMany
    {
        return $this->hasMany(ScheduleException::class);
    }


    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function shiftAssignments(): HasMany
    {
        return $this->hasMany(TeacherShiftAssignment::class);
    }

    public function shiftPatterns(): BelongsToMany
    {
        return $this->belongsToMany(ShiftPattern::class, 'teacher_shift_assignments')
            ->withPivot(['effective_from', 'effective_to', 'priority'])
            ->withTimestamps();
    }
    public function materials()
    {
        return $this->belongsToMany(\App\Models\Material::class,
        'teacher_materials', // pivot table
        'teacher_id',        // pivot の Teacher FK
        'material_id',       // pivot の Material FK
        'id',                // teachers 側PK
        'material_id'        // materials 側PK)
        )->withTimestamps();
    }

}
