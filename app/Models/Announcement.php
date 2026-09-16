<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;
    // フォームから保存可能なカラムを指定
    protected $fillable = [
        'title',
        'target',
        'content',
    ];
}
