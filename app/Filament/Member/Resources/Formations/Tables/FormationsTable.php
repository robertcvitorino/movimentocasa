<?php

namespace App\Filament\Member\Resources\Formations\Tables;

use App\Enums\FormationProgressStatus;
use App\Filament\Member\Resources\Formations\FormationResource;
use App\Models\Formation;
use App\Models\MemberFormationProgress;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FormationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Formação')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('ministry.name')
                    ->label('Ministério')
                    ->placeholder('-'),

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
                    ->placeholder('-'),


                TextColumn::make('progress_label')
                    ->label('Progresso')
                    ->badge()
                    ->icon(function (Formation $record): string {
                        $status = self::getProgressStatus($record);

                        return match ($status) {
                            FormationProgressStatus::Completed  => 'heroicon-o-check-circle',
                            FormationProgressStatus::InProgress => 'heroicon-o-play-circle',
                            FormationProgressStatus::Failed     => 'heroicon-o-x-circle',
                            FormationProgressStatus::Blocked    => 'heroicon-o-lock-closed',
                            default                             => 'heroicon-o-clock',
                        };
                    })
                    ->color(function (Formation $record): string {
                        $status = self::getProgressStatus($record);

                        return match ($status) {
                            FormationProgressStatus::Completed  => 'success',
                            FormationProgressStatus::InProgress => 'info',
                            FormationProgressStatus::Failed     => 'danger',
                            FormationProgressStatus::Blocked    => 'warning',
                            default                             => 'gray',
                        };
                    })
                    ->state(function (Formation $record): string {
                        $progress = self::getProgress($record);

                        if (! $progress) {
                            return 'Não iniciada';
                        }

                        return sprintf(
                            '%d%% — %s',
                            (int) $progress->progress_percentage,
                            $progress->status->label(),
                        );
                    }),
            ])
            ->recordActions([
                Action::make('attend')
                    ->label('Abrir')
                    ->url(fn (Formation $record): string => FormationResource::getUrl('attend', ['record' => $record])),
            ]);
    }

    private static function getProgress(Formation $record): ?MemberFormationProgress
    {
        $memberId = auth()->user()?->member?->getKey();

        if (! $memberId) {
            return null;
        }

        return MemberFormationProgress::query()
            ->where('member_id', $memberId)
            ->where('formation_id', $record->getKey())
            ->first();
    }

    private static function getProgressStatus(Formation $record): FormationProgressStatus
    {
        return self::getProgress($record)?->status ?? FormationProgressStatus::NotStarted;
    }
}
