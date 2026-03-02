<?php

namespace App\Enums;

enum FormationProgressStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Failed = 'failed';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'Não iniciada',
            self::InProgress => 'Em andamento',
            self::Completed => 'Concluída',
            self::Failed => 'Reprovada',
            self::Blocked => 'Bloqueada',
        };
    }
}
