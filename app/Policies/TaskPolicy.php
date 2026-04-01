<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\Task;
use Illuminate\Foundation\Auth\User as AuthUser;

class TaskPolicy
{
    public function viewAny(AuthUser $authUser): bool
    {
        return true;
    }

    public function view(AuthUser $authUser, Task $task): bool
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

    public function update(AuthUser $authUser, Task $task): bool
    {
        return $this->create($authUser);
    }

    public function delete(AuthUser $authUser, Task $task): bool
    {
        return $this->create($authUser);
    }
}
