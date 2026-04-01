<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_datetime')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('end_datetime')
                    ->label('Fim')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Local')
                    ->toggleable(),
                TextColumn::make('audience')
                    ->label('Destinatarios')
                    ->state(fn ($record): string => $record->resolveAudienceType()->label()),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
