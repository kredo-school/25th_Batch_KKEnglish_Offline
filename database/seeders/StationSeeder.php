<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Station;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stations = [];

        // Group Lesson Room 1〜5
        for ($i = 1; $i <= 5; $i++) {
            $stations[] = [
                'name' => 'Group Lesson Room ' . $i,
                'code' => 'GROUP_LESSON_ROOM_' . $i,
                'is_active' => true,
            ];
        }

        // Station A〜Z
        for ($i = 0; $i < 26; $i++) {
            $letter = chr(65 + $i);

            $stations[] = [
                'name' => 'Station ' . $letter,
                'code' => 'STATION_' . $letter,
                'is_active' => true,
            ];
        }

        foreach ($stations as $station) {
            Station::updateOrCreate(
                ['code' => $station['code']],
                $station
            );
        }
    }
}
