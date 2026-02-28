<?php

namespace Database\Seeders;

use App\Enums\MemberStatus;
use App\Enums\RoleName;
use App\Models\Member;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SacramentalTitleSeeder::class,
        ]);

        $admin = User::query()->updateOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Administrador do Sistema',
            'password' => Hash::make('admin'),
            'is_active' => true,
        ]);

        $admin->syncRoles([RoleName::SystemAdmin->value]);

        Member::query()->updateOrCreate(
            ['user_id' => $admin->getKey()],
            [
                'full_name' => 'Administrador do Sistema',
                'email' => $admin->email,
                'status' => MemberStatus::Active,
                'joined_at' => now()->toDateString(),
                'is_whatsapp' => true,
            ],
        );

        $memberUser = User::query()->firstOrCreate([
            'email' => 'membro@movimentocasa.test',
        ], [
            'name' => 'Membro de Exemplo',
            'password' => 'password',
            'is_active' => true,
        ]);

        $memberUser->assignRole(RoleName::Member->value);

        Member::query()->updateOrCreate(
            ['user_id' => $memberUser->getKey()],
            [
                'full_name' => 'Membro de Exemplo',
                'email' => $memberUser->email,
                'status' => MemberStatus::Active,
                'joined_at' => now()->toDateString(),
                'is_whatsapp' => true,
            ],
        );
    }
}
