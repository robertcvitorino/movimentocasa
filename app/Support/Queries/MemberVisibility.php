<?php

namespace App\Support\Queries;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class MemberVisibility
{
    public static function forUser(Builder $query, User $user): Builder
    {
        if ($user->isSystemAdmin() || $user->isGeneralCoordinator()) {
            return $query;
        }

        if ($user->isMinistryCoordinator() && $user->member) {
            return $query->whereHas('ministries.coordinators', function (Builder $coordinatorQuery) use ($user): void {
                $coordinatorQuery->where('members.id', $user->member->getKey());
            });
        }

        if ($user->isMember() && $user->member) {
            return $query->whereKey($user->member->getKey());
        }

        return $query->whereRaw('1 = 0');
    }
}
