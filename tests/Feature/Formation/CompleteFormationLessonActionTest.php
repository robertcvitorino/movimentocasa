<?php

use App\Actions\Formation\CompleteFormationLessonAction;
use App\Actions\Formation\EnsureFormationProgressAction;
use App\Enums\FormationProgressStatus;
use App\Enums\LessonSourceType;
use App\Models\Formation;
use App\Models\FormationLesson;
use App\Models\Member;
use App\Models\Quiz;
use Illuminate\Support\Facades\Storage;

it('issues certificate when required lessons are completed and there is no quiz', function () {
    Storage::fake('public');

    $member = Member::factory()->create();

    $formation = Formation::factory()->create([
        'certificate_enabled' => true,
    ]);

    $lesson = FormationLesson::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Aula 1',
        'source_type' => LessonSourceType::Youtube,
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'display_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $progress = app(EnsureFormationProgressAction::class)->execute($member, $formation);
    $progress = app(CompleteFormationLessonAction::class)->execute($progress, $lesson)->fresh('certificate');

    expect($progress->status)->toBe(FormationProgressStatus::Completed);
    expect($progress->certificate)->not->toBeNull();
    Storage::disk('public')->assertExists($progress->certificate->pdf_path);
});

it('issues certificate when quiz exists but is inactive', function () {
    Storage::fake('public');

    $member = Member::factory()->create();

    $formation = Formation::factory()->create([
        'certificate_enabled' => true,
    ]);

    Quiz::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Prova final',
        'minimum_score' => 70,
        'max_attempts' => 3,
        'is_active' => false,
    ]);

    $lesson = FormationLesson::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Aula 1',
        'source_type' => LessonSourceType::Youtube,
        'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
        'display_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $progress = app(EnsureFormationProgressAction::class)->execute($member, $formation);
    $progress = app(CompleteFormationLessonAction::class)->execute($progress, $lesson)->fresh('certificate');

    expect($progress->status)->toBe(FormationProgressStatus::Completed);
    expect($progress->certificate)->not->toBeNull();
    Storage::disk('public')->assertExists($progress->certificate->pdf_path);
});

it('issues certificate when quiz exists and is active after completing required lessons', function () {
    Storage::fake('public');

    $member = Member::factory()->create();

    $formation = Formation::factory()->create([
        'certificate_enabled' => true,
    ]);

    Quiz::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Prova final',
        'minimum_score' => 70,
        'max_attempts' => 3,
        'is_active' => true,
    ]);

    $lesson = FormationLesson::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Aula 1',
        'source_type' => LessonSourceType::Youtube,
        'video_url' => 'https://www.youtube.com/watch?v=3JZ_D3ELwOQ',
        'display_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $progress = app(EnsureFormationProgressAction::class)->execute($member, $formation);
    $progress = app(CompleteFormationLessonAction::class)->execute($progress, $lesson)->fresh('certificate');

    expect($progress->status)->toBe(FormationProgressStatus::Completed);
    expect($progress->certificate)->not->toBeNull();
    Storage::disk('public')->assertExists($progress->certificate->pdf_path);
});
