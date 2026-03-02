<?php

namespace App\Filament\Member\Resources\Certificates;

use App\Filament\Member\Resources\Certificates\Pages\ListCertificates;
use App\Filament\Member\Resources\Certificates\Tables\CertificatesTable;
use App\Models\Certificate;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Minha area';

    protected static ?string $navigationLabel = 'Certificados';

    public static function table(Table $table): Table
    {
        return CertificatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertificates::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('member_id', auth()->user()?->member?->getKey())
            ->with('formation.ministry');
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
