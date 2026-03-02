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

function grantAttendFormationPermissions(string $roleName): void
{
    $permissions = collect([
        'ViewAny:Formation',
        'View:Formation',
    ])->map(fn (string $permission) => Permission::findOrCreate($permission, 'web')->name);

    Role::findByName($roleName, 'web')->givePermissionTo($permissions);
}

it('renders unique wizard steps even when lesson display_order is duplicated', function () {
    $this->seed(RoleSeeder::class);
    grantAttendFormationPermissions(RoleName::Member->value);

    $user = User::factory()->create();
    $user->assignRole(RoleName::Member->value);

    Member::factory()->create([
        'user_id' => $user->getKey(),
        'email' => $user->email,
        'status' => MemberStatus::Active,
    ]);

    $formation = Formation::factory()->create([
        'title' => 'Formacao com ordem duplicada',
        'status' => FormationStatus::Published,
    ]);

    $firstLesson = FormationLesson::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Primeira aula',
        'source_type' => LessonSourceType::Youtube,
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'display_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $secondLesson = FormationLesson::query()->create([
        'formation_id' => $formation->getKey(),
        'title' => 'Segunda aula',
        'source_type' => LessonSourceType::Youtube,
        'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
        'display_order' => 1,
        'is_required' => true,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(
        route('filament.member.resources.formations.attend', [
            'record' => $formation,
            'step' => 'lesson-' . $secondLesson->getKey(),
        ]),
    );

    $response
        ->assertOk()
        ->assertSee('Aula 1')
        ->assertSee('Aula 2')
        ->assertSee('lesson-' . $firstLesson->getKey(), false)
        ->assertSee('lesson-' . $secondLesson->getKey(), false);
});
