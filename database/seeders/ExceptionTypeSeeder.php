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
                'type_name' => 'Personal Leave',
                'description' => 'Time off or unavailable periods for personal reasons.',
            ],
            [
                'type_code' => 'medical',
                'type_name' => 'Medical Leave',
                'description' => 'Time off or unavailable periods for medical appointments or health reasons.',
            ],
            [
                'type_code' => 'training',
                'type_name' => 'Training',
                'description' => 'Time off or unavailable periods to attend training.',
            ],
            [
                'type_code' => 'emergency',
                'type_name' => 'Emergency Leave',
                'description' => 'Time off or unavailable periods due to emergencies, such as sudden illness.',
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
