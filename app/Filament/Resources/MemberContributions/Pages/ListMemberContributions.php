<?php

namespace App\Filament\Resources\MemberContributions\Pages;

use App\Filament\Resources\MemberContributions\MemberContributionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMemberContributions extends ListRecords
{
    protected static string $resource = MemberContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
