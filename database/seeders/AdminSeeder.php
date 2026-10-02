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
                'first_name' => 'Fujimaki',
                'last_name' => 'Taro',
                'profile_image' => 'https://images.openai.com/static-rsc-4/sDlrqO2l4zwNn-Aq_LfAjWhT3yG57tDLqDYKX7EWjkjer-Y7W0JbOPCXcmnFIrvAqIajEJdvCzipP458HRlb9dA_2CBrrSHj6IBUuU5XikY_PbYqKJX36F55JZVHPiRk0fInEH1dqoal-O9PVGDs_dfQ1GGbqJyXBWXKr7JA2EE?purpose=inline',
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
