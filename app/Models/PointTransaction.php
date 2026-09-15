<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $primaryKey = 'transaction_id';

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'transaction_type',
        'point',
        'related_reservation_id',
        'note',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'point' => 'integer',
        'created_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function transactionType()
    {
        return $this->belongsTo(
            TransactionType::class,
            'transaction_type',
            'type_id'
        );
    }

    public function reservation()
    {
        return $this->belongsTo(
            Reservation::class,
            'related_reservation_id'
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
