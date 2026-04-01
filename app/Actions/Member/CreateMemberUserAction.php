<?php

namespace App\Actions\Member;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class CreateMemberUserAction
{
    /**
     * @param  array{full_name: string, email: string, profile_photo_path?: ?string, is_active?: bool, role_name?: ?string}  $data
     * @return array{temporary_password: string, user: User}
     */
    public function execute(array $data): array
    {
        $this->ensureEmailIsAvailable($data['email']);

        $temporaryPassword = Str::password(16);

        $user = User::query()->create([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'profile_photo_path' => $data['profile_photo_path'] ?? null,
            'password' => $temporaryPassword,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $roleName = $data['role_name'] ?? RoleName::Member->value;
        Role::findOrCreate($roleName, 'web');
        $user->syncRoles([$roleName]);

        return [
            'temporary_password' => $temporaryPassword,
            'user' => $user,
        ];
    }

    protected function ensureEmailIsAvailable(string $email): void
    {
        if (! User::query()->where('email', $email)->exists()) {
            return;
        }

        throw ValidationException::withMessages([
            'data.email' => 'Já existe um usuário com este e-mail.',
        ]);
    }
}
