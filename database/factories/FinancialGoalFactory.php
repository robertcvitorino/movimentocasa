<?php

namespace Database\Factories;

use App\Enums\FinancialGoalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialGoal>
 */
class FinancialGoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'target_amount' => fake()->randomFloat(2, 500, 5000),
            'month' => fake()->numberBetween(1, 12),
            'year' => (int) now()->format('Y'),
            'status' => fake()->randomElement(FinancialGoalStatus::cases()),
        ];
    }
}
