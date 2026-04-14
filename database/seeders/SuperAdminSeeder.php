<?php

namespace Database\Seeders;

use App\Enums\MemberStatus;
use App\Enums\RoleName;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'admin@movimentocasa.test'],
            [
                'name'              => 'Super Admin',
                'password'          => 'password',
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([RoleName::SystemAdmin->value]);

        Member::query()->updateOrCreate(
            ['user_id' => $user->getKey()],
            [
                'full_name'  => 'Super Admin',
                'email'      => $user->email,
                'status'     => MemberStatus::Active,
                'joined_at'  => now()->toDateString(),
            ],
        );

        $this->command->info('Super Admin criado: admin@movimentocasa.test / password');
    }
}
