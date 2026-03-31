<?php

namespace App\Filament\Member\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Models\SacramentalTitle;
use App\Notifications\MemberVerifyEmailNotification;
use App\Services\Integrations\ViaCepService;
use Filament\Actions\Action;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use LogicException;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 4,
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome completo')
                            ->required()
                            ->columnSpan(4)
                            ->maxLength(255)
                            ->autofocus(),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->columnSpan(4)
                            ->maxLength(255),

                        DatePicker::make('birth_date')
                            ->label('Data de nascimento')
                            ->required()
                            ->columnSpan(2)
                            ->maxDate(now()),



                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->columnSpan(2)
                            ->required()
                            ->maxLength(255)
                            ->placeholder('@seuperfil'),

                        TextInput::make('phone')
                            ->label('Telefone')
                            ->required()
                            ->columnSpan(2)
                            ->mask('(99) 99999-9999')
                            ->placeholder('(47) 99999-9999')
                            ->maxLength(30),


                        Checkbox::make('is_whatsapp')
                            ->columnSpan(2)
                            ->default(true)
                            ->inline(false)
                            ->label('Este telefone e WhatsApp'),

                        TextInput::make('zip_code')
                            ->label('CEP')
                            ->required()
                            ->columnSpan(2)
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

                        TextInput::make('number')
                            ->label('Numero')
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(20),

                        TextInput::make('street')
                            ->label('Rua')
                            ->columnSpan(4)
                            ->required()
                            ->maxLength(255),


                        TextInput::make('complement')
                            ->label('Complemento')
                            ->columnSpan(2)
                            ->maxLength(255),
                        TextInput::make('district')
                            ->columnSpan(2)
                            ->label('Bairro')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('city')
                            ->columnSpan(2)
                            ->label('Cidade')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('state')
                            ->columnSpan(2)
                            ->label('Estado (UF)')
                            ->required()
                            ->maxLength(2),

                        Select::make('member_titles')
                            ->label('Sacramentos')
                            ->multiple()
                            ->required()
                            ->columnSpan(4)
                            ->minItems(1)
                            ->options(fn (): array => SacramentalTitle::query()
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all()),

                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->columnSpan(4)
                            ->required(),

                        TextInput::make('password_confirmation')
                            ->label('Confirmar senha')
                            ->password()
                            ->columnSpan(4)
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required(),
                    ]),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        return app(CreateNewUser::class)->create([
            ...$data,
            'member_titles' => collect($data['member_titles'] ?? [])
                ->map(static fn (mixed $id): int => (int) $id)
                ->all(),
        ]);
    }

    protected function sendEmailVerificationNotification(Model $user): void
    {
        if (! $user instanceof MustVerifyEmail) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new LogicException("Model [{$userClass}] does not have a [notify()] method.");
        }

        $user->notify(new MemberVerifyEmailNotification(
            Filament::getVerifyEmailUrl($user),
        ));
    }

    public function getTitle(): string | Htmlable
    {
        return 'Cadastro';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Criar conta';
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->link()
            ->label('Entrar')
            ->url(filament()->getLoginUrl());
    }

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString('ou ' . $this->loginAction->toHtml() . ' na sua conta');
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label('Cadastrar')
            ->submit('register');
    }
}
