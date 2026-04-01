<?php

namespace App\Actions\Formation;

use App\Enums\FormationProgressStatus;
use App\Enums\LessonProgressStatus;
use App\Models\MemberFormationProgress;

class SyncFormationProgressAction
{
    public function execute(MemberFormationProgress $progress): MemberFormationProgress
    {
        $formation = $progress->formation()->with(['lessons' => fn ($query) => $query->where('is_active', true), 'quiz'])->firstOrFail();
        $lessonProgress = $progress->lessonProgress()->get()->keyBy('formation_lesson_id');
        $lessons = $formation->lessons;

        $totalLessons = $lessons->count();
        $completedLessons = $lessons
            ->filter(fn ($lesson) => $lessonProgress->get($lesson->getKey())?->status === LessonProgressStatus::Completed)
            ->count();

        $requiredLessonsCount = $lessons->where('is_required', true)->count();
        $completedRequiredLessonsCount = $lessons
            ->where('is_required', true)
            ->filter(fn ($lesson) => $lessonProgress->get($lesson->getKey())?->status === LessonProgressStatus::Completed)
            ->count();

        $allRequiredLessonsCompleted = $requiredLessonsCount === $completedRequiredLessonsCount;

        $status = FormationProgressStatus::InProgress;

        if ($allRequiredLessonsCompleted) {
            $status = FormationProgressStatus::Completed;
        }

        $progress->forceFill([
            'status' => $status,
            'required_lessons_count' => $requiredLessonsCount,
            'completed_required_lessons_count' => $completedRequiredLessonsCount,
            'progress_percentage' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0,
            'completed_at' => $status === FormationProgressStatus::Completed ? ($progress->completed_at ?? now()) : null,
            'last_accessed_at' => now(),
        ])->save();

        return $progress->fresh(['lessonProgress', 'quizAttempts']);
    }
}
