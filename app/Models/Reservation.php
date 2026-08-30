<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Reservation extends Model
{
    protected $fillable = [
        'student_id', 'teacher_id', 'schedule_id', 'material_id',
        'status_id', 'start_at', 'end_at', 'point_cost',
        'cancelled_by', 'cancelled_at', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'point_cost' => 'integer',
            'cancelled_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(TeacherSchedule::class, 'schedule_id', 'schedule_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ReservationStatus::class, 'status_id', 'status_id');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ReservationHistory::class);
    }

    public function lessonRecord(): HasOne
    {
        return $this->hasOne(LessonRecord::class);
    }
}
