<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FinancialGoal;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinancialGoalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FinancialGoal');
    }

    public function view(AuthUser $authUser, FinancialGoal $financialGoal): bool
    {
        return $authUser->can('View:FinancialGoal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FinancialGoal');
    }

    public function update(AuthUser $authUser, FinancialGoal $financialGoal): bool
    {
        return $authUser->can('Update:FinancialGoal');
    }

    public function delete(AuthUser $authUser, FinancialGoal $financialGoal): bool
    {
        return $authUser->can('Delete:FinancialGoal');
    }

    public function restore(AuthUser $authUser, FinancialGoal $financialGoal): bool
    {
        return $authUser->can('Restore:FinancialGoal');
    }

    public function forceDelete(AuthUser $authUser, FinancialGoal $financialGoal): bool
    {
        return $authUser->can('ForceDelete:FinancialGoal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FinancialGoal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FinancialGoal');
    }

    public function replicate(AuthUser $authUser, FinancialGoal $financialGoal): bool
    {
        return $authUser->can('Replicate:FinancialGoal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FinancialGoal');
    }

}