<?php

namespace App\Filament\Resources\Formations\Pages;

use App\Filament\Resources\Formations\FormationResource;
use App\Filament\Resources\Formations\Schemas\FormationForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFormation extends EditRecord
{
    protected static string $resource = FormationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = FormationForm::normalizeLessonsFormData($data);
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
