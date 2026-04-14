<?php

namespace Database\Seeders;

use App\Enums\MemberMinistryStatus;
use App\Enums\MemberStatus;
use App\Enums\MinistryStatus;
use App\Enums\RoleName;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MinistryAcolhidaSeeder extends Seeder
{
    public function run(): void
    {
        // Ministério
        $ministry = Ministry::query()->updateOrCreate(
            ['slug' => 'acolhida'],
            [
                'name'        => 'Acolhida',
                'description' => 'Recepção e acolhimento dos participantes nos encontros.',
                'status'      => MinistryStatus::Active,
            ],
        );

        // Usuário + Membro
        $user = User::query()->updateOrCreate(
            ['email' => 'acolhida@movimentocasa.test'],
            [
                'name'              => 'Membro Acolhida',
                'password'          => 'password',
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([RoleName::Member->value]);

        $member = Member::query()->updateOrCreate(
            ['user_id' => $user->getKey()],
            [
                'full_name'  => 'Membro Acolhida',
                'email'      => $user->email,
                'status'     => MemberStatus::Active,
                'joined_at'  => now()->toDateString(),
            ],
        );

        // Vínculo com o ministério
        $member->ministries()->syncWithoutDetaching([
            $ministry->getKey() => [
                'role_name' => 'Servo',
                'status'    => MemberMinistryStatus::Active->value,
                'joined_at' => now()->toDateString(),
            ],
        ]);

        $this->command->info("Ministério Acolhida: acolhida@movimentocasa.test / password");
    }
}
