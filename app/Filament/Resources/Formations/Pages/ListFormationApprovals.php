<?php

namespace App\Filament\Resources\Formations\Pages;

use App\Filament\Resources\Formations\FormationApprovalResource;
use Filament\Resources\Pages\ListRecords;

class ListFormationApprovals extends ListRecords
{
    protected static string $resource = FormationApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
