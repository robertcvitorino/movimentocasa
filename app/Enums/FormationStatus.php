<?php

namespace App\Enums;

enum FormationStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Rascunho',
            self::Published => 'Publicada',
            self::Archived => 'Arquivada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'warning',
            self::Published => 'success',
            self::Archived => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil-square',
            self::Published => 'heroicon-o-check-circle',
            self::Archived => 'heroicon-o-archive-box',
        };
    }
}
