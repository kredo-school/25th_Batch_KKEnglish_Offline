<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeacherStationAssignment;

class TeacherStationAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignments = [];

        for ($teacherId = 1; $teacherId <= 30; $teacherId++) {

            // Teacher 1 → Station 1
            // Teacher 2 → Station 2
            // ...
            // Teacher 30 → Station 30
            $stationId = $teacherId;

            $assignments[] = [
                'teacher_id' => $teacherId,
                'station_id' => $stationId,
                'start_date' => '2026-10-01',
                'end_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        TeacherStationAssignment::insert($assignments);
    }
}
