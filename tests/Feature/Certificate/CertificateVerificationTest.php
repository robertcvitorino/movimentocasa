<?php

use App\Models\Certificate;
use App\Models\Formation;
use App\Models\Member;
use App\Models\MemberFormationProgress;
use App\Models\Ministry;

it('displays valid certificate info when code exists', function () {
    $member = Member::factory()->create(['full_name' => 'Joao Silva']);
    $ministry = Ministry::factory()->create(['name' => 'Louvor']);
    $formation = Formation::factory()->create([
        'title' => 'Musicalizacao',
        'ministry_id' => $ministry->getKey(),
        'workload_hours' => 16,
    ]);

    $progress = MemberFormationProgress::factory()->create([
        'member_id' => $member->getKey(),
        'formation_id' => $formation->getKey(),
        'quiz_score' => 92.50,
        'completed_at' => now(),
    ]);

    $certificate = Certificate::factory()->create([
        'member_id' => $member->getKey(),
        'formation_id' => $formation->getKey(),
        'member_formation_progress_id' => $progress->getKey(),
        'certificate_code' => 'CERT-20260413-TESTCODE',
        'issued_at' => now(),
    ]);

    $this->get(route('certificate.verify', 'CERT-20260413-TESTCODE'))
        ->assertOk()
        ->assertSee('Certificado Válido')
        ->assertSee('Joao Silva')
        ->assertSee('Musicalizacao')
        ->assertSee('Louvor')
        ->assertSee('CERT-20260413-TESTCODE');
});

it('displays not found when certificate code is invalid', function () {
    $this->get(route('certificate.verify', 'INVALID-CODE'))
        ->assertOk()
        ->assertSee('Certificado Não Encontrado');
});
