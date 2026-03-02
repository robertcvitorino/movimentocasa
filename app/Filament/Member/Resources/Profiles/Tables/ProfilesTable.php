<?php

namespace App\Filament\Member\Resources\Profiles\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->label('Nome'),
                TextColumn::make('email')->label('E-mail'),
                TextColumn::make('phone')->label('Telefone'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
