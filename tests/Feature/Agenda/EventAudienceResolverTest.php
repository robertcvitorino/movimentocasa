<?php

use App\Models\Event;
use App\Models\Member;
use App\Models\Ministry;
use App\Services\EventAudienceResolver;

it('resolves users from ministries and specific members without duplicates', function () {
    $event = Event::factory()->create();

    $ministry = Ministry::factory()->create();
    $memberFromMinistry = Member::factory()->create();
    $memberSpecific = Member::factory()->create();

    $ministry->members()->attach($memberFromMinistry->getKey(), ['status' => 'active']);

    $event->ministries()->attach($ministry->getKey());
    $event->members()->attach([
        $memberFromMinistry->getKey(),
        $memberSpecific->getKey(),
    ]);

    $users = app(EventAudienceResolver::class)->resolveUsers($event);

    expect($users)->toHaveCount(2);
    expect($users->pluck('id')->all())->toContain($memberFromMinistry->user_id, $memberSpecific->user_id);
});
