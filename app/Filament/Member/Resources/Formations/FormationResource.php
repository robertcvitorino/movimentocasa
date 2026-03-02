<?php

namespace App\Filament\Member\Resources\Formations;

use App\Filament\Member\Resources\Formations\Pages\AttendFormation;
use App\Filament\Member\Resources\Formations\Pages\ListFormations;
use App\Filament\Member\Resources\Formations\Tables\FormationsTable;
use App\Models\Formation;
use App\Support\Queries\FormationVisibility;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FormationResource extends Resource
{
    protected static ?string $model = Formation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Minha area';

    protected static ?string $navigationLabel = 'Minhas formacoes';

    protected static ?string $modelLabel = 'Formacao';

    protected static ?string $pluralModelLabel = 'Formacoes';

    public static function table(Table $table): Table
    {
        return FormationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormations::route('/'),
            'attend' => AttendFormation::route('/{record}/attend'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return FormationVisibility::forUser(parent::getEloquentQuery(), auth()->user(), management: false)
            ->with(['lessons', 'quiz', 'ministry']);
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
