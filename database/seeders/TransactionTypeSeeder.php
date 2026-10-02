<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionType;

class TransactionTypeSeeder extends Seeder
{
    public function run(): void
    {
        // 予約ポイント消費
        TransactionType::updateOrCreate(
            [
                'type_code' => 'reservation_use',
            ],
            [
                'type_name' => '予約ポイント消費',
                'description' => 'レッスン予約時のポイント消費',
            ]
        );

        // 予約ポイント返還
        TransactionType::updateOrCreate(
            [
                'type_code' => 'reservation_refund',
            ],
            [
                'type_name' => '予約ポイント返還',
                'description' => '予約キャンセル時のポイント返還',
            ]
        );

        // 管理者によるポイント付与
        TransactionType::updateOrCreate(
            [
                'type_code' => 'grant',
            ],
            [
                'type_name' => 'Point Grant',
                'description' => 'Point Grant by Admin',
            ]
        );

        // 管理者によるポイント返還
        TransactionType::updateOrCreate(
            [
                'type_code' => 'refund',
            ],
            [
                'type_name' => 'Point Refund',
                'description' => 'Point Refund by Admin',
            ]
        );

        // 管理者によるポイント調整
        TransactionType::updateOrCreate(
            [
                'type_code' => 'adjustment',
            ],
            [
                'type_name' => 'Point Adjustment',
                'description' => 'Point Adjustment by Admin',
            ]
        );
    }
}
