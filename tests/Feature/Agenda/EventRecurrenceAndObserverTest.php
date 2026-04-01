<?php

use App\Enums\EventRecurrenceType;
use App\Jobs\SendEventCreatedNotificationsJob;
use App\Models\Event;
use App\Services\EventRecurrenceService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Queue;

it('expands recurring events inside the requested period', function () {
    $event = Event::factory()->create([
        'start_datetime' => CarbonImmutable::parse('2026-04-01 19:00:00'),
        'end_datetime' => CarbonImmutable::parse('2026-04-01 21:00:00'),
        'is_recurring' => true,
        'recurrence_type' => EventRecurrenceType::Weekly,
        'recurrence_until' => '2026-04-30',
    ]);

    $occurrences = app(EventRecurrenceService::class)->occurrencesInRange(
        $event,
        CarbonImmutable::parse('2026-04-01 00:00:00'),
        CarbonImmutable::parse('2026-04-30 23:59:59'),
    );

    expect($occurrences)->toHaveCount(5);
});

it('dispatches event notification job when event is created', function () {
    Queue::fake();

    $event = Event::factory()->create();

    Queue::assertPushed(SendEventCreatedNotificationsJob::class, function (SendEventCreatedNotificationsJob $job) use ($event): bool {
        return $job->eventId === $event->getKey();
    });
});
