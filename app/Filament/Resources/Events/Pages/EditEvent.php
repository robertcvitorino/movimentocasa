<?php

namespace App\Filament\Resources\Events\Pages;

use App\Enums\EventAudienceType;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Pages\Concerns\InteractsWithEventAudience;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    use InteractsWithEventAudience;

    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing(['ministries', 'members']);

        $audienceType = EventAudienceType::General;

        if ($this->record->ministries->isNotEmpty()) {
            $audienceType = EventAudienceType::Ministry;
        } elseif ($this->record->members->isNotEmpty()) {
            $audienceType = EventAudienceType::Members;
        }

        $data['audience_type'] = $audienceType->value;
        $data['ministry_ids'] = $this->record->ministries->pluck('id')->all();
        $data['member_ids'] = $this->record->members->pluck('id')->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->extractAudienceData($data);
    }

    protected function afterSave(): void
    {
        $this->syncAudience($this->record);
    }
}
