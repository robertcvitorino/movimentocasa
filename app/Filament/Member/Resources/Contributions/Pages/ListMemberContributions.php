<?php

namespace App\Filament\Member\Resources\Contributions\Pages;

use App\Filament\Member\Resources\Contributions\MemberContributionResource;
use Filament\Resources\Pages\ListRecords;

class ListMemberContributions extends ListRecords
{
    protected static string $resource = MemberContributionResource::class;
}
