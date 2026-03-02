<?php

namespace Database\Factories;

use App\Enums\MinistryStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ministry>
 */
class MinistryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'slug' => Str::slug(fake()->unique()->words(2, true)),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(MinistryStatus::cases()),
        ];
    }
}
