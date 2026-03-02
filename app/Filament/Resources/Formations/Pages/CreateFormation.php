<?php

namespace App\Filament\Resources\Formations\Pages;

use App\Filament\Resources\Formations\FormationResource;
use App\Filament\Resources\Formations\Schemas\FormationForm;
use Filament\Resources\Pages\CreateRecord;

class CreateFormation extends CreateRecord
{
    protected static string $resource = FormationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = FormationForm::normalizeLessonsFormData($data);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
