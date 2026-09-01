<?php

namespace Database\Seeders;

use App\Models\ExceptionType;
use Illuminate\Database\Seeder;

class ExceptionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $exceptionTypes = [
            [
                'type_code' => 'personal',
                'type_name' => '私用',
                'description' => '個人的な都合による休日・休止時間',
            ],
            [
                'type_code' => 'medical',
                'type_name' => '通院',
                'description' => '通院や健康上の理由による休日・休止時間',
            ],
            [
                'type_code' => 'training',
                'type_name' => '研修',
                'description' => '研修参加による休日・休止時間',
            ],
            [
                'type_code' => 'emergency',
                'type_name' => '緊急休暇',
                'description' => '急病など緊急事情による休日・休止時間',
            ],
        ];

        foreach ($exceptionTypes as $exceptionType) {
            ExceptionType::updateOrCreate(
                [
                    'type_code' => $exceptionType['type_code'],
                ],
                $exceptionType
            );
        }
    }
}
