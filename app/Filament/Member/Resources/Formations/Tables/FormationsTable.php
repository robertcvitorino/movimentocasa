<?php

namespace App\Filament\Member\Resources\Formations\Tables;

use App\Filament\Member\Resources\Formations\FormationResource;
use App\Models\Formation;
use App\Models\MemberFormationProgress;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FormationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Formacao')
                    ->searchable(),
                TextColumn::make('ministry.name')
                    ->label('Ministerio'),
                TextColumn::make('lessons_count')
                    ->label('Aulas')
                    ->counts('activeLessons'),
                TextColumn::make('progress_label')
                    ->label('Progresso')
                    ->state(function (Formation $record): string {
                        $memberId = auth()->user()?->member?->getKey();
                        $progress = MemberFormationProgress::query()
                            ->where('member_id', $memberId)
                            ->where('formation_id', $record->getKey())
                            ->first();

                        if (! $progress) {
                            return 'Nao iniciada';
                        }

                        return sprintf('%s%% - %s', $progress->progress_percentage, $progress->status->label());
                    }),
            ])
            ->recordActions([
                Action::make('attend')
                    ->label('Abrir')
                    ->url(fn (Formation $record): string => FormationResource::getUrl('attend', ['record' => $record])),
            ]);
    }
}
