<?php

namespace App\Filament\Member\Resources\Profiles;

use App\Filament\Member\Resources\Profiles\Pages\EditProfile;
use App\Filament\Member\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Member\Resources\Profiles\Schemas\ProfileForm;
use App\Filament\Member\Resources\Profiles\Tables\ProfilesTable;
use App\Models\Member;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProfileResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Minha area';

    protected static ?string $navigationLabel = 'Meu perfil';

    protected static ?string $modelLabel = 'Perfil';

    protected static ?string $pluralModelLabel = 'Perfil';

    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfilesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProfiles::route('/'),
            'edit' => EditProfile::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $memberId = auth()->user()?->member?->getKey();

        return parent::getEloquentQuery()->whereKey($memberId);
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
