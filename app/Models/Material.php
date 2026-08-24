<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $primaryKey = 'material_id';
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
}
