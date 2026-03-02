<?php

namespace App\Enums;

enum MemberStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Visitor = 'visitor';
    case Paused = 'paused';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Ativo',
            self::Inactive => 'Inativo',
            self::Visitor => 'Visitante',
            self::Paused => 'Pausado',
        };
    }
}
