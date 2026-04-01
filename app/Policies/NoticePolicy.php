<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Notice;
use Illuminate\Foundation\Auth\User as AuthUser;

class NoticePolicy
{
    public function viewAny(AuthUser $authUser): bool
    {
        return true;
    }

    public function view(AuthUser $authUser, Notice $notice): bool
    {
        return true;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole([
            RoleName::SystemAdmin->value,
            RoleName::GeneralCoordinator->value,
            RoleName::MinistryCoordinator->value,
        ]);
    }

    public function update(AuthUser $authUser, Notice $notice): bool
    {
        return $this->create($authUser);
    }

    public function delete(AuthUser $authUser, Notice $notice): bool
    {
        return $this->create($authUser);
    }
}

