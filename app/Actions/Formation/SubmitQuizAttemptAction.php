<?php

namespace App\Actions\Formation;

use App\Enums\QuizAttemptStatus;
use App\Models\MemberFormationProgress;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitQuizAttemptAction
{
    /**
     * @param  array<int|string, int|string|null>  $answers
     */
    public function execute(MemberFormationProgress $progress, array $answers): QuizAttempt
    {
        $formation = $progress->formation()->with(['quiz.questions.options'])->firstOrFail();
        $quiz = $formation->quiz;

        if (! $quiz || ! $quiz->is_active) {
            throw ValidationException::withMessages([
                'quiz' => 'Nao ha quiz ativo para esta formacao.',
            ]);
        }

        return DB::transaction(function () use ($formation, $progress, $quiz, $answers): QuizAttempt {
            $attempt = $progress->quizAttempts()->create([
                'quiz_id' => $quiz->getKey(),
                'member_id' => $progress->member_id,
                'attempt_number' => $progress->quizAttempts()->count() + 1,
                'status' => QuizAttemptStatus::InProgress,
                'started_at' => now(),
            ]);

            $totalWeight = 0.0;
            $earnedWeight = 0.0;

            foreach ($quiz->questions->where('is_active', true) as $question) {
                $selectedOptionId = $answers[$question->getKey()] ?? null;
                $selectedOption = $question->options->firstWhere('id', (int) $selectedOptionId);
                $isCorrect = (bool) ($selectedOption?->is_correct ?? false);
                $weight = (float) $question->weight;

                $totalWeight += $weight;
                $earnedWeight += $isCorrect ? $weight : 0;

                $attempt->answers()->create([
                    'quiz_question_id' => $question->getKey(),
                    'quiz_option_id' => $selectedOption?->getKey(),
                    'is_correct' => $isCorrect,
                    'score_earned' => $isCorrect ? $weight : 0,
                ]);
            }

            $score = $totalWeight > 0 ? round(($earnedWeight / $totalWeight) * 100, 2) : 0;
            $passed = $score >= (float) $quiz->minimum_score;

            $attempt->forceFill([
                'status' => $passed ? QuizAttemptStatus::Passed : QuizAttemptStatus::Failed,
                'score' => $score,
                'submitted_at' => now(),
                'finished_at' => now(),
            ])->save();

            $progress->forceFill([
                'quiz_score' => $score,
                'quiz_passed_at' => $passed ? now() : null,
            ])->save();

            $progress = app(SyncFormationProgressAction::class)->execute($progress);

            if ($progress->status === \App\Enums\FormationProgressStatus::Completed && $formation->certificate_enabled) {
                app(IssueCertificateAction::class)->execute($progress);
            }

            return $attempt->fresh(['answers']);
        });
    }
}
