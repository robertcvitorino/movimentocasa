<?php

namespace App\Enums;

enum FinancialGoalStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Rascunho',
            self::Active => 'Ativa',
            self::Completed => 'Concluída',
            self::Cancelled => 'Cancelada',
        };
    }
}
