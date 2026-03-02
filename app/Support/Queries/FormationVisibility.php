<?php

namespace App\Support\Queries;

use App\Enums\FormationStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class FormationVisibility
{
    public static function forUser(Builder $query, User $user, bool $management = true): Builder
    {
        if ($management) {
            if ($user->isSystemAdmin() || $user->isGeneralCoordinator()) {
                return $query;
            }

            return $query->whereRaw('1 = 0');
        }

        return $query->published();
    }
}
