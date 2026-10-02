<?php

namespace Database\Seeders;

use App\Models\Station;
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
            // Master
            RoleSeeder::class,
            ReservationStatusSeeder::class,
            TransactionTypeSeeder::class,
            ExceptionTypeSeeder::class,

            // Users
            StudentSeeder::class,
            AdminSeeder::class,

            // ShiftPatternSeederはAdmin作成後
            ShiftPatternSeeder::class,

            // Teacher
            MaterialSeeder::class,
            TeacherProfileSeeder::class,
            MaterialTeacherSeeder::class,

            // Schedule
            TeacherScheduleSeeder::class,

            // Reservation
            ReservationSeeder::class,

            // Reservation related
            PointTransactionSeeder::class,
            LessonRecordSeeder::class,
            ReviewSeeder::class,

            // Other
            TeacherLikeSeeder::class,
            StationSeeder::class,
            TeacherStationAssignmentSeeder::class,
            AnnouncementSeeder::class,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Test User
        |--------------------------------------------------------------------------
        */

        User::factory()->create([
            'role_id' => 1,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);
    }
}
