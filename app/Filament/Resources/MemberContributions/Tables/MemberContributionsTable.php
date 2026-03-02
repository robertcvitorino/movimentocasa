<?php

namespace App\Filament\Resources\MemberContributions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MemberContributionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.full_name')->label('Membro')->searchable(),
                TextColumn::make('reference_month')->label('Mês'),
                TextColumn::make('reference_year')->label('Ano'),
                TextColumn::make('contribution_type')->label('Tipo')->badge(),
                TextColumn::make('payment_method')->label('Pagamento')->badge(),
                TextColumn::make('declared_amount')->label('Valor')->money('BRL'),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'declared' => 'Declarada',
                        'confirmed' => 'Confirmada',
                        'cancelled' => 'Cancelada',
                    ]),
                SelectFilter::make('contribution_type')
                    ->label('Tipo')
                    ->options([
                        'tithe' => 'Dízimo',
                        'offering' => 'Oferta',
                        'donation' => 'Doação',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
