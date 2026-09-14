<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    protected $primaryKey = 'type_id';

    public $timestamps = false;

    protected $fillable = [
        'type_code',
        'type_name',
        'description',
    ];
}
