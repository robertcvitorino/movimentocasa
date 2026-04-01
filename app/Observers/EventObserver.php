<?php

namespace App\Observers;

use App\Jobs\SendEventCreatedNotificationsJob;
use App\Models\Event;

class EventObserver
{
    public function created(Event $event): void
    {
        SendEventCreatedNotificationsJob::dispatch($event->getKey());
    }
}
