<?php

namespace App\Enums;

enum LessonProgressStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'Não iniciada',
            self::InProgress => 'Em andamento',
            self::Completed => 'Concluída',
        };
    }
}
