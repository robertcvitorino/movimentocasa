<?php

namespace App\Enums;

enum LessonSourceType: string
{
    case Upload = 'upload';
    case Youtube = 'youtube';

    public function label(): string
    {
        return match ($this) {
            self::Upload => 'Upload',
            self::Youtube => 'YouTube',
        };
    }
}
