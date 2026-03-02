<?php

namespace App\Filament\Resources\MemberContributions\Pages;

use App\Filament\Resources\MemberContributions\MemberContributionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMemberContribution extends EditRecord
{
    protected static string $resource = MemberContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
