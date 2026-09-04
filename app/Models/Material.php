<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Material extends Model
{
    protected $table = 'materials';
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
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'material_id', 'material_id');
    }

public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_materials')
            ->withTimestamps();
    }

}
