<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Pix = 'pix';
    case Cash = 'cash';
    case Other = 'other';
    case Prayer = 'prayer';

    public function label(): string
    {
        return match ($this) {
            self::Pix => 'Pix',
            self::Cash => 'Dinheiro',
            self::Other => 'Outro',
            self::Prayer => 'Oração',
        };
    }
}
