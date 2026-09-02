<?php

namespace Database\Factories;

use App\Models\ShiftPattern;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftPatternFactory extends Factory
{
    protected $model = ShiftPattern::class;

    public function definition(): array
    {
        return [
            'pattern_code' => 'P' . $this->faker->unique()->numerify('###'),
            'pattern_name' => 'Pattern ' . $this->faker->word(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'end_day_offset' => 0,
            'slot_minutes' => 30,
            'is_active' => true,
            'display_order' => 0,
            'created_by' => 1, // テスト用途。必要ならUserFactory連携に変更
        ];
    }
}
