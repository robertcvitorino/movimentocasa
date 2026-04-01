<?php

namespace App\Jobs;

use App\Models\Event;
use App\Services\EventNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendEventCreatedNotificationsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $eventId,
    ) {}

    public function handle(EventNotificationService $notificationService): void
    {
        $event = Event::query()
            ->with(['ministries.members.user', 'members.user'])
            ->find($this->eventId);

        if (! $event) {
            return;
        }

        $notificationService->notifyEventCreated($event);
    }
}
