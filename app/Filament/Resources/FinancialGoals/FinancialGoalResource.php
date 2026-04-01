<?php

namespace App\Filament\Resources\FinancialGoals;

use App\Enums\RoleName;
use App\Filament\Resources\FinancialGoals\Pages\CreateFinancialGoal;
use App\Filament\Resources\FinancialGoals\Pages\EditFinancialGoal;
use App\Filament\Resources\FinancialGoals\Pages\ListFinancialGoals;
use App\Filament\Resources\FinancialGoals\Schemas\FinancialGoalForm;
use App\Filament\Resources\FinancialGoals\Tables\FinancialGoalsTable;
use App\Models\FinancialGoal;
use App\Support\Queries\FinancialGoalVisibility;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FinancialGoalResource extends Resource
{
    protected static ?string $model = FinancialGoal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?string $modelLabel = 'Meta financeira';

    protected static ?string $pluralModelLabel = 'Metas financeiras';

    public static function form(Schema $schema): Schema
    {
        return FinancialGoalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinancialGoalsTable::configure($table);
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
            'index' => ListFinancialGoals::route('/'),
            'create' => CreateFinancialGoal::route('/create'),
            'edit' => EditFinancialGoal::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return FinancialGoalVisibility::forUser(parent::getEloquentQuery(), auth()->user());
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
