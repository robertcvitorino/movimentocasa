<?php

namespace App\Services;

use App\Models\Event;
use App\Notifications\EventInvitationNotification;

class EventNotificationService
{
    public function __construct(
        protected readonly EventAudienceResolver $audienceResolver,
    ) {}

    public function notifyEventCreated(Event $event): void
    {
        $users = $this->audienceResolver->resolveUsers($event);

        foreach ($users as $user) {
            $user->notify(new EventInvitationNotification($event));
        }
    }
}
