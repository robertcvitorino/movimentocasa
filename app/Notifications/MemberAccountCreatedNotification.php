<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberAccountCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $temporaryPassword,
        public readonly string $passwordResetUrl,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seu acesso ao Movimento Casa foi criado')
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Criamos seu usuário de acesso ao Movimento Casa.')
            ->line('Usuário: '.$notifiable->email)
            ->line('Senha temporária: '.$this->temporaryPassword)
            ->line('Por segurança, altere sua senha no primeiro acesso.')
            ->action('Alterar senha', $this->passwordResetUrl)
            ->line('Se preferir, você também pode entrar com a senha temporária e alterá-la depois na área logada.');
    }
}
