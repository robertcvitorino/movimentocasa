<?php

namespace Database\Factories;

use App\Enums\FormationProgressStatus;
use App\Models\Formation;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MemberFormationProgress>
 */
class MemberFormationProgressFactory extends Factory
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
            'formation_id' => Formation::factory(),
            'status' => FormationProgressStatus::Completed,
            'progress_percentage' => 100,
            'started_at' => now()->subDays(5),
            'completed_at' => now(),
            'required_lessons_count' => 4,
            'completed_required_lessons_count' => 4,
        ];
    }
}
