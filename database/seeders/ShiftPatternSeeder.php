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
                'pattern_name' => 'Morning Shift',
                'start_time' => '6:00',
                'end_time' => '14:00',
                'end_day_offset' => 0,
                'slot_minutes' => 30,
                'is_active' => true,
                'display_order' => 10,
            ],
            [
                'pattern_code' => 'afternoon',
                'pattern_name' => 'Afternoon Shift',
                'start_time' => '12:00',
                'end_time' => '20:00',
                'end_day_offset' => 0,
                'slot_minutes' => 30,
                'is_active' => true,
                'display_order' => 20,
            ],
            [
                'pattern_code' => 'evening',
                'pattern_name' => 'Evening Shift',
                'start_time' => '16:00',
                'end_time' => '24:00',
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
