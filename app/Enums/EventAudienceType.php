<?php

namespace App\Enums;

enum EventAudienceType: string
{
    case General = 'general';
    case Ministry = 'ministry';
    case Members = 'members';

    public function label(): string
    {
        return match ($this) {
            self::General => 'Evento geral',
            self::Ministry => 'Convidar ministerio',
            self::Members => 'Convidar membros',
        };
    }
}
