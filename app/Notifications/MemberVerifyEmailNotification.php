<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberVerifyEmailNotification extends Notification
{
    public function __construct(
        private readonly string $verificationUrl,
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
            ->subject('Verifique seu e-mail')
            ->markdown('mail.member.verify-email', [
                'name' => $name,
                'verificationUrl' => $this->verificationUrl,
                'logoUrl' => asset('image/logo_casa.png'),
            ]);
    }
}
