<?php

namespace Database\Seeders;

use App\Models\ReservationStatus;
use Illuminate\Database\Seeder;

class ReservationStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'status_code' => 'pending',
                'status_name' => '予約待ち',
                'description' => '予約が作成され、確定を待っている状態',
            ],
            [
                'status_code' => 'confirmed',
                'status_name' => '予約確定',
                'description' => '予約が確定している状態',
            ],
            [
                'status_code' => 'cancelled',
                'status_name' => 'キャンセル',
                'description' => '予約がキャンセルされた状態',
            ],
            [
                'status_code' => 'completed',
                'status_name' => '完了',
                'description' => 'レッスンが完了した状態',
            ],
        ];

        foreach ($statuses as $status) {
            ReservationStatus::updateOrCreate(
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
