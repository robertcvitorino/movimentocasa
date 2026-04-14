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
                'quiz' => 'Não há quiz ativo para esta formação.',
            ]);
        }

        $attemptCount = $progress->quizAttempts()->count();

        if ($quiz->max_attempts > 0 && $attemptCount >= $quiz->max_attempts) {
            throw ValidationException::withMessages([
                'quiz' => "Você atingiu o limite de {$quiz->max_attempts} tentativa(s) para este quiz.",
                'limit_reached' => true,
            ]);
        }

        return DB::transaction(function () use ($formation, $progress, $quiz, $answers, $attemptCount): QuizAttempt {
            $attempt = $progress->quizAttempts()->create([
                'quiz_id' => $quiz->getKey(),
                'member_id' => $progress->member_id,
                'attempt_number' => $attemptCount + 1,
                'status' => QuizAttemptStatus::InProgress,
                'started_at' => now(),
            ]);

            $activeQuestions = $quiz->questions->where('is_active', true);
            $totalQuestions = $activeQuestions->count();
            $correctCount = 0;

            foreach ($activeQuestions as $question) {
                $selectedOptionId = $answers[$question->getKey()] ?? null;
                $selectedOption = $question->options->firstWhere('id', (int) $selectedOptionId);
                $isCorrect = (bool) ($selectedOption?->is_correct ?? false);

                if ($isCorrect) {
                    $correctCount++;
                }

                $attempt->answers()->create([
                    'quiz_question_id' => $question->getKey(),
                    'quiz_option_id' => $selectedOption?->getKey(),
                    'is_correct' => $isCorrect,
                    'score_earned' => $isCorrect ? 1 : 0,
                ]);
            }

            $score = $totalQuestions > 0
                ? round(($correctCount / $totalQuestions) * 100, 2)
                : 0;

            $attempt->forceFill([
                'status' => QuizAttemptStatus::Passed,
                'score' => $score,
                'submitted_at' => now(),
                'finished_at' => now(),
            ])->save();

            $progress->forceFill([
                'quiz_score' => $score,
                'quiz_passed_at' => now(),
            ])->save();

            $progress = app(SyncFormationProgressAction::class)->execute($progress);

            if ($progress->status === \App\Enums\FormationProgressStatus::Completed && $formation->certificate_enabled) {
                app(IssueCertificateAction::class)->execute($progress);
            }

            return $attempt->fresh(['answers']);
        });
    }
}
