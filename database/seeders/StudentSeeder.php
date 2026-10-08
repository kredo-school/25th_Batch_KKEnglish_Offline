<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Demo Student
        |--------------------------------------------------------------------------
        |
        | プレゼンで実際にログインする固定Student
        |
        */

        $demoUser = User::factory()
            ->student()
            ->create([
                'first_name' => 'Kurt',
                'last_name' => 'Taro',
                'email' => 'student@example.com',
                'password' => Hash::make('password'),
                'nationality' => 'JP',
                'gender' => 'other',
                'status' => 'active',
                'profile_image' => 'images/Kurt.png',
            ]);

        Student::factory()->create([
            'user_id' => $demoUser->id,
            'point_balance' => 3000,
            'level' => 'B1',
            'birthday' => '1995-01-01',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Test Students
        |--------------------------------------------------------------------------
        |
        | 残り49人をFactoryで生成
        |
        */

        User::factory()
            ->student()
            ->count(49)
            ->create()
            ->each(function ($user) {

                Student::factory()->create([
                    'user_id' => $user->id,
                ]);

            });
    }
}
