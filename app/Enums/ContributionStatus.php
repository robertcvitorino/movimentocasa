<?php

namespace App\Enums;

enum ContributionStatus: string
{
    case Pending = 'pending';
    case Declared = 'declared';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Declared => 'Declarada',
            self::Confirmed => 'Confirmada',
            self::Cancelled => 'Cancelada',
        };
    }
}
