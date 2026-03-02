<?php

namespace App\Filament\Member\Resources\Contributions;

use App\Filament\Member\Resources\Contributions\Pages\EditMemberContribution;
use App\Filament\Member\Resources\Contributions\Pages\ListMemberContributions;
use App\Filament\Member\Resources\Contributions\Schemas\MemberContributionForm;
use App\Filament\Member\Resources\Contributions\Tables\MemberContributionsTable;
use App\Models\MemberContribution;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MemberContributionResource extends Resource
{
    protected static ?string $model = MemberContribution::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Minha area';

    protected static ?string $navigationLabel = 'Contribuicoes';

    public static function form(Schema $schema): Schema
    {
        return MemberContributionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MemberContributionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMemberContributions::route('/'),
            'edit' => EditMemberContribution::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('member_id', auth()->user()?->member?->getKey());
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
