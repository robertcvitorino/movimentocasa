<?php

namespace App\Filament\Member\Resources\Certificates\Pages;

use App\Filament\Member\Resources\Certificates\CertificateResource;
use Filament\Resources\Pages\ListRecords;

class ListCertificates extends ListRecords
{
    protected static string $resource = CertificateResource::class;
}
