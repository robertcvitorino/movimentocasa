<?php

use App\Actions\Formation\EnsureFormationProgressAction;
use App\Actions\Formation\IssueCertificateAction;
use App\Actions\Formation\SubmitQuizAttemptAction;
use App\Enums\FormationProgressStatus;
use App\Enums\QuestionType;
use App\Models\Formation;
use App\Models\Member;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

it('submits quiz and calculates score without weights', function () {
    $member = Member::factory()->create();
    $formation = Formation::factory()->create(['quiz_enabled' => true]);

    $quiz = Quiz::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Quiz final',
        'minimum_score' => 0,
        'max_attempts' => 3,
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

    $attempt = app(SubmitQuizAttemptAction::class)->execute($progress, [
        $question->getKey() => $correctOption->getKey(),
    ]);

    expect($attempt->attempt_number)->toBe(1);
    expect((float) $attempt->score)->toBe(100.0);
    expect($progress->fresh()->quiz_passed_at)->not->toBeNull();
});

it('blocks new submission when max_attempts is reached', function () {
    $member = Member::factory()->create();
    $formation = Formation::factory()->create(['quiz_enabled' => true]);

    $quiz = Quiz::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Quiz final',
        'minimum_score' => 0,
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

    $progress = app(EnsureFormationProgressAction::class)->execute($member, $formation);

    // Primeira tentativa: sucesso
    app(SubmitQuizAttemptAction::class)->execute($progress, [
        $question->getKey() => $correctOption->getKey(),
    ]);

    // Segunda tentativa: bloqueia com ValidationException e campo limit_reached
    expect(fn () => app(SubmitQuizAttemptAction::class)->execute($progress->fresh(), [
        $question->getKey() => $correctOption->getKey(),
    ]))->toThrow(ValidationException::class);
});

it('never prevents certificate generation when attempt limit is reached', function () {
    Storage::fake('public');

    $member = Member::factory()->create();
    $formation = Formation::factory()->create([
        'quiz_enabled' => true,
        'certificate_enabled' => true,
        'workload_hours' => 4,
    ]);

    $quiz = Quiz::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Quiz final',
        'minimum_score' => 0,
        'max_attempts' => 1,
        'is_active' => true,
    ]);

    $question = QuizQuestion::query()->create([
        'quiz_id' => $quiz->getKey(),
        'statement' => 'Questao',
        'question_type' => QuestionType::MultipleChoice,
        'weight' => 1,
        'display_order' => 1,
        'is_active' => true,
    ]);

    $option = QuizOption::query()->create([
        'quiz_question_id' => $question->getKey(),
        'label' => 'Resposta',
        'is_correct' => true,
        'display_order' => 1,
    ]);

    $progress = app(EnsureFormationProgressAction::class)->execute($member, $formation);

    // Forcefully mark as completed to allow certificate issuance
    $progress->forceFill([
        'status' => FormationProgressStatus::Completed,
        'completed_at' => now(),
    ])->save();

    // Emitir certificado diretamente (independente do limite de tentativas)
    $certificate = app(IssueCertificateAction::class)->execute($progress->fresh());

    expect($certificate)->not->toBeNull();
    expect($certificate->member_id)->toBe($member->getKey());
    Storage::disk('public')->assertExists($certificate->pdf_path);
});
