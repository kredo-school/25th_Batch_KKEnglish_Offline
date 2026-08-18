<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
        'id' => 1,
        'role_code' => 'student',
        'role_name' => 'student',
        'description' => 'student role',
       ]);

        Role::create([
        'id' => 2,
        'role_code' => 'teacher',
        'role_name' => 'teacher',
        'description' => 'teacher role',
       ]);

        Role::create([
        'id' => 3,
        'role_code' => 'admin',
        'role_name' => 'admin',
        'description' => 'admin role',
       ]);
    }
}
