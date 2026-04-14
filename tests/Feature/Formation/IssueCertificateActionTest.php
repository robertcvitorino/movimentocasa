<?php

use App\Actions\Formation\IssueCertificateAction;
use App\Enums\FormationProgressStatus;
use App\Models\Certificate;
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
        'qrCodeDataUri' => 'data:image/png;base64,test',
    ])->render();

    expect($html)->toContain('Movimento Casa');
    expect($html)->toContain('Nome da Pessoa');
    expect($html)->toContain('Iluminacao');
    expect($html)->toContain('Codigo de autenticacao');
    expect($html)->toContain('Producao');
    expect($html)->toContain('size: 297mm 210mm');
    expect($html)->not->toContain('Nota final');
});

it('generates a certificate with QR code for verification', function () {
    Storage::fake('public');

    $member = Member::factory()->create();
    $formation = Formation::factory()->create([
        'certificate_enabled' => true,
        'workload_hours' => 10,
    ]);

    $progress = MemberFormationProgress::query()->create([
        'member_id' => $member->getKey(),
        'formation_id' => $formation->getKey(),
        'status' => FormationProgressStatus::Completed,
        'progress_percentage' => 100,
        'started_at' => now()->subDays(3),
        'completed_at' => now(),
        'required_lessons_count' => 3,
        'completed_required_lessons_count' => 3,
        'quiz_score' => 85.50,
        'quiz_passed_at' => now(),
    ]);

    $certificate = app(IssueCertificateAction::class)->execute($progress);

    expect($certificate)->toBeInstanceOf(Certificate::class);
    expect($certificate->verification_hash)->not->toBeNull();

    Storage::disk('public')->assertExists($certificate->pdf_path);

    $progress->refresh();
    expect($progress->certificate_issued_at)->not->toBeNull();
});

it('returns existing certificate when already issued', function () {
    Storage::fake('public');

    $member = Member::factory()->create();
    $formation = Formation::factory()->create([
        'certificate_enabled' => true,
    ]);

    $progress = MemberFormationProgress::query()->create([
        'member_id' => $member->getKey(),
        'formation_id' => $formation->getKey(),
        'status' => FormationProgressStatus::Completed,
        'progress_percentage' => 100,
        'started_at' => now()->subDays(3),
        'completed_at' => now(),
        'required_lessons_count' => 2,
        'completed_required_lessons_count' => 2,
    ]);

    $action = app(IssueCertificateAction::class);

    $first = $action->execute($progress);
    $second = $action->execute($progress);

    expect($first->getKey())->toBe($second->getKey());
    expect(Certificate::query()->count())->toBe(1);
});
