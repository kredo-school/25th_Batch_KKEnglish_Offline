<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\PointTransaction;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'point_balance',
        'birthday',
    ];

    protected $casts = [
        'point_balance' => 'integer',
        'birthday' => 'date',
    ];

    /**
     * Student → User
     *
     * この生徒のログインアカウント情報
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    /**
     * Student → PointTransaction
     *
     * この生徒のポイント取引履歴
     */
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(
            PointTransaction::class,
            'student_id',
            'id'
        );
    }
}