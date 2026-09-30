<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Main Demo Admin
        |--------------------------------------------------------------------------
        */

        $adminUser = User::factory()
            ->admin()
            ->create([
                'first_name' => 'Demo',
                'last_name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'nationality' => 'JP',
                'gender' => 'other',
                'status' => 'active',
            ]);

        Admin::create([
            'user_id' => $adminUser->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Additional Admins
        |--------------------------------------------------------------------------
        */

        User::factory()
            ->admin()
            ->count(2)
            ->create()
            ->each(function ($user) {

                Admin::create([
                    'user_id' => $user->id,
                ]);

            });
    }
}
