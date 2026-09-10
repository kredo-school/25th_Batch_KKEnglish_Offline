<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'status_code' => 'pending',
                'status_name' => 'Pending',
                'description' => '予約確認待ち',
            ],
            [
                'status_code' => 'confirmed',
                'status_name' => 'Confirmed',
                'description' => '予約確定',
            ],
            [
                'status_code' => 'cancelled',
                'status_name' => 'Cancelled',
                'description' => 'キャンセル済み',
            ],
            [
                'status_code' => 'completed',
                'status_name' => 'Completed',
                'description' => 'レッスン完了',
            ],
            [
                'status_code' => 'absent',
                'status_name' => 'Absent',
                'description' => '生徒欠席',
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('reservation_statuses')->updateOrInsert(
                [
                    'status_code' => $status['status_code'],
                ],
                [
                    'status_name' => $status['status_name'],
                    'description' => $status['description'],
                ]
            );
        }
    }
}
