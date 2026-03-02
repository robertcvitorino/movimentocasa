<?php

namespace App\Filament\Member\Resources\Profiles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados pessoais')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nome completo')
                            ->disabled(),
                        DatePicker::make('birth_date')
                            ->label('Data de nascimento'),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->disabled(),
                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel(),
                        Toggle::make('is_whatsapp')
                            ->label('Usa WhatsApp'),
                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->prefix('@'),
                    ])
                    ->extraAttributes([
                        'class' => 'py-2',
                    ])
                    ->columns(2),
                Section::make('Endereco')
                    ->schema([
                        TextInput::make('zip_code')->label('CEP'),
                        TextInput::make('street')->label('Logradouro'),
                        TextInput::make('number')->label('Numero'),
                        TextInput::make('complement')->label('Complemento'),
                        TextInput::make('district')->label('Bairro'),
                        TextInput::make('city')->label('Cidade'),
                        TextInput::make('state')->label('Estado'),
                    ])
                    ->extraAttributes([
                        'class' => 'py-2',
                    ])
                    ->columns(2),
            ]);
    }
}
