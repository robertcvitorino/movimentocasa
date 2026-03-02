<?php

namespace App\Filament\Resources\Ministries;

use App\Enums\RoleName;
use App\Filament\Resources\Ministries\Pages\CreateMinistry;
use App\Filament\Resources\Ministries\Pages\EditMinistry;
use App\Filament\Resources\Ministries\Pages\ListMinistries;
use App\Filament\Resources\Ministries\Schemas\MinistryForm;
use App\Filament\Resources\Ministries\Tables\MinistriesTable;
use App\Models\Ministry;
use App\Support\Queries\MinistryVisibility;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MinistryResource extends Resource
{
    protected static ?string $model = Ministry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Cadastro';

    protected static ?string $modelLabel = 'Ministério';

    protected static ?string $pluralModelLabel = 'Ministérios';

    public static function form(Schema $schema): Schema
    {
        return MinistryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MinistriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMinistries::route('/'),
            'create' => CreateMinistry::route('/create'),
            'edit' => EditMinistry::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return MinistryVisibility::forUser(parent::getEloquentQuery(), auth()->user());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole([
            RoleName::SystemAdmin->value,
            RoleName::GeneralCoordinator->value,
            RoleName::MinistryCoordinator->value,
        ]) ?? false;
    }
}
