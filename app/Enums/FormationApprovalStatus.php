<?php

namespace App\Enums;

enum FormationApprovalStatus: string
{
    case PendingReview   = 'pending_review';
    case Approved        = 'approved';
    case NeedsRefinement = 'needs_refinement';

    public function label(): string
    {
        return match ($this) {
            self::PendingReview   => 'Aguardando Revisão',
            self::Approved        => 'Aprovada',
            self::NeedsRefinement => 'Requer Ajustes',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PendingReview   => 'warning',
            self::Approved        => 'success',
            self::NeedsRefinement => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PendingReview   => 'heroicon-o-clock',
            self::Approved        => 'heroicon-o-check-circle',
            self::NeedsRefinement => 'heroicon-o-exclamation-triangle',
        };
    }
}
