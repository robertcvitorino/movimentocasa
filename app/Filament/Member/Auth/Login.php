<?php

namespace App\Filament\Member\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('E-mail')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Senha')
            ->hint(
                filament()->hasPasswordReset()
                    ? new HtmlString(Blade::render(
                        '<x-filament::link :href="filament()->getRequestPasswordResetUrl()">Esqueci minha senha</x-filament::link>'
                    ))
                    : null
            )
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required();
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Lembrar de mim');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Entrar';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Acesse sua conta';
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Entrar')
            ->submit('authenticate');
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label('Cadastrar-se')
            ->url(filament()->getRegistrationUrl());
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString('Ainda nao tem conta? '.$this->registerAction->toHtml());
    }

    protected function throwFailureValidationException(): never
    {
        $email = mb_strtolower(trim((string) data_get($this->data, 'email', '')));

        if ($email !== '') {
            $user = User::query()->where('email', $email)->first();

            if ($user && ! $user->is_active) {
                throw ValidationException::withMessages([
                    'data.email' => 'Voce não tem acesso ao sistema. Entre em contato com o coordenador.',
                ]);
            }
        }

        parent::throwFailureValidationException();
    }
}
