<?php

use App\Actions\Formation\IssueCertificateAction;
use App\Enums\FormationProgressStatus;
use App\Models\Formation;
use App\Models\Member;
use App\Models\MemberFormationProgress;
use App\Models\Ministry;
use Barryvdh\DomPDF\Facade\Pdf;
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
    expect($html)->toContain('Nota final');
});

it('generates a certificate PDF with exactly one page', function () {
    Storage::fake('public');

    $member = Member::factory()->create([
        'full_name' => 'Maria Aparecida dos Santos Silva',
    ]);

    $ministry = Ministry::factory()->create([
        'name' => 'Ministerio de Louvor e Adoracao',
    ]);

    $formation = Formation::factory()->create([
        'title' => 'Formacao Completa em Lideranca e Gestao de Ministerios',
        'ministry_id' => $ministry->getKey(),
        'workload_hours' => 120,
        'certificate_enabled' => true,
    ]);

    $progress = MemberFormationProgress::query()->create([
        'member_id' => $member->getKey(),
        'formation_id' => $formation->getKey(),
        'status' => FormationProgressStatus::Completed,
        'progress_percentage' => 100,
        'started_at' => now()->subDays(30),
        'completed_at' => now()->startOfMinute(),
        'required_lessons_count' => 20,
        'completed_required_lessons_count' => 20,
        'quiz_score' => 95,
        'quiz_passed_at' => now()->subMinute(),
    ]);

    $pdf = Pdf::loadView('pdf.certificate', [
        'certificateCode' => 'CERT-20260411-ABCDEFGH',
        'issuedAt' => now(),
        'member' => $member,
        'formation' => $formation->load('ministry'),
        'progress' => $progress,
    ])
        ->setPaper('a4', 'landscape')
        ->setOption('dpi', 72)
        ->setOption('defaultMediaType', 'print')
        ->setOption('isFontSubsettingEnabled', true);

    $pdf->render();

    $pageCount = $pdf->getDomPDF()->getCanvas()->get_page_count();

    expect($pageCount)->toBe(1);
});
