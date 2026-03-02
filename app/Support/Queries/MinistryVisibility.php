<?php

namespace App\Support\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class MinistryVisibility
{
    public static function forUser(Builder $query, User $user): Builder
    {
        if ($user->isSystemAdmin() || $user->isGeneralCoordinator()) {
            return $query;
        }

        if ($user->isMinistryCoordinator() && $user->member) {
            return $query->whereHas('coordinators', fn (Builder $coordinatorQuery) => $coordinatorQuery->whereKey($user->member->getKey()));
        }

        if ($user->isMember() && $user->member) {
            return $query->whereHas('members', fn (Builder $memberQuery) => $memberQuery->whereKey($user->member->getKey()));
        }

        return $query->whereRaw('1 = 0');
    }
}
