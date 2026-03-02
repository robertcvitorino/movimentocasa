<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RoleName::cases() as $role) {
            $roleModel = Role::findOrCreate($role->value, 'web');

            if ($role === RoleName::SystemAdmin) {
                $roleModel->syncPermissions(Permission::query()->pluck('name')->all());
            }
        }
    }
}
