<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskResponsibleType;
use App\Models\Member;
use App\Models\Ministry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-5 days', '+5 days');
        $end = (clone $start)->modify('+2 hours');

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'start_datetime' => $start,
            'end_datetime' => $end,
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'ministry_id' => null,
            'responsible_type' => TaskResponsibleType::Member,
            'responsible_member_id' => Member::factory(),
            'responsible_ministry_id' => null,
            'attachment_path' => null,
            'created_by' => null,
        ];
    }

    public function forMember(Member $member): static
    {
        return $this->state(fn (): array => [
            'responsible_type' => TaskResponsibleType::Member,
            'responsible_member_id' => $member->getKey(),
            'responsible_ministry_id' => null,
        ]);
    }

    public function forMinistry(Ministry $ministry): static
    {
        return $this->state(fn (): array => [
            'responsible_type' => TaskResponsibleType::Ministry,
            'responsible_member_id' => null,
            'responsible_ministry_id' => $ministry->getKey(),
        ]);
    }
}
