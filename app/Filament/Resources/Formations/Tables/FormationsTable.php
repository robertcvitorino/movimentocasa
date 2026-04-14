<?php

namespace App\Filament\Resources\Formations\Tables;

use App\Enums\FormationProgressStatus;
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
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('ministry.name')
                    ->label('Ministerio')
                    ->searchable()
                    ->placeholder('-')
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
                    ->label('Obrigatória')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                IconColumn::make('certificate_enabled')
                    ->label('Certificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-academic-cap')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                IconColumn::make('quiz_enabled')
                    ->label('Quiz')
                    ->boolean()
                    ->trueIcon('heroicon-o-clipboard-document-list')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('info')
                    ->falseColor('gray'),

                TextColumn::make('workload_hours')
                    ->label('Carga Horária')
                    ->suffix('h')
                    ->numeric(decimalPlaces: 0)
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('lessons_count')
                    ->label('Aulas')
                    ->counts('activeLessons')
                    ->icon('heroicon-o-play-circle')
                    ->iconColor('primary'),

                TextColumn::make('completions_count')
                    ->label('Progresso')
                    ->badge()
                    ->icon('heroicon-o-users')
                    ->color('success')
                    ->state(function ($record): string {
                        $total = $record->progress()->count();
                        $completed = $record->progress()
                            ->where('status', FormationProgressStatus::Completed->value)
                            ->count();

                        if ($total === 0) {
                            return 'Sem membros';
                        }

                        return "{$completed}/{$total} concluíram";
                    })
                    ->toggleable(),
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
