<?php

namespace App\Filament\Resources\Formations\Tables;

use App\Enums\FormationStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FormationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ministry.name')
                    ->label('Ministerio')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(
                        fn (FormationStatus|string|null $state): string => match (true) {
                            $state instanceof FormationStatus => $state->label(),
                            is_string($state) => FormationStatus::tryFrom($state)?->label() ?? $state,
                            default => '-',
                        }
                    )
                    ->color(
                        fn (FormationStatus|string|null $state): string => match (true) {
                            $state instanceof FormationStatus => $state->color(),
                            is_string($state) => FormationStatus::tryFrom($state)?->color() ?? 'gray',
                            default => 'gray',
                        }
                    )
                    ->icon(
                        fn (FormationStatus|string|null $state): ?string => match (true) {
                            $state instanceof FormationStatus => $state->icon(),
                            is_string($state) => FormationStatus::tryFrom($state)?->icon(),
                            default => null,
                        }
                    )
                    ->badge(),

                IconColumn::make('is_required')
                    ->label('Obrigatoria')
                    ->boolean(),

                IconColumn::make('certificate_enabled')
                    ->label('Certificado')
                    ->boolean(),
                IconColumn::make('has_quiz')
                    ->label('Quiz')
                    ->boolean()
                    ->state(fn ($record): bool => $record->quiz()->exists()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(
                        collect(FormationStatus::cases())
                            ->mapWithKeys(fn (FormationStatus $status) => [$status->value => $status->label()])
                    ),
                SelectFilter::make('ministry_id')
                    ->label('Ministerio')
                    ->relationship('ministry', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
