<?php

namespace App\Filament\Resources\FinancialGoals\Schemas;

use App\Enums\FinancialGoalStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class FinancialGoalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Meta')
                    ->schema([
                        TextInput::make('title')->label('Título')->required(),
                        TextInput::make('target_amount')->label('Valor alvo')->numeric()->required(),
                        TextInput::make('month')->label('Mês')->numeric()->minValue(1)->maxValue(12)->required(),
                        TextInput::make('year')->label('Ano')->numeric()->minValue(2024)->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options(collect(FinancialGoalStatus::cases())->mapWithKeys(fn (FinancialGoalStatus $status) => [$status->value => $status->label()]))
                            ->required(),
                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
