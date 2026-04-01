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

        if (isset($data['quiz']) && is_array($data['quiz'])) {
            $quizIsActive = filter_var($data['quiz']['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if (! $quizIsActive) {
                unset($data['quiz']);
            }
        }

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
