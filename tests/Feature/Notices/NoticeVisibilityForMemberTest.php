<?php

use App\Models\Notice;

it('shows only published and already released notices for members', function () {
    $visibleNotice = Notice::factory()->create([
        'title' => 'Aviso visivel',
        'is_published' => true,
        'published_at' => now()->subMinute(),
        'expires_at' => now()->addDay(),
    ]);

    $draftNotice = Notice::factory()->draft()->create([
        'title' => 'Aviso rascunho',
    ]);

    $futureNotice = Notice::factory()->create([
        'title' => 'Aviso futuro',
        'is_published' => true,
        'published_at' => now()->addHour(),
    ]);

    $expiredNotice = Notice::factory()->create([
        'title' => 'Aviso expirado',
        'is_published' => true,
        'published_at' => now()->subDay(),
        'expires_at' => now()->subMinute(),
    ]);

    $titles = Notice::query()
        ->visibleToMember()
        ->pluck('title')
        ->all();

    expect($titles)->toContain($visibleNotice->title);
    expect($titles)->not->toContain($draftNotice->title);
    expect($titles)->not->toContain($futureNotice->title);
    expect($titles)->toContain($expiredNotice->title);
});
