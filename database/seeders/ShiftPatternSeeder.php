<?php

namespace Database\Seeders;

use App\Models\ShiftPattern;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class ShiftPatternSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::query()
            ->whereHas('role', function ($query) {
                $query->where('role_code', 'admin');
            })
            ->first();

        if (!$adminUser) {
            throw new RuntimeException(
                'ShiftPatternSeederを実行する前に、管理者ユーザーを作成してください。'
            );
        }

        $patterns = [
            [
                'pattern_code' => 'morning',
                'pattern_name' => '朝シフト',
                'start_time' => '06:00',
                'end_time' => '12:00',
                'end_day_offset' => 0,
                'slot_minutes' => 30,
                'is_active' => true,
                'display_order' => 10,
            ],
            [
                'pattern_code' => 'afternoon',
                'pattern_name' => '昼シフト',
                'start_time' => '12:00',
                'end_time' => '18:00',
                'end_day_offset' => 0,
                'slot_minutes' => 30,
                'is_active' => true,
                'display_order' => 20,
            ],
            [
                'pattern_code' => 'evening',
                'pattern_name' => '夜シフト',
                'start_time' => '18:00',
                'end_time' => '22:00',
                'end_day_offset' => 0,
                'slot_minutes' => 30,
                'is_active' => true,
                'display_order' => 30,
            ],
        ];

        foreach ($patterns as $pattern) {
            ShiftPattern::updateOrCreate(
                [
                    'pattern_code' => $pattern['pattern_code'],
                ],
                [
                    ...$pattern,
                    'created_by' => $adminUser->id,
                ]
            );
        }
    }
}
