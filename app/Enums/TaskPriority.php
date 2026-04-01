<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Baixo',
            self::Medium => 'Medio',
            self::High => 'Alto',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => '#16a34a',
            self::Medium => '#f59e0b',
            self::High => '#dc2626',
        };
    }
}
