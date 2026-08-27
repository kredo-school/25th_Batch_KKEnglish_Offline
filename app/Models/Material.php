<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'material_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'cover_image',
        'description',
        'level',
        'target_level',
        'duration',
        'printed_textbook',
        'status',
    ];
    public function getRouteKeyName(): string
    {
        return 'material_id';
    }
}
