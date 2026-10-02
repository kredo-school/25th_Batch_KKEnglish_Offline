<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password = null;

    /**
     * Role CodeからRole IDを取得
     */
    private function roleIdByCode(string $code): int
    {
        $id = Role::query()
            ->where('role_code', $code)
            ->value('id');

        if ($id) {
            return (int) $id;
        }

        return (int) Role::query()->create([
            'role_name' => $code,
            'role_code' => $code,
        ])->id;
    }

    /**
     * Student用プロフィール画像
     */
    private function studentProfileImages(): array
    {
            return [
                'https://i.pravatar.cc/400?img=1',
                'https://i.pravatar.cc/400?img=2',
                'https://i.pravatar.cc/400?img=3',
                'https://i.pravatar.cc/400?img=4',
                'https://i.pravatar.cc/400?img=5',
                'https://i.pravatar.cc/400?img=6',
                'https://i.pravatar.cc/400?img=7',
                'https://i.pravatar.cc/400?img=8',
                'https://i.pravatar.cc/400?img=9',
                'https://i.pravatar.cc/400?img=10',

                'https://i.pravatar.cc/400?img=11',
                'https://i.pravatar.cc/400?img=12',
                'https://i.pravatar.cc/400?img=13',
                'https://i.pravatar.cc/400?img=14',
                'https://i.pravatar.cc/400?img=15',
                'https://i.pravatar.cc/400?img=16',
                'https://i.pravatar.cc/400?img=17',
                'https://i.pravatar.cc/400?img=18',
                'https://i.pravatar.cc/400?img=19',
                'https://i.pravatar.cc/400?img=20',

                'https://i.pravatar.cc/400?img=21',
                'https://i.pravatar.cc/400?img=22',
                'https://i.pravatar.cc/400?img=23',
                'https://i.pravatar.cc/400?img=24',
                'https://i.pravatar.cc/400?img=25',
                'https://i.pravatar.cc/400?img=26',
                'https://i.pravatar.cc/400?img=27',
                'https://i.pravatar.cc/400?img=28',
                'https://i.pravatar.cc/400?img=29',
                'https://i.pravatar.cc/400?img=30',
            ];
        }

    /**
     * Default User
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),

            // デフォルトはstudent
            'role_id' => $this->roleIdByCode('student'),

            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),

            // デフォルトでは画像なし
            'profile_image' => null,

            'nationality' => 'JP',
            'gender' => 'other',
            'status' => 'active',
            'email_verified_at' => now(),

            'password' =>
                static::$password ??= Hash::make('password'),

            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Admin
     */
    public function admin(): static
    {
        return $this->state(fn () => [
            'role_id' => $this->roleIdByCode('admin'),
        ]);
    }

    /**
     * Teacher
     */
    public function teacher(): static
    {
        return $this->state(fn () => [
            'role_id' => $this->roleIdByCode('teacher'),
        ]);
    }

    /**
     * Student
     */
    public function student(): static
    {
        return $this->state(function () {

            $images = $this->studentProfileImages();

            return [
                'role_id' =>
                    $this->roleIdByCode('student'),

                'profile_image' =>
                    fake()->randomElement($images),
            ];
        });
    }
}
