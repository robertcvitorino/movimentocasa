<?php

namespace App\Support\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class FinancialGoalVisibility
{
    public static function forUser(Builder $query, User $user): Builder
    {
        if ($user->isSystemAdmin() || $user->isGeneralCoordinator() || $user->isFinancialCoordinator()) {
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }
}
