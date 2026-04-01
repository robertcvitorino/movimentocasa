<?php

namespace App\Filament\Resources\Ministries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MinistriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                TextColumn::make('coordinators.full_name')
                    ->label('Coordenadores')
                    ->listWithLineBreaks()
                    ->limitList(2),
                TextColumn::make('members_count')
                    ->label('Membros')
                    ->alignCenter()
                    ->counts('members'),
            ])
            ->filters([
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([

            ]);
    }
}
