<?php

namespace App\Support\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class MemberContributionVisibility
{
    public static function forUser(Builder $query, User $user): Builder
    {
        if ($user->isSystemAdmin() || $user->isGeneralCoordinator() || $user->isFinancialCoordinator()) {
            return $query;
        }

        if ($user->isMember() && $user->member) {
            return $query->where('member_id', $user->member->getKey());
        }

        return $query->whereRaw('1 = 0');
    }
}
