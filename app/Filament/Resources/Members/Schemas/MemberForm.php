<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Enums\MemberStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Acesso ao sistema')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('user_profile_photo_path')
                            ->label('Foto de perfil')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('users/profile-photos')
                            ->required(false)
                            ->helperText('Opcional')
                            ->columnSpanFull(),
                        TextInput::make('full_name')
                            ->label('Nome completo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->columnSpan(2)
                            ->unique(ignoreRecord: true),

                        Toggle::make('user_is_active')
                            ->label('Permitir acesso ao sistema')
                            ->default(true),
                    ]),
                Section::make('Dados pessoais')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        DatePicker::make('birth_date')
                            ->label('Data de nascimento'),
                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(30),
                        Toggle::make('is_whatsapp')
                            ->label('E WhatsApp?'),
                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->prefix('@'),
                    ]),
                Section::make('Endereco')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('zip_code')->label('CEP')->maxLength(10),
                        TextInput::make('street')->label('Rua')->columnSpan(2),
                        TextInput::make('number')->label('Numero')->maxLength(20),
                        TextInput::make('complement')->label('Complemento'),
                        TextInput::make('district')->label('Bairro'),
                        TextInput::make('city')->label('Cidade'),
                        TextInput::make('state')->label('Estado')->maxLength(2),
                    ]),
                Section::make('Dados internos')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(collect(MemberStatus::cases())->mapWithKeys(fn (MemberStatus $status) => [$status->value => $status->label()]))
                            ->default(MemberStatus::Active->value)
                            ->required(),

                        DatePicker::make('joined_at')
                            ->label('Entrada no grupo')
                            ->default(Carbon::today()),
                        Select::make('titles')
                            ->label('Sacramentos')
                            ->relationship('titles', 'name')
                            ->multiple()
                            ->required()
                            ->minItems(1)
                            ->preload(),

                        Select::make('ministries')
                            ->label('Ministerios')
                            ->relationship('ministries', 'name')
                            ->multiple()
                            ->preload(),
                    ]),
            ]);
    }
}
