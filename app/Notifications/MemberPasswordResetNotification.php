<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberPasswordResetNotification extends Notification
{
    public function __construct(
        public readonly string $passwordResetUrl,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = (string) ($notifiable->name ?? 'membro');

        return (new MailMessage)
            ->subject('Redefinicao de senha')
            ->markdown('mail.member.password-reset', [
                'name' => $name,
                'passwordResetUrl' => $this->passwordResetUrl,
                'logoUrl' => asset('image/logo_casa.png'),
            ]);
    }
}
