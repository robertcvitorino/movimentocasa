<?php

namespace App\Filament\Member\Resources\Contributions\Pages;

use App\Enums\ContributionStatus;
use App\Filament\Member\Resources\Contributions\MemberContributionResource;
use Filament\Resources\Pages\EditRecord;

class EditMemberContribution extends EditRecord
{
    protected static string $resource = MemberContributionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = ContributionStatus::Declared;
        $data['declared_at'] = now();

        return $data;
    }
}
