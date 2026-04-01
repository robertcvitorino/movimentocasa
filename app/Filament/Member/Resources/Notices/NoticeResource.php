<?php

namespace App\Filament\Member\Resources\Notices;

use App\Filament\Member\Resources\Notices\Pages\ListNotices;
use App\Filament\Member\Resources\Notices\Tables\NoticesTable;
use App\Models\Notice;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NoticeResource extends Resource
{
    protected static ?string $model = Notice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|\UnitEnum|null $navigationGroup = 'Minha area';

    protected static ?string $navigationLabel = 'Mural de avisos';

    protected static ?string $slug = 'avisos';

    protected static ?string $modelLabel = 'Aviso';

    protected static ?string $pluralModelLabel = 'Mural de avisos';

    public static function table(Table $table): Table
    {
        return NoticesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotices::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->visibleToMember()
            ->with('creator')
            ->withCount(['likes', 'comments'])
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}

