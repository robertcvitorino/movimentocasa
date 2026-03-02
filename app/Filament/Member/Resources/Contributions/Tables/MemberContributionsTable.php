<?php

namespace App\Filament\Member\Resources\Contributions\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MemberContributionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_month')->label('Mes'),
                TextColumn::make('reference_year')->label('Ano'),
                TextColumn::make('contribution_type')->label('Tipo')->badge(),
                TextColumn::make('declared_amount')->label('Valor')->money('BRL'),
                TextColumn::make('payment_method')->label('Forma')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
