<?php

namespace App\Filament\Member\Resources\Profiles\Pages;

use App\Filament\Member\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\ListRecords;

class ListProfiles extends ListRecords
{
    protected static string $resource = ProfileResource::class;
}
