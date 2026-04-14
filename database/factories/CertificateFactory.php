<?php

namespace Database\Factories;

use App\Models\Formation;
use App\Models\Member;
use App\Models\MemberFormationProgress;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = 'CERT-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));

        return [
            'member_id' => Member::factory(),
            'formation_id' => Formation::factory(),
            'member_formation_progress_id' => MemberFormationProgress::factory(),
            'certificate_code' => $code,
            'issued_at' => now(),
            'pdf_path' => 'certificates/1/'.Str::slug($code).'.pdf',
            'verification_hash' => hash('sha256', $code.'|'.Str::uuid()),
        ];
    }
}
