<?php

use App\Enums\FormationStatus;
use App\Enums\LessonSourceType;
use App\Enums\MemberStatus;
use App\Enums\RoleName;
use App\Models\Formation;
use App\Models\FormationLesson;
use App\Models\Member;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function grantAttendFormationWithoutQuizPermissions(string $roleName): void
{
    $permissions = collect([
        'ViewAny:Formation',
        'View:Formation',
    ])->map(fn (string $permission) => Permission::findOrCreate($permission, 'web')->name);

    Role::findByName($roleName, 'web')->givePermissionTo($permissions);
}

it('shows generate certificate submit button when formation has no active quiz', function () {
    $this->seed(RoleSeeder::class);
    grantAttendFormationWithoutQuizPermissions(RoleName::Member->value);

    $user = User::factory()->create();
    $user->assignRole(RoleName::Member->value);

    Member::factory()->create([
        'user_id' => $user->getKey(),
        'email' => $user->email,
        'status' => MemberStatus::Active,
    ]);

    $formation = Formation::factory()->create([
        'status' => FormationStatus::Published,
    ]);

    FormationLesson::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Aula sem prova',
        'source_type' => LessonSourceType::Youtube,
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'display_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('filament.member.resources.formations.attend', ['record' => $formation]))
        ->assertOk()
        ->assertSee('Gerar certificado')
        ->assertDontSee('Enviar quiz');
});
