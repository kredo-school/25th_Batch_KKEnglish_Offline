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
                'status_id' => 1,
                'status_code' => 'pending',
                'status_name' => 'Pending',
                'description' => '予約確認待ち',
            ],
            [
                'status_id' => 2,
                'status_code' => 'confirmed',
                'status_name' => 'Confirmed',
                'description' => '予約確定・レッスン待ち',
            ],
            [
                'status_id' => 3,
                'status_code' => 'cancelled',
                'status_name' => 'Cancelled',
                'description' => 'キャンセル済み',
            ],
            [
                'status_id' => 4,
                'status_code' => 'completed',
                'status_name' => 'Completed',
                'description' => 'レッスン完了',
            ],
            [
                'status_id' => 5,
                'status_code' => 'absent',
                'status_name' => 'Absent',
                'description' => '生徒欠席',
            ],
            [
                'status_id' => 6,
                'status_code' => 'awaiting_result',
                'status_name' => 'Awaiting Result',
                'description' => 'レッスン結果登録待ち',
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('reservation_statuses')->updateOrInsert(
                [
                    'status_code' => $status['status_code'],
                ],
                [
                    'status_id' => $status['status_id'],
                    'status_name' => $status['status_name'],
                    'description' => $status['description'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
