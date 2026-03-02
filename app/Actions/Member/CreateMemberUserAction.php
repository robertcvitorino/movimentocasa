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
     * @param  array{full_name: string, email: string}  $data
     * @return array{temporary_password: string, user: User}
     */
    public function execute(array $data): array
    {
        $this->ensureEmailIsAvailable($data['email']);

        $temporaryPassword = Str::password(16);

        $user = User::query()->create([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'password' => $temporaryPassword,
            'is_active' => true,
        ]);

        Role::findOrCreate(RoleName::Member->value, 'web');
        $user->assignRole(RoleName::Member->value);

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
