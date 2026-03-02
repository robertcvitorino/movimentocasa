<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SacramentalTitle>
 */
class SacramentalTitleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'slug' => Str::slug(fake()->unique()->words(2, true)),
            'type' => 'other',
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 99),
        ];
    }
}
