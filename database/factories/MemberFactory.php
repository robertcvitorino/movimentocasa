<?php

namespace Database\Factories;

use App\Enums\MemberStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name(),
            'birth_date' => fake()->date(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'is_whatsapp' => fake()->boolean(),
            'instagram' => fake()->userName(),
            'zip_code' => fake()->postcode(),
            'street' => fake()->streetName(),
            'number' => (string) fake()->buildingNumber(),
            'complement' => fake()->optional()->secondaryAddress(),
            'district' => fake()->citySuffix(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'status' => fake()->randomElement(MemberStatus::cases()),
            'joined_at' => fake()->date(),
            'internal_notes' => fake()->optional()->sentence(),
        ];
    }
}
