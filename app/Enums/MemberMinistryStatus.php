<?php

namespace App\Enums;

enum MemberMinistryStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case OnLeave = 'on_leave';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Ativo',
            self::Inactive => 'Inativo',
            self::OnLeave => 'Afastado',
        };
    }
}
