<?php

namespace App\Filament\Member\Resources\Events\Tables;

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
                    ->searchable(),
                TextColumn::make('start_datetime')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('end_datetime')
                    ->label('Fim')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Local'),
            ])
            ->defaultSort('start_datetime');
    }
}
