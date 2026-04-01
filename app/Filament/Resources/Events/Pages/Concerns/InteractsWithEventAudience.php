<?php

namespace App\Filament\Resources\Events\Pages\Concerns;

use App\Enums\EventAudienceType;
use App\Filament\Support\AgendaEventForm;
use App\Models\Event;

trait InteractsWithEventAudience
{
    /**
     * @var array{audience_type: EventAudienceType, ministry_ids: array<int, int>, member_ids: array<int, int>}
     */
    protected array $audienceData = [
        'audience_type' => EventAudienceType::General,
        'ministry_ids' => [],
        'member_ids' => [],
    ];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function extractAudienceData(array $data): array
    {
        $this->audienceData = AgendaEventForm::extractAudienceData($data);

        return $data;
    }

    protected function syncAudience(Event $event): void
    {
        $event->syncAudience(
            $this->audienceData['audience_type'],
            $this->audienceData['ministry_ids'],
            $this->audienceData['member_ids'],
        );
    }
}
