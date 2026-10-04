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

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(
            TeacherSchedule::class,
            'schedule_id',
            'schedule_id'
        );
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

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(
            PointTransaction::class,
            'related_reservation_id'
        );
    }

    public function review(): HasOne
    {
        return $this->hasOne(
            Review::class,
            'reservation_id'
        );
    }

    public function stationOverride(): HasOne
    {
        return $this->hasOne(LessonStationOverride::class);
    }


    public function displayStation(): ?Station
    {
        // 予約ごとのStation変更があれば最優先
        if ($this->stationOverride?->station) {
            return $this->stationOverride->station;
        }

        // この予約の日付
        $lessonDate = $this->start_at->toDateString();

        // その日に有効なTeacherの通常Stationを取得
        $assignment = $this->teacher
            ->stationAssignments
            ->filter(function ($assignment) use ($lessonDate) {
                return $assignment->start_date->toDateString() <= $lessonDate
                    && (
                        $assignment->end_date === null
                        || $assignment->end_date->toDateString() >= $lessonDate
                    );
            })
            ->sortByDesc('start_date')
            ->first();

        return $assignment?->station;
    }
}
