<?php

namespace App\Actions\Formation;

use App\Enums\FormationProgressStatus;
use App\Enums\LessonProgressStatus;
use App\Models\FormationLesson;
use App\Models\MemberFormationProgress;
use Illuminate\Support\Facades\DB;

class CompleteFormationLessonAction
{
    public function execute(MemberFormationProgress $progress, FormationLesson $lesson): MemberFormationProgress
    {
        return DB::transaction(function () use ($progress, $lesson): MemberFormationProgress {
            $lessonProgress = $progress->lessonProgress()->firstOrCreate(
                ['formation_lesson_id' => $lesson->getKey()],
                ['status' => LessonProgressStatus::NotStarted],
            );

            $lessonProgress->forceFill([
                'status' => LessonProgressStatus::Completed,
                'started_at' => $lessonProgress->started_at ?? now(),
                'completed_at' => now(),
                'last_watched_at' => now(),
            ])->save();

            $progress = app(SyncFormationProgressAction::class)->execute($progress);
            $progress->loadMissing('formation');

            if (
                $progress->status === FormationProgressStatus::Completed
                && $progress->formation?->certificate_enabled
            ) {
                app(IssueCertificateAction::class)->execute($progress);
            }

            return $progress;
        });
    }
}
