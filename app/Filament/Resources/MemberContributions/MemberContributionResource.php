<?php

namespace App\Filament\Resources\MemberContributions;

use App\Enums\RoleName;
use App\Filament\Resources\MemberContributions\Pages\CreateMemberContribution;
use App\Filament\Resources\MemberContributions\Pages\EditMemberContribution;
use App\Filament\Resources\MemberContributions\Pages\ListMemberContributions;
use App\Filament\Resources\MemberContributions\Schemas\MemberContributionForm;
use App\Filament\Resources\MemberContributions\Tables\MemberContributionsTable;
use App\Models\MemberContribution;
use App\Support\Queries\MemberContributionVisibility;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MemberContributionResource extends Resource
{
    protected static ?string $model = MemberContribution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?string $modelLabel = 'Contribuição';

    protected static ?string $pluralModelLabel = 'Contribuições';

    public static function form(Schema $schema): Schema
    {
        return MemberContributionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MemberContributionsTable::configure($table);
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
            'index' => ListMemberContributions::route('/'),
            'create' => CreateMemberContribution::route('/create'),
            'edit' => EditMemberContribution::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return MemberContributionVisibility::forUser(parent::getEloquentQuery(), auth()->user());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole([
            RoleName::SystemAdmin->value,
            RoleName::GeneralCoordinator->value,
            RoleName::FinancialCoordinator->value,
        ]) ?? false;
    }
}
