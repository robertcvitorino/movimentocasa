<?php

namespace App\Actions\Formation;

use App\Enums\FormationProgressStatus;
use App\Enums\LessonProgressStatus;
use App\Models\Formation;
use App\Models\Member;
use App\Models\MemberFormationProgress;
use Illuminate\Support\Facades\DB;

class EnsureFormationProgressAction
{
    public function execute(Member $member, Formation $formation): MemberFormationProgress
    {
        return DB::transaction(function () use ($member, $formation): MemberFormationProgress {
            $lessons = $formation->lessons()->where('is_active', true)->get();

            $progress = MemberFormationProgress::query()->firstOrCreate(
                [
                    'member_id' => $member->getKey(),
                    'formation_id' => $formation->getKey(),
                ],
                [
                    'status' => FormationProgressStatus::InProgress,
                    'progress_percentage' => 0,
                    'started_at' => now(),
                    'last_accessed_at' => now(),
                    'required_lessons_count' => $lessons->where('is_required', true)->count(),
                    'completed_required_lessons_count' => 0,
                ],
            );

            foreach ($lessons as $lesson) {
                $progress->lessonProgress()->firstOrCreate(
                    ['formation_lesson_id' => $lesson->getKey()],
                    ['status' => LessonProgressStatus::NotStarted],
                );
            }

            return app(SyncFormationProgressAction::class)->execute($progress);
        });
    }
}
