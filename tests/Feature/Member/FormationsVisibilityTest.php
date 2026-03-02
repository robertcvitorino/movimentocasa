<?php

use App\Enums\FormationStatus;
use App\Enums\MemberStatus;
use App\Enums\RoleName;
use App\Models\Formation;
use App\Models\Member;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function grantFormationViewPermissions(string $roleName): void
{
    $permissions = collect([
        'ViewAny:Formation',
        'View:Formation',
    ])->map(fn (string $permission) => Permission::findOrCreate($permission, 'web')->name);

    Role::findByName($roleName, 'web')->givePermissionTo($permissions);
}

it('shows only published formations in the member panel', function () {
    $this->seed(RoleSeeder::class);
    grantFormationViewPermissions(RoleName::Member->value);

    $user = User::factory()->create();
    $user->assignRole(RoleName::Member->value);

    Member::factory()->create([
        'user_id' => $user->getKey(),
        'email' => $user->email,
        'status' => MemberStatus::Active,
    ]);

    $publishedFormation = Formation::factory()->create([
        'title' => 'Formacao Publicada',
        'status' => FormationStatus::Published,
    ]);

    Formation::factory()->create([
        'title' => 'Formacao Rascunho',
        'status' => FormationStatus::Draft,
    ]);

    $this->actingAs($user)
        ->get(route('filament.member.resources.formations.index'))
        ->assertOk()
        ->assertSee($publishedFormation->title)
        ->assertDontSee('Formacao Rascunho');
});

it('does not resolve unpublished formations for members on direct access', function () {
    $this->seed(RoleSeeder::class);
    grantFormationViewPermissions(RoleName::Member->value);

    $user = User::factory()->create();
    $user->assignRole(RoleName::Member->value);

    Member::factory()->create([
        'user_id' => $user->getKey(),
        'email' => $user->email,
        'status' => MemberStatus::Active,
    ]);

    $draftFormation = Formation::factory()->create([
        'status' => FormationStatus::Draft,
    ]);

    $this->actingAs($user)
        ->get(route('filament.member.resources.formations.attend', ['record' => $draftFormation]))
        ->assertNotFound();
});

it('shows only published formations in the member panel for system admins too', function () {
    $this->seed(RoleSeeder::class);
    grantFormationViewPermissions(RoleName::SystemAdmin->value);

    $user = User::factory()->create();
    $user->assignRole(RoleName::SystemAdmin->value);

    $publishedFormation = Formation::factory()->create([
        'title' => 'Formacao Publicada Admin',
        'status' => FormationStatus::Published,
    ]);

    Formation::factory()->create([
        'title' => 'Formacao Rascunho Admin',
        'status' => FormationStatus::Draft,
    ]);

    $this->actingAs($user)
        ->get(route('filament.member.resources.formations.index'))
        ->assertOk()
        ->assertSee($publishedFormation->title)
        ->assertDontSee('Formacao Rascunho Admin');
});
