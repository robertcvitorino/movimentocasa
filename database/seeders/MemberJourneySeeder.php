<?php

namespace Database\Seeders;

use App\Enums\FormationProgressStatus;
use App\Enums\FormationStatus;
use App\Enums\LessonProgressStatus;
use App\Enums\MemberStatus;
use App\Enums\QuizAttemptStatus;
use App\Models\Certificate;
use App\Models\Formation;
use App\Models\Member;
use App\Models\MemberFormationProgress;
use App\Models\MemberLessonProgress;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MemberJourneySeeder extends Seeder
{
    public function run(): void
    {
        $members = Member::query()
            ->whereIn('status', [MemberStatus::Active, MemberStatus::Visitor])
            ->orderBy('id')
            ->get();

        $formations = Formation::query()
            ->where('status', FormationStatus::Published)
            ->with(['lessons', 'quiz.questions.options'])
            ->orderBy('id')
            ->get();

        if ($members->isEmpty() || $formations->isEmpty()) {
            return;
        }

        foreach ($members as $memberIndex => $member) {
            foreach ($formations as $formationIndex => $formation) {
                $shouldComplete = ($memberIndex + $formationIndex) % 2 === 0;
                $requiredLessonsCount = $formation->lessons->where('is_required', true)->count();
                $completedRequiredLessonsCount = $shouldComplete
                    ? $requiredLessonsCount
                    : (int) floor($requiredLessonsCount / 2);

                $progress = MemberFormationProgress::query()->updateOrCreate(
                    [
                        'member_id' => $member->getKey(),
                        'formation_id' => $formation->getKey(),
                    ],
                    [
                        'status' => $shouldComplete ? FormationProgressStatus::Completed : FormationProgressStatus::InProgress,
                        'progress_percentage' => $shouldComplete ? 100 : 50,
                        'started_at' => now()->subDays(20 + ($memberIndex * 2)),
                        'completed_at' => $shouldComplete ? now()->subDays(5) : null,
                        'last_accessed_at' => now()->subDay(),
                        'required_lessons_count' => $requiredLessonsCount,
                        'completed_required_lessons_count' => $completedRequiredLessonsCount,
                        'quiz_score' => $shouldComplete ? 85 : null,
                        'quiz_passed_at' => $shouldComplete ? now()->subDays(6) : null,
                        'certificate_issued_at' => $shouldComplete ? now()->subDays(5) : null,
                    ],
                );

                foreach ($formation->lessons as $lessonIndex => $lesson) {
                    $lessonCompleted = $shouldComplete || ($lessonIndex < $completedRequiredLessonsCount);

                    MemberLessonProgress::query()->updateOrCreate(
                        [
                            'member_formation_progress_id' => $progress->getKey(),
                            'formation_lesson_id' => $lesson->getKey(),
                        ],
                        [
                            'status' => $lessonCompleted ? LessonProgressStatus::Completed : LessonProgressStatus::InProgress,
                            'started_at' => now()->subDays(18 - $lessonIndex),
                            'completed_at' => $lessonCompleted ? now()->subDays(10 - $lessonIndex) : null,
                            'last_watched_at' => now()->subDays(2),
                            'watch_seconds' => $lessonCompleted
                                ? (int) (($lesson->estimated_duration_minutes ?? 15) * 60)
                                : (int) (($lesson->estimated_duration_minutes ?? 15) * 30),
                        ],
                    );
                }

                if ($formation->quiz) {
                    $attempt = QuizAttempt::query()->updateOrCreate(
                        [
                            'member_formation_progress_id' => $progress->getKey(),
                            'attempt_number' => 1,
                        ],
                        [
                            'quiz_id' => $formation->quiz->getKey(),
                            'member_id' => $member->getKey(),
                            'status' => $shouldComplete ? QuizAttemptStatus::Passed : QuizAttemptStatus::InProgress,
                            'score' => $shouldComplete ? 85 : null,
                            'started_at' => now()->subDays(7),
                            'submitted_at' => $shouldComplete ? now()->subDays(6) : null,
                            'finished_at' => $shouldComplete ? now()->subDays(6) : null,
                        ],
                    );

                    foreach ($formation->quiz->questions as $question) {
                        $selectedOption = $question->options->firstWhere('is_correct', true)
                            ?? $question->options->first();

                        if (! $selectedOption) {
                            continue;
                        }

                        $isCorrect = $shouldComplete ? (bool) $selectedOption->is_correct : false;

                        QuizAttemptAnswer::query()->updateOrCreate(
                            [
                                'quiz_attempt_id' => $attempt->getKey(),
                                'quiz_question_id' => $question->getKey(),
                            ],
                            [
                                'quiz_option_id' => $selectedOption->getKey(),
                                'is_correct' => $isCorrect,
                                'score_earned' => $isCorrect ? $question->weight : 0,
                            ],
                        );
                    }
                }

                if ($shouldComplete && $formation->certificate_enabled) {
                    $certificateCode = sprintf(
                        'MC-%04d-%04d-%03d',
                        $member->getKey(),
                        $formation->getKey(),
                        1
                    );

                    Certificate::query()->updateOrCreate(
                        ['member_formation_progress_id' => $progress->getKey()],
                        [
                            'member_id' => $member->getKey(),
                            'formation_id' => $formation->getKey(),
                            'certificate_code' => $certificateCode,
                            'issued_at' => now()->subDays(5),
                            'pdf_path' => 'certificates/'.Str::lower($certificateCode).'.pdf',
                            'verification_hash' => Str::uuid()->toString(),
                        ],
                    );
                }
            }
        }
    }
}
