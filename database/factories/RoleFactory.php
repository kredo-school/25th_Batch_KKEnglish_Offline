<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'role_name' => 'student',
            'role_code' => 'student',
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role_name' => 'admin',
            'role_code' => 'admin',
        ]);
    }

    public function teacher(): static
    {
        return $this->state(fn () => [
            'role_name' => 'teacher',
            'role_code' => 'teacher',
        ]);
    }

    public function student(): static
    {
        return $this->state(fn () => [
            'role_name' => 'student',
            'role_code' => 'student',
        ]);
    }
}
