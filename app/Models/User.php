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
        $image = $this->profile_image;

        /*
    |--------------------------------------------------------------------------
    | 画像なし
    |--------------------------------------------------------------------------
    */
        if (blank($image)) {
            return null;
        }


        /*
    |--------------------------------------------------------------------------
    | ① 外部URL
    |--------------------------------------------------------------------------
    |
    | DB例:
    | https://images.unsplash.com/xxxxx.jpg
    |
    */
        if (
            str_starts_with($image, 'http://') ||
            str_starts_with($image, 'https://')
        ) {
            return $image;
        }


        /*
    |--------------------------------------------------------------------------
    | ② Laravel Storage
    |--------------------------------------------------------------------------
    |
    | DB例:
    | storage:teachers/mary.jpg
    |
    | 実ファイル:
    | storage/app/public/teachers/mary.jpg
    |
    */
        if (str_starts_with($image, 'storage:')) {

            $path = substr(
                $image,
                strlen('storage:')
            );

            return Storage::disk('public')->url($path);
        }


        /*
    |--------------------------------------------------------------------------
    | ③ public フォルダ
    |--------------------------------------------------------------------------
    |
    | DB例:
    | images/IMG_4426.jpeg
    |
    | 実ファイル:
    | public/images/IMG_4426.jpeg
    |
    */
        return asset(
            ltrim($image, '/')
        );
    }
}
