<?php

namespace App\Enums;

enum QuizAttemptStatus: string
{
    case InProgress = 'in_progress';
    case Submitted = 'submitted';
    case Passed = 'passed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::InProgress => 'Em andamento',
            self::Submitted => 'Enviada',
            self::Passed => 'Aprovada',
            self::Failed => 'Reprovada',
        };
    }
}
