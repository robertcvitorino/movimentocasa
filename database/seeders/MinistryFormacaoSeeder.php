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

class MinistryFormacaoSeeder extends Seeder
{
    public function run(): void
    {
        // Ministério
        $ministry = Ministry::query()->updateOrCreate(
            ['slug' => 'formacao'],
            [
                'name'        => 'Formação',
                'description' => 'Organização de trilhas formativas e conteúdo catequético.',
                'status'      => MinistryStatus::Active,
            ],
        );

        // Usuário + Membro
        $user = User::query()->updateOrCreate(
            ['email' => 'formacao@movimentocasa.test'],
            [
                'name'              => 'Membro Formação',
                'password'          => 'password',
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([RoleName::Member->value]);

        $member = Member::query()->updateOrCreate(
            ['user_id' => $user->getKey()],
            [
                'full_name'  => 'Membro Formação',
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

        $this->command->info("Ministério Formação: formacao@movimentocasa.test / password");
    }
}
