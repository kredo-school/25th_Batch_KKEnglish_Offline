<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Material extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'material_id';
    public $incrementing = true;   // UUID等なら false に変更
    protected $keyType = 'int';    // 文字列キーなら 'string'


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
        return $this->belongsToMany(\App\Models\Teacher::class,
        'teacher_materials',
        'material_id', // pivot の Material FK
        'teacher_id',  // pivot の Teacher FK
        'material_id', // materials 側PK
        'id'           // teachers 側PK)
        )->withTimestamps();
    }

}
