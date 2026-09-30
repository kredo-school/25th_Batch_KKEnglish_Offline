<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password = null;

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

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),

            // デフォルトはstudent
            'role_id' => $this->roleIdByCode('student'),

            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'profile_image' => null,
            'nationality' => 'JP',
            'gender' => 'other',
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role_id' => $this->roleIdByCode('admin'),
        ]);
    }

    public function teacher(): static
    {
        return $this->state(fn () => [
            'role_id' => $this->roleIdByCode('teacher'),
        ]);
    }

    public function student(): static
    {
        return $this->state(fn () => [
            'role_id' => $this->roleIdByCode('student'),
        ]);
    }
}
