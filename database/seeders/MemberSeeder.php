<?php

namespace Database\Seeders;

use App\Enums\MemberStatus;
use App\Enums\RoleName;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\SacramentalTitle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $titles = SacramentalTitle::query()->pluck('id', 'slug');
        $ministries = Ministry::query()->pluck('id', 'slug');

        $admin = $this->upsertMemberUser(
            email: 'admin@admin.com',
            name: 'Administrador do Sistema',
            role: RoleName::SystemAdmin,
            status: MemberStatus::Active,
        );

        $generalCoordinator = $this->upsertMemberUser(
            email: 'coordenacao@movimentocasa.test',
            name: 'Coordenador Geral',
            role: RoleName::GeneralCoordinator,
            status: MemberStatus::Active,
        );

        $ministryCoordinator = $this->upsertMemberUser(
            email: 'ministerio@movimentocasa.test',
            name: 'Coordenador de Ministério',
            role: RoleName::MinistryCoordinator,
            status: MemberStatus::Active,
        );

        $financialCoordinator = $this->upsertMemberUser(
            email: 'financeiro@movimentocasa.test',
            name: 'Coordenador Financeiro',
            role: RoleName::FinancialCoordinator,
            status: MemberStatus::Active,
        );

        $memberA = $this->upsertMemberUser(
            email: 'membro@movimentocasa.test',
            name: 'Membro de Exemplo',
            role: RoleName::Member,
            status: MemberStatus::Active,
        );

        $memberB = $this->upsertMemberUser(
            email: 'maria@movimentocasa.test',
            name: 'Maria da Silva',
            role: RoleName::Member,
            status: MemberStatus::Active,
        );

        $memberC = $this->upsertMemberUser(
            email: 'joao@movimentocasa.test',
            name: 'João Pereira',
            role: RoleName::Member,
            status: MemberStatus::Visitor,
        );

        $this->syncTitles($admin, [$titles['baptism'] ?? null, $titles['first-eucharist'] ?? null, $titles['confirmation'] ?? null]);
        $this->syncTitles($generalCoordinator, [$titles['baptism'] ?? null, $titles['first-eucharist'] ?? null]);
        $this->syncTitles($ministryCoordinator, [$titles['baptism'] ?? null, $titles['confirmation'] ?? null]);
        $this->syncTitles($financialCoordinator, [$titles['baptism'] ?? null, $titles['first-eucharist'] ?? null]);
        $this->syncTitles($memberA, [$titles['baptism'] ?? null, $titles['first-eucharist'] ?? null, $titles['confirmation'] ?? null]);
        $this->syncTitles($memberB, [$titles['baptism'] ?? null, $titles['first-eucharist'] ?? null]);
        $this->syncTitles($memberC, [$titles['baptism'] ?? null]);

        $this->attachMemberToMinistry($generalCoordinator, $ministries['acolhida'] ?? null, 'Líder');
        $this->attachMemberToMinistry($ministryCoordinator, $ministries['formacao'] ?? null, 'Coordenador');
        $this->attachMemberToMinistry($financialCoordinator, $ministries['intercessao'] ?? null, 'Apoio');
        $this->attachMemberToMinistry($memberA, $ministries['musica'] ?? null, 'Servo');
        $this->attachMemberToMinistry($memberB, $ministries['acolhida'] ?? null, 'Servo');
        $this->attachMemberToMinistry($memberC, $ministries['intercessao'] ?? null, 'Visitante');

        $this->attachCoordinator($ministryCoordinator, $ministries['formacao'] ?? null, true);
        $this->attachCoordinator($generalCoordinator, $ministries['acolhida'] ?? null, true);
    }

    private function upsertMemberUser(
        string $email,
        string $name,
        RoleName $role,
        MemberStatus $status,
    ): Member {
        $user = User::query()->updateOrCreate(
            ['email' => Str::lower($email)],
            [
                'name' => $name,
                'password' => 'password',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([$role->value]);

        /** @var Member $member */
        $member = Member::query()->updateOrCreate(
            ['user_id' => $user->getKey()],
            [
                'full_name' => $name,
                'email' => $user->email,
                'birth_date' => fake()->dateTimeBetween('-45 years', '-18 years')->format('Y-m-d'),
                'phone' => fake()->numerify('11#########'),
                'is_whatsapp' => true,
                'instagram' => Str::slug($name, '_'),
                'zip_code' => fake()->numerify('#####-###'),
                'street' => fake()->streetName(),
                'number' => (string) fake()->numberBetween(10, 999),
                'complement' => fake()->optional()->secondaryAddress(),
                'district' => fake()->citySuffix(),
                'city' => fake()->city(),
                'state' => fake()->stateAbbr(),
                'status' => $status,
                'joined_at' => now()->subMonths(rand(1, 24))->toDateString(),
                'internal_notes' => 'Registro inicial criado pelo seeder.',
            ],
        );

        return $member;
    }

    /**
     * @param  array<int, int|null>  $titleIds
     */
    private function syncTitles(Member $member, array $titleIds): void
    {
        $titleIds = array_values(array_filter($titleIds));

        if ($titleIds === []) {
            return;
        }

        $member->titles()->syncWithoutDetaching(
            collect($titleIds)->mapWithKeys(fn (int $id): array => [
                $id => [
                    'received_at' => now()->subMonths(rand(6, 120))->toDateString(),
                    'notes' => null,
                ],
            ])->all(),
        );
    }

    private function attachMemberToMinistry(Member $member, ?int $ministryId, string $roleName): void
    {
        if (! $ministryId) {
            return;
        }

        $member->ministries()->syncWithoutDetaching([
            $ministryId => [
                'role_name' => $roleName,
                'status' => 'active',
                'joined_at' => now()->subMonths(rand(1, 18))->toDateString(),
                'left_at' => null,
                'notes' => null,
            ],
        ]);
    }

    private function attachCoordinator(Member $member, ?int $ministryId, bool $isPrimary): void
    {
        if (! $ministryId) {
            return;
        }

        $member->coordinatedMinistries()->syncWithoutDetaching([
            $ministryId => [
                'is_primary' => $isPrimary,
                'appointed_at' => now()->subMonths(rand(1, 12))->toDateString(),
                'ended_at' => null,
            ],
        ]);
    }
}
