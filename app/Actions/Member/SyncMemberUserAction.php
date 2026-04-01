<?php

namespace App\Actions\Member;

use App\Enums\RoleName;
use App\Models\Member;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class SyncMemberUserAction
{
    /**
     * @param  array{full_name: string, email: string, profile_photo_path?: ?string, is_active?: bool, role_name?: ?string}  $data
     */
    public function execute(Member $member, array $data): void
    {
        $user = $member->user;

        if (! $user) {
            return;
        }

        $this->ensureEmailIsAvailable($data['email'], $user);

        $user->forceFill([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'profile_photo_path' => $data['profile_photo_path'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ])->save();

        $roleName = $data['role_name'] ?? RoleName::Member->value;
        Role::findOrCreate($roleName, 'web');
        $user->syncRoles([$roleName]);
    }

    protected function ensureEmailIsAvailable(string $email, User $user): void
    {
        if (! User::query()->where('email', $email)->whereKeyNot($user->getKey())->exists()) {
            return;
        }

        throw ValidationException::withMessages([
            'data.email' => 'Já existe um usuário com este e-mail.',
        ]);
    }
}
