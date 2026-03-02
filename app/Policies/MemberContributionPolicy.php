<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MemberContribution;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberContributionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MemberContribution');
    }

    public function view(AuthUser $authUser, MemberContribution $memberContribution): bool
    {
        return $authUser->can('View:MemberContribution');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MemberContribution');
    }

    public function update(AuthUser $authUser, MemberContribution $memberContribution): bool
    {
        return $authUser->can('Update:MemberContribution');
    }

    public function delete(AuthUser $authUser, MemberContribution $memberContribution): bool
    {
        return $authUser->can('Delete:MemberContribution');
    }

    public function restore(AuthUser $authUser, MemberContribution $memberContribution): bool
    {
        return $authUser->can('Restore:MemberContribution');
    }

    public function forceDelete(AuthUser $authUser, MemberContribution $memberContribution): bool
    {
        return $authUser->can('ForceDelete:MemberContribution');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MemberContribution');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MemberContribution');
    }

    public function replicate(AuthUser $authUser, MemberContribution $memberContribution): bool
    {
        return $authUser->can('Replicate:MemberContribution');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MemberContribution');
    }

}