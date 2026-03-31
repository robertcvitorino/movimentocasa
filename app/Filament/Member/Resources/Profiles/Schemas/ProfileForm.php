<?php

namespace App\Filament\Member\Resources\Profiles\Schemas;

use App\Enums\MemberStatus;
use App\Services\Integrations\ViaCepService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Acesso ao sistema')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('user_profile_photo_path')
                            ->label('Foto de perfil')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('users/profile-photos')
                            ->required(false),
                    ]),

                Section::make('Dados pessoais')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nome completo')
                            ->required()
                            ->columnSpan(1)
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->columns(1)
                            ->required()
                            ->maxLength(255),

                        DatePicker::make('birth_date')
                            ->columnSpan(1)
                            ->label('Data de nascimento'),

                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->prefix('@')
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->mask('(99) 99999-9999')
                            ->placeholder('(11) 99999-9999')
                            ->maxLength(30),

                        Toggle::make('is_whatsapp')
                            ->inline(false)
                            ->label('Usa WhatsApp'),
                    ])
                    ->extraAttributes([
                        'class' => 'py-2',
                    ]),

                Section::make('Endereco')
                    ->columnSpanFull()

                    ->schema([
                        TextInput::make('zip_code')
                            ->label('CEP')
                            ->columnSpan(1)
                            ->mask('99999-999')
                            ->maxLength(10)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if (blank($state)) {
                                    return;
                                }

                                try {
                                    $address = app(ViaCepService::class)->lookup($state);
                                } catch (\Throwable) {
                                    return;
                                }

                                if (! is_array($address)) {
                                    return;
                                }

                                $set('zip_code', $address['zip_code'] ?? $state);
                                $set('street', $address['street'] ?? null);
                                $set('district', $address['district'] ?? null);
                                $set('city', $address['city'] ?? null);
                                $set('state', $address['state'] ?? null);
                            }),
                        TextInput::make('street')
                            ->columnSpan(2)
                            ->label('Logradouro')
                            ->maxLength(255),

                        TextInput::make('number')
                            ->label('Numero')
                            ->columnSpan(1)
                            ->maxLength(20),

                        TextInput::make('complement')
                            ->label('Complemento')
                            ->columnSpan(1)
                            ->maxLength(255),

                        TextInput::make('district')
                            ->label('Bairro')
                            ->columnSpan(1)
                            ->maxLength(255),

                        TextInput::make('city')
                            ->label('Cidade')
                            ->columnSpan(1)
                            ->maxLength(255),

                        TextInput::make('state')
                            ->label('Estado')
                            ->columnSpan(1)
                            ->maxLength(2)
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? mb_strtoupper($state) : null),
                    ])
                    ->extraAttributes([
                        'class' => 'py-2',
                    ])
                    ->columns(4),

                Section::make('Dados internos')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->columnSpan(1)
                            ->options(collect(MemberStatus::cases())->mapWithKeys(fn (MemberStatus $status) => [
                                $status->value => $status->label(),
                            ]))
                            ->disabled(),

                        DatePicker::make('joined_at')
                            ->label('Entrada no grupo')
                            ->columnSpan(1)
                            ->disabled(),

                        Select::make('titles')
                            ->label('Sacramentos')
                            ->relationship('titles', 'name')
                            ->multiple()
                            ->columnSpan(1)
                            ->preload(),

                    ])
                    ->columns(3),
            ]);
    }
}
