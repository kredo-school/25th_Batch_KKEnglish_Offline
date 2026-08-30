<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExceptionType extends Model
{
    protected $primaryKey = 'exception_type_id';
    public $timestamps = false;

    protected $fillable = ['type_code', 'type_name', 'description'];

    public function exceptions(): HasMany
    {
        return $this->hasMany(
            ScheduleException::class,
            'exception_type_id',
            'exception_type_id'
        );
    }
}
