<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpectedReservationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [];

        // 6:00〜23:30まで30分ごと
        $startTime = Carbon::createFromTime(6, 0, 0);
        $endTime = Carbon::createFromTime(23, 30, 0);

        while ($startTime <= $endTime) {

            $time = $startTime->format('H:i:s');

            // normal / weekday
            if ($time >= '06:00:00' && $time < '09:00:00') {
                $weekdayNormal = 10;
            } elseif ($time >= '09:00:00' && $time < '13:00:00') {
                $weekdayNormal = 20;
            } elseif ($time >= '13:00:00' && $time < '14:00:00') {
                $weekdayNormal = 10;
            } elseif ($time >= '14:00:00' && $time < '18:00:00') {
                $weekdayNormal = 20;
            } else {
                // 18:00〜24:00
                $weekdayNormal = 15;
            }

            // normal / weekend
            if ($time >= '06:00:00' && $time < '09:00:00') {
                $weekendNormal = 10;
            } elseif ($time >= '09:00:00' && $time < '13:00:00') {
                $weekendNormal = 15;
            } elseif ($time >= '13:00:00' && $time < '14:00:00') {
                $weekendNormal = 10;
            } elseif ($time >= '14:00:00' && $time < '18:00:00') {
                $weekendNormal = 15;
            } else {
                // 18:00〜24:00
                $weekendNormal = 15;
            }

            // normal
            $settings[] = [
                'season_type' => 'normal',
                'day_type' => 'weekday',
                'target_time' => $time,
                'expected_count' => $weekdayNormal,
            ];

            $settings[] = [
                'season_type' => 'normal',
                'day_type' => 'weekend',
                'target_time' => $time,
                'expected_count' => $weekendNormal,
            ];

            // busy = normal × 1.2
            $settings[] = [
                'season_type' => 'busy',
                'day_type' => 'weekday',
                'target_time' => $time,
                'expected_count' => (int) round($weekdayNormal * 1.2),
            ];

            $settings[] = [
                'season_type' => 'busy',
                'day_type' => 'weekend',
                'target_time' => $time,
                'expected_count' => (int) round($weekendNormal * 1.2),
            ];

            // quiet = normal × 0.8
            $settings[] = [
                'season_type' => 'quiet',
                'day_type' => 'weekday',
                'target_time' => $time,
                'expected_count' => (int) round($weekdayNormal * 0.8),
            ];

            $settings[] = [
                'season_type' => 'quiet',
                'day_type' => 'weekend',
                'target_time' => $time,
                'expected_count' => (int) round($weekendNormal * 0.8),
            ];

            $startTime->addMinutes(30);
        }

        // 既存データがあれば更新、なければ登録
        DB::table('expected_reservation_settings')->upsert(
            $settings,
            ['season_type', 'day_type', 'target_time'],
            ['expected_count', 'updated_at']
        );
    }
}
