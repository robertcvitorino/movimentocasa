<?php

namespace App\Filament\Resources\Formations\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizAttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'quizAttempts';

    protected static ?string $title = 'Pontuação do Quiz';

    protected static ?string $label = 'tentativa';

    protected static ?string $pluralLabel = 'tentativas';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('attempt_number')
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                TextColumn::make('member.full_name')
                    ->label('Participante')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('score')
                    ->label('Nota')
                    ->formatStateUsing(fn (?string $state): string => $state !== null ? number_format((float) $state, 2, ',', '.') . '%' : '-')
                    ->sortable(),

                TextColumn::make('attempt_number')
                    ->label('Tentativa')
                    ->sortable(),

                TextColumn::make('submitted_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }
}
