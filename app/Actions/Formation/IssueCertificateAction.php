<?php

namespace App\Actions\Formation;

use App\Models\Certificate;
use App\Models\MemberFormationProgress;
use App\Services\QrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IssueCertificateAction
{
    public function __construct(
        protected QrCodeService $qrCodeService,
    ) {}

    public function execute(MemberFormationProgress $progress): Certificate
    {
        return DB::transaction(function () use ($progress): Certificate {
            $progress->load(['member', 'formation.ministry', 'certificate']);

            if ($progress->certificate) {
                return $progress->certificate;
            }

            $certificateCode = $this->generateCertificateCode();
            $issuedAt = now();
            $filePath = sprintf(
                'certificates/%s/%s.pdf',
                $progress->member_id,
                Str::slug($certificateCode),
            );

            $verificationUrl = route('certificate.verify', $certificateCode);
            $qrCodeDataUri = $this->qrCodeService->generateBase64Png($verificationUrl);

            $pdf = Pdf::loadView('pdf.certificate', [
                'certificateCode' => $certificateCode,
                'issuedAt' => $issuedAt,
                'member' => $progress->member,
                'formation' => $progress->formation,
                'progress' => $progress,
                'qrCodeDataUri' => $qrCodeDataUri,
            ])->setPaper('a4', 'landscape');

            Storage::disk('public')->put($filePath, $pdf->output());

            $certificate = Certificate::query()->create([
                'member_id' => $progress->member_id,
                'formation_id' => $progress->formation_id,
                'member_formation_progress_id' => $progress->getKey(),
                'certificate_code' => $certificateCode,
                'issued_at' => $issuedAt,
                'pdf_path' => $filePath,
                'verification_hash' => hash('sha256', $certificateCode.'|'.Str::uuid()),
            ]);

            $progress->forceFill([
                'certificate_issued_at' => $issuedAt,
            ])->save();

            $progress->setRelation('certificate', $certificate);

            return $certificate;
        });
    }

    protected function generateCertificateCode(): string
    {
        do {
            $code = 'CERT-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (Certificate::query()->where('certificate_code', $code)->exists());

        return $code;
    }
}
