<?php

namespace App\Http\Controllers;

use App\Models\Certificate;

class CertificateVerificationController extends Controller
{
    public function __invoke(string $code)
    {
        $certificate = Certificate::query()
            ->with(['member', 'formation.ministry', 'formationProgress'])
            ->where('certificate_code', $code)
            ->first();

        return view('certificate.verify', [
            'certificate' => $certificate,
        ]);
    }
}
