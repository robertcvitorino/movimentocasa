<?php

namespace App\Filament\Support;

class CalendarPresentation
{
    public const DEFAULT_OPTIONS = [
        'height' => 'auto',
        'headerToolbar' => [
            'start' => 'dayGridMonth,timeGridWeek,timeGridDay',
            'center' => 'title',
            'end' => 'prev,next today',
        ],
        'buttonText' => [
            'today' => 'Hoje',
            'dayGridMonth' => 'Mes',
            'timeGridWeek' => 'Semana',
            'timeGridDay' => 'Dia',
        ],
        'eventTimeFormat' => [
            'hour' => '2-digit',
            'minute' => '2-digit',
            'hour12' => false,
        ],
    ];

    /**
     * @return array<string, mixed>
     */
    public static function defaultOptions(): array
    {
        return self::DEFAULT_OPTIONS;
    }
}
