<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'nationality',
        'gender',
        'status',
        'phone_number',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

public function getProfileImageUrlAttribute(): ?string
{
    $image = trim((string) $this->profile_image);

    if (blank($image)) {
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | ① 外部URL
    |--------------------------------------------------------------------------
    */
    if (
        str_starts_with($image, 'http://') ||
        str_starts_with($image, 'https://')
    ) {
        return $image;
    }

    /*
    |--------------------------------------------------------------------------
    | ② storage 系
    |--------------------------------------------------------------------------
    */

    if (str_starts_with($image, '/storage/')) {
        $image = substr($image, strlen('/storage/'));
    }

    if (str_starts_with($image, 'storage/')) {
        $image = substr($image, strlen('storage/'));
    }

    // profile_images/... の場合
    if (str_starts_with($image, 'profile_images/')) {
        return asset('storage/' . $image);
    }

    /*
    |--------------------------------------------------------------------------
    | ③ public/images 系
    |--------------------------------------------------------------------------
    |
    | DB例:
    | images/IMG_4426.jpeg
    |
    */
    if (str_starts_with($image, 'images/')) {
        return asset($image);
    }

    /*
    |--------------------------------------------------------------------------
    | ④ ファイル名だけの場合
    |--------------------------------------------------------------------------
    |
    | 生徒画像として profile_images を見る
    |
    */
    return asset(
        'storage/profile_images/' . basename($image)
    );
}
}
