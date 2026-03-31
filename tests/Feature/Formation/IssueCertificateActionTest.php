<?php

use App\Actions\Formation\IssueCertificateAction;
use App\Enums\FormationProgressStatus;
use App\Models\Formation;
use App\Models\Member;
use App\Models\MemberFormationProgress;
use App\Models\Ministry;
use Illuminate\Support\Facades\Storage;

it('issues a certificate PDF using the Movimento Casa template', function () {
    Storage::fake('public');

    $member = Member::factory()->create([
        'full_name' => 'Nome da Pessoa',
    ]);

    $ministry = Ministry::factory()->create([
        'name' => 'Producao',
    ]);

    $formation = Formation::factory()->create([
        'title' => 'Iluminacao',
        'ministry_id' => $ministry->getKey(),
        'workload_hours' => 24,
        'certificate_enabled' => true,
    ]);

    $progress = MemberFormationProgress::query()->create([
        'member_id' => $member->getKey(),
        'formation_id' => $formation->getKey(),
        'status' => FormationProgressStatus::Completed,
        'progress_percentage' => 100,
        'started_at' => now()->subDays(5),
        'completed_at' => now()->startOfMinute(),
        'required_lessons_count' => 6,
        'completed_required_lessons_count' => 6,
        'quiz_score' => 100,
        'quiz_passed_at' => now()->subMinute(),
    ]);

    $certificate = app(IssueCertificateAction::class)->execute($progress);

    expect($certificate->certificate_code)->toStartWith('CERT-');
    expect($certificate->pdf_path)->not->toBeNull();
    expect($certificate->member_id)->toBe($member->getKey());
    expect($certificate->formation_id)->toBe($formation->getKey());

    Storage::disk('public')->assertExists($certificate->pdf_path);

    $html = view('pdf.certificate', [
        'certificateCode' => $certificate->certificate_code,
        'issuedAt' => $certificate->issued_at,
        'member' => $member,
        'formation' => $formation->load('ministry'),
        'progress' => $progress->fresh(),
    ])->render();

    expect($html)->toContain('Movimento Casa');
    expect($html)->toContain('Nome da Pessoa');
    expect($html)->toContain('Iluminacao');
    expect($html)->toContain('Codigo de autenticacao');
});
