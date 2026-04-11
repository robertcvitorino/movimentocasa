<?php

use App\Actions\Formation\IssueCertificateAction;
use App\Enums\FormationProgressStatus;
use App\Models\Certificate;
use App\Models\Formation;
use App\Models\Member;
use App\Models\MemberFormationProgress;
use App\Models\Ministry;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

it('issues a certificate PDF with QR code using the Movimento Casa template', function () {
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
    expect($certificate->verification_hash)->not->toBeNull();

    Storage::disk('public')->assertExists($certificate->pdf_path);
});

it('generates a certificate PDF with exactly one page including QR code', function () {
    Storage::fake('public');

    $member = Member::factory()->create([
        'full_name' => 'Maria Santos Silva',
    ]);

    $ministry = Ministry::factory()->create([
        'name' => 'Louvor e Adoração',
    ]);

    $formation = Formation::factory()->create([
        'title' => 'Formação em Liderança de Ministérios',
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

    $builder = new Builder(
        writer: new PngWriter,
        data: 'https://example.com/certificados/test-hash',
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::Medium,
        size: 150,
        margin: 5,
    );
    $qrResult = $builder->build();

    $pdf = Pdf::loadView('pdf.certificate', [
        'certificateCode' => 'CERT-20260411-ABCDEFGH',
        'issuedAt' => now(),
        'member' => $member,
        'formation' => $formation->load('ministry'),
        'progress' => $progress,
        'qrCodeBase64' => base64_encode($qrResult->getString()),
        'verificationUrl' => 'https://example.com/certificados/test-hash',
    ])
        ->setPaper('a4', 'landscape')
        ->setOption('dpi', 72)
        ->setOption('defaultMediaType', 'print')
        ->setOption('isFontSubsettingEnabled', true);

    $pdf->render();

    $pageCount = $pdf->getDomPDF()->getCanvas()->get_page_count();

    expect($pageCount)->toBe(1);
});

it('allows public access to certificate verification page', function () {
    $member = Member::factory()->create(['full_name' => 'Joao Silva']);
    $ministry = Ministry::factory()->create(['name' => 'Louvor']);
    $formation = Formation::factory()->create([
        'title' => 'Curso de Louvor',
        'ministry_id' => $ministry->getKey(),
        'certificate_enabled' => true,
    ]);

    $progress = MemberFormationProgress::query()->create([
        'member_id' => $member->getKey(),
        'formation_id' => $formation->getKey(),
        'status' => FormationProgressStatus::Completed,
        'progress_percentage' => 100,
        'started_at' => now()->subDays(5),
        'completed_at' => now(),
        'required_lessons_count' => 1,
        'completed_required_lessons_count' => 1,
    ]);

    $certificate = Certificate::query()->create([
        'member_id' => $member->getKey(),
        'formation_id' => $formation->getKey(),
        'member_formation_progress_id' => $progress->getKey(),
        'certificate_code' => 'CERT-20260411-TESTCODE',
        'issued_at' => now(),
        'pdf_path' => 'certificates/test.pdf',
        'verification_hash' => 'test-verification-hash-123',
    ]);

    $response = $this->get(route('certificates.verify', 'test-verification-hash-123'));

    $response->assertSuccessful();
    $response->assertSee('Joao Silva');
    $response->assertSee('Curso de Louvor');
    $response->assertSee('Certificado Verificado');
    $response->assertSee('CERT-20260411-TESTCODE');
});

it('returns 404 for invalid verification hash', function () {
    $response = $this->get(route('certificates.verify', 'invalid-hash'));

    $response->assertNotFound();
});
