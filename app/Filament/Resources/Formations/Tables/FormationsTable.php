<?php

namespace App\Filament\Resources\Formations\Tables;

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
                    ->badge(),
                IconColumn::make('is_required')
                    ->label('Obrigatoria')
                    ->boolean(),
                IconColumn::make('certificate_enabled')
                    ->label('Certificado')
                    ->boolean(),
                TextColumn::make('minimum_score')
                    ->label('Nota minima'),
                TextColumn::make('lessons_count')
                    ->label('Aulas')
                    ->counts('activeLessons'),
                IconColumn::make('has_quiz')
                    ->label('Prova')
                    ->boolean()
                    ->state(fn ($record): bool => $record->quiz()->exists()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Rascunho',
                        'published' => 'Publicada',
                        'archived' => 'Arquivada',
                    ]),
                SelectFilter::make('ministry_id')
                    ->label('Ministerio')
                    ->relationship('ministry', 'name'),
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
