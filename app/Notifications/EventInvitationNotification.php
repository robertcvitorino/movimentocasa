<?php

namespace App\Notifications;

use App\Models\Event;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Event $event,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Novo evento na agenda')
            ->body($this->eventSummary())
            ->icon('heroicon-o-calendar-days')
            ->getDatabaseMessage();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('Novo evento na agenda')
            ->body($this->eventSummary())
            ->icon('heroicon-o-calendar-days')
            ->getBroadcastMessage();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novo evento na agenda - Movimento Casa')
            ->greeting('Ola, ' . ($notifiable->name ?? 'membro') . '!')
            ->line('Voce recebeu um novo convite de agenda.')
            ->line($this->eventSummary())
            ->action('Abrir agenda', url('/member/agenda'));
    }

    protected function eventSummary(): string
    {
        $start = $this->event->start_datetime?->format('d/m/Y H:i') ?? '-';
        $location = filled($this->event->location) ? 'Local: ' . $this->event->location : 'Local nao informado';

        return sprintf('%s - %s. %s', $this->event->title, $start, $location);
    }
}
