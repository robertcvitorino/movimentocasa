<?php

namespace App\Filament\Member\Resources\Events;

use App\Filament\Member\Resources\Events\Pages\ListEvents;
use App\Filament\Member\Resources\Events\Tables\EventsTable;
use App\Models\Event;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Minha area';

    protected static ?string $navigationLabel = 'Agenda';

    protected static ?string $slug = 'agenda';

    protected static ?string $modelLabel = 'Evento';

    protected static ?string $pluralModelLabel = 'Agenda';

    public static function table(Table $table): Table
    {
        return EventsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['ministries', 'members']);

        $member = auth()->user()?->member;

        if (! $member) {
            return $query->whereRaw('1 = 0');
        }

        return $query->visibleToMember($member);
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
