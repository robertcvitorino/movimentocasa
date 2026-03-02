<?php

namespace Database\Factories;

use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Enums\PaymentMethod;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MemberContribution>
 */
class MemberContributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'reference_month' => fake()->numberBetween(1, 12),
            'reference_year' => (int) now()->format('Y'),
            'contribution_type' => fake()->randomElement(ContributionType::cases()),
            'expected_amount' => fake()->randomFloat(2, 20, 300),
            'declared_amount' => fake()->randomFloat(2, 0, 300),
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'status' => fake()->randomElement(ContributionStatus::cases()),
            'declared_at' => now(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
