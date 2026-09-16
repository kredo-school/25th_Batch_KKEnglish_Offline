<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionType;

class TransactionTypeSeeder extends Seeder
{
    public function run(): void
    {
        TransactionType::updateOrCreate(
            [
                'type_code' => 'reservation_use',
            ],
            [
                'type_name' => '予約ポイント消費',
                'description' => 'レッスン予約時のポイント消費',
            ]
        );

        TransactionType::updateOrCreate(
            [
                'type_code' => 'reservation_refund',
            ],
            [
                'type_name' => '予約ポイント返還',
                'description' => '予約キャンセル時のポイント返還',
            ]
        );
    }
}
