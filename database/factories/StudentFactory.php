<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'point_balance' => fake()->randomElement([
                500,
                1000,
                1500,
                2000,
                2500,
                3000,
            ]),

            'level' => fake()->randomElement([
                'A1',
                'A2',
                'B1',
                'B2',
                'C1',
                'C2',
            ]),

            'birthday' => fake()->dateTimeBetween(
                '-60 years',
                '-18 years'
            )->format('Y-m-d'),
        ];
    }
}
