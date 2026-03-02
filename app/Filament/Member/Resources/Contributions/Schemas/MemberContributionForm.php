<?php

namespace App\Filament\Member\Resources\Contributions\Schemas;

use App\Enums\PaymentMethod;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberContributionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Declaracao de contribuicao')
                    ->schema([
                        TextInput::make('reference_month')->label('Mes')->disabled(),
                        TextInput::make('reference_year')->label('Ano')->disabled(),
                        TextInput::make('contribution_type')->label('Tipo')->disabled(),
                        TextInput::make('expected_amount')->label('Valor esperado')->numeric()->disabled(),
                        TextInput::make('declared_amount')->label('Valor informado')->numeric(),
                        Select::make('payment_method')
                            ->label('Forma de contribuicao')
                            ->options(collect(PaymentMethod::cases())->mapWithKeys(fn (PaymentMethod $method) => [$method->value => $method->label()])),
                        FileUpload::make('receipt_path')
                            ->label('Comprovante')
                            ->disk('public')
                            ->directory('contributions/receipts'),
                        Textarea::make('notes')
                            ->label('Observacao')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->extraAttributes([
                        'class' => 'py-2',
                    ])
                    ->columns(2),
            ]);
    }
}
