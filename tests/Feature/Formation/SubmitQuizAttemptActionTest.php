<?php

use App\Actions\Formation\EnsureFormationProgressAction;
use App\Actions\Formation\SubmitQuizAttemptAction;
use App\Enums\QuestionType;
use App\Models\Formation;
use App\Models\Member;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;

it('allows submitting the quiz multiple times even when max_attempts is reached', function () {
    $member = Member::factory()->create();
    $formation = Formation::factory()->create();

    $quiz = Quiz::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Quiz final',
        'minimum_score' => 70,
        'max_attempts' => 1,
        'is_active' => true,
    ]);

    $question = QuizQuestion::query()->create([
        'quiz_id' => $quiz->getKey(),
        'statement' => 'Qual a resposta correta?',
        'question_type' => QuestionType::MultipleChoice,
        'weight' => 1,
        'display_order' => 1,
        'is_active' => true,
    ]);

    $correctOption = QuizOption::query()->create([
        'quiz_question_id' => $question->getKey(),
        'label' => 'Opcao correta',
        'is_correct' => true,
        'display_order' => 1,
    ]);

    QuizOption::query()->create([
        'quiz_question_id' => $question->getKey(),
        'label' => 'Opcao incorreta',
        'is_correct' => false,
        'display_order' => 2,
    ]);

    $progress = app(EnsureFormationProgressAction::class)->execute($member, $formation);

    $firstAttempt = app(SubmitQuizAttemptAction::class)->execute($progress, [
        $question->getKey() => $correctOption->getKey(),
    ]);

    $secondAttempt = app(SubmitQuizAttemptAction::class)->execute($progress->fresh(), [
        $question->getKey() => $correctOption->getKey(),
    ]);

    expect($firstAttempt->attempt_number)->toBe(1);
    expect($secondAttempt->attempt_number)->toBe(2);
    expect($progress->fresh()->quizAttempts()->count())->toBe(2);
});
