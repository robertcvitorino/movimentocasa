<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Enums\MemberStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados pessoais')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nome completo')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('birth_date')
                            ->label('Data de nascimento'),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(30),
                        Toggle::make('is_whatsapp')
                            ->label('É WhatsApp?'),
                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->prefix('@'),
                    ])
                    ->columns(2),
                Section::make('Endereço')
                    ->schema([
                        TextInput::make('zip_code')->label('CEP')->maxLength(10),
                        TextInput::make('street')->label('Logradouro'),
                        TextInput::make('number')->label('Número')->maxLength(20),
                        TextInput::make('complement')->label('Complemento'),
                        TextInput::make('district')->label('Bairro'),
                        TextInput::make('city')->label('Cidade'),
                        TextInput::make('state')->label('Estado')->maxLength(2),
                    ])
                    ->columns(2),
                Section::make('Dados internos')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(collect(MemberStatus::cases())->mapWithKeys(fn (MemberStatus $status) => [$status->value => $status->label()]))
                            ->required(),
                        DatePicker::make('joined_at')->label('Entrada no grupo'),
                        Select::make('titles')
                            ->label('Títulos sacramentais')
                            ->relationship('titles', 'name')
                            ->multiple()
                            ->preload(),
                        Select::make('ministries')
                            ->label('Ministérios')
                            ->relationship('ministries', 'name')
                            ->multiple()
                            ->preload(),
                        Textarea::make('internal_notes')
                            ->label('Observações internas')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
