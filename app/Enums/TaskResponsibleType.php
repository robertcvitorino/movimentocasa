<?php

namespace App\Enums;

enum TaskResponsibleType: string
{
    case Member = 'member';
    case Ministry = 'ministry';

    public function label(): string
    {
        return match ($this) {
            self::Member => 'Pessoa',
            self::Ministry => 'Ministerio',
        };
    }
}
