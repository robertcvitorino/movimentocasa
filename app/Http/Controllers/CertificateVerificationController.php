<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    public function __invoke(string $hash): View
    {
        $certificate = Certificate::query()
            ->where('verification_hash', $hash)
            ->with(['member', 'formation.ministry', 'formationProgress'])
            ->firstOrFail();

        return view('certificates.verify', [
            'certificate' => $certificate,
        ]);
    }
}
