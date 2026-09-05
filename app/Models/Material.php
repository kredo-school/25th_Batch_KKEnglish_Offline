<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Teacher;
use App\Models\Reservation;
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
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'material_id', 'material_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            Teacher::class,
            'teacher_materials',
            'material_id',
            'teacher_id',
            'material_id',
            'id'
        )->withTimestamps();
    }

}
