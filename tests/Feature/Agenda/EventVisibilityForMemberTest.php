<?php

use App\Enums\MemberMinistryStatus;
use App\Models\Event;
use App\Models\Member;
use App\Models\Ministry;

it('shows general, ministry and direct events for the member scope', function () {
    $member = Member::factory()->create();
    $memberMinistry = Ministry::factory()->create();
    $otherMinistry = Ministry::factory()->create();

    $memberMinistry->members()->attach($member->getKey(), [
        'status' => MemberMinistryStatus::Active->value,
    ]);

    $generalEvent = Event::factory()->create(['title' => 'Evento geral']);
    $ministryEvent = Event::factory()->create(['title' => 'Evento ministerio']);
    $directEvent = Event::factory()->create(['title' => 'Evento direto']);
    $hiddenEvent = Event::factory()->create(['title' => 'Evento oculto']);

    $ministryEvent->ministries()->attach($memberMinistry->getKey());
    $directEvent->members()->attach($member->getKey());
    $hiddenEvent->ministries()->attach($otherMinistry->getKey());

    $visibleTitles = Event::query()
        ->visibleToMember($member)
        ->pluck('title')
        ->all();

    expect($visibleTitles)->toContain($generalEvent->title, $ministryEvent->title, $directEvent->title);
    expect($visibleTitles)->not->toContain($hiddenEvent->title);
});
