<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionType extends Model
{
    protected $table = 'transaction_types';
    protected $primaryKey = 'type_id';

    public $timestamps = false;

    protected $fillable = [
        'type_code',
        'type_name',
        'description',
    ];

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'transaction_type', 'type_id');
    }
}
