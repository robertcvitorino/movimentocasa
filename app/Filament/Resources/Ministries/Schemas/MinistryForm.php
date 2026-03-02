<?php

namespace App\Filament\Resources\Ministries\Schemas;

use App\Enums\MinistryStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class MinistryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do ministério')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('status')
                            ->label('Status')
                            ->placeholder('Selecione uma opção')
                            ->options(collect(MinistryStatus::cases())->mapWithKeys(fn (MinistryStatus $status) => [$status->value => $status->label()]))
                            ->required(),

                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
                Section::make('Equipe')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        Select::make('members')
                            ->label('Membros')
                            ->relationship('members', 'full_name')
                            ->multiple()
                            ->columnSpan(2)
                            ->preload(),
                        Select::make('coordinators')
                            ->label('Coordenadores')
                            ->relationship('coordinators', 'full_name')
                            ->multiple()
                            ->columnSpan(2)
                            ->preload(),
                    ]),
            ]);
    }
}
