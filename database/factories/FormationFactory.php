<?php

namespace Database\Factories;

use App\Enums\FormationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Formation>
 */
class FormationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'slug' => Str::slug(fake()->sentence(3)),
            'short_description' => fake()->sentence(),
            'full_description' => fake()->paragraph(),
            'ministry_id' => null,
            'is_required' => fake()->boolean(),
            'status' => fake()->randomElement(FormationStatus::cases()),
            'certificate_enabled' => true,
            'quiz_enabled' => false,
            'workload_hours' => fake()->randomFloat(1, 1, 20),
            'published_at' => now(),
        ];
    }
}
