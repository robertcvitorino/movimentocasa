<?php

namespace Database\Factories;

use App\Enums\EventRecurrenceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->addDays(fake()->numberBetween(1, 20))->startOfHour();

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'start_datetime' => $start,
            'end_datetime' => (clone $start)->addHours(2),
            'location' => fake()->city(),
            'color' => '#2563eb',
            'is_recurring' => false,
            'recurrence_type' => null,
            'recurrence_until' => null,
        ];
    }

    public function recurring(EventRecurrenceType $type = EventRecurrenceType::Weekly): static
    {
        return $this->state(fn (): array => [
            'is_recurring' => true,
            'recurrence_type' => $type,
            'recurrence_until' => now()->addMonth()->toDateString(),
        ]);
    }
}
