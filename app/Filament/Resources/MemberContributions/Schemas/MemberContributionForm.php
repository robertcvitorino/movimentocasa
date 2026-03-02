<?php

namespace App\Filament\Resources\MemberContributions\Schemas;

use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Enums\PaymentMethod;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class MemberContributionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contribuição')
                    ->schema([
                        Select::make('member_id')
                            ->label('Membro')
                            ->relationship('member', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('reference_month')->label('Mês')->numeric()->minValue(1)->maxValue(12)->required(),
                        TextInput::make('reference_year')->label('Ano')->numeric()->minValue(2024)->required(),
                        Select::make('contribution_type')
                            ->label('Tipo')
                            ->options(collect(ContributionType::cases())->mapWithKeys(fn (ContributionType $type) => [$type->value => $type->label()]))
                            ->required(),
                        Select::make('payment_method')
                            ->label('Forma de pagamento')
                            ->options(collect(PaymentMethod::cases())->mapWithKeys(fn (PaymentMethod $method) => [$method->value => $method->label()]))
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options(collect(ContributionStatus::cases())->mapWithKeys(fn (ContributionStatus $status) => [$status->value => $status->label()]))
                            ->required(),
                        TextInput::make('expected_amount')->label('Valor esperado')->numeric(),
                        TextInput::make('declared_amount')->label('Valor informado')->numeric(),
                        FileUpload::make('receipt_path')
                            ->label('Comprovante')
                            ->directory('contributions/receipts')
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Observação')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
