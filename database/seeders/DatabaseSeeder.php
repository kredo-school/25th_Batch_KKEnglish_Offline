<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;


    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            MaterialSeeder::class,
            TeacherProfileSeeder::class,
            ExceptionTypeSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role_id' => 3,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => bcrypt('password123'),
                'status' => 'active',
            ]
        );

        Admin::firstOrCreate(['user_id' => $admin->id]);

        $this->call([
            ShiftPatternSeeder::class,
            TeacherScheduleSeeder::class,
            MaterialTeacherSeeder::class,
<<<<<<< HEAD
=======
            TransactionTypeSeeder::class,
>>>>>>> 33ed112abf095cc369ec0a77a705cda47f6dc63d
        ]);

        $student = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'role_id' => 1,
                'first_name' => 'Test',
                'last_name' => 'User',
                'password' => bcrypt('password123'),
                'status' => 'active',
            ]
        );

        Student::firstOrCreate(
            ['user_id' => $student->id],
            [
                'point_balance' => 1000,
                'birthday' => '2000-01-01',
            ]
        );
    }
}
