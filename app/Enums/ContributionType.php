<?php

namespace App\Enums;

enum ContributionType: string
{
    case Tithe = 'tithe';
    case Offering = 'offering';
    case Donation = 'donation';

    public function label(): string
    {
        return match ($this) {
            self::Tithe => 'Dízimo',
            self::Offering => 'Oferta',
            self::Donation => 'Doação',
        };
    }
}
