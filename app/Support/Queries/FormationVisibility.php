<?php

namespace App\Support\Queries;

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

        // Admins no portal do membro veem todas as publicadas (comportamento original)
        if ($user->isSystemAdmin() || $user->isGeneralCoordinator()) {
            return $query->published();
        }

        // Portal do membro: aplica regras de visibilidade por ministério
        $member = $user->member;

        if (! $member) {
            return $query->whereRaw('1 = 0');
        }

        // IDs dos ministérios ativos do membro (respeita status = Active do pivot)
        $ministryIds = $member->ministries()->pluck('ministries.id');

        return $query->published()->where(function (Builder $q) use ($ministryIds): void {
            // Flag geral ativa → todos os membros veem
            $q->where('is_general', true)
              // Flag geral inativa → somente membros do ministério vinculado
              ->orWhereIn('ministry_id', $ministryIds);
        });
    }
}
