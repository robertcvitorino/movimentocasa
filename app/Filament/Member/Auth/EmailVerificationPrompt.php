<?php

namespace App\Filament\Member\Auth;

use App\Notifications\MemberVerifyEmailNotification;
use Filament\Actions\Action;
use Filament\Auth\Pages\EmailVerification\EmailVerificationPrompt as BaseEmailVerificationPrompt;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use LogicException;

class EmailVerificationPrompt extends BaseEmailVerificationPrompt
{
    public function getTitle(): string | Htmlable
    {
        return 'Verificacao de e-mail';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Verifique seu e-mail';
    }

    public function resendNotificationAction(): Action
    {
        return parent::resendNotificationAction()
            ->label('Reenviar e-mail de verificacao.');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Text::make('Enviamos um link de verificacao para ' . filament()->auth()->user()->getEmailForVerification() . '.'),
                Text::make(new HtmlString(
                    'Nao recebeu o e-mail? ' . $this->resendNotificationAction->toHtml(),
                )),
            ]);
    }

    protected function getRateLimitedNotification(\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title('Muitas tentativas de reenvio.')
            ->body('Aguarde alguns segundos e tente novamente.')
            ->danger();
    }

    protected function sendEmailVerificationNotification(MustVerifyEmail $user): void
    {
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
}
