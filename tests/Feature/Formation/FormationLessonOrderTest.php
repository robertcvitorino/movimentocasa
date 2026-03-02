<?php

use App\Enums\FormationStatus;
use App\Enums\LessonSourceType;
use App\Enums\RoleName;
use App\Filament\Resources\Formations\Pages\CreateFormation;
use App\Models\Formation;
use App\Models\Ministry;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function grantFormationManagementPermissions(string $roleName): void
{
    $permissions = collect([
        'ViewAny:Formation',
        'Create:Formation',
    ])->map(fn (string $permission) => Permission::findOrCreate($permission, 'web')->name);

    Role::findByName($roleName, 'web')->givePermissionTo($permissions);
}

it('normalizes lesson display_order automatically when creating a formation', function () {
    $this->seed(RoleSeeder::class);
    grantFormationManagementPermissions(RoleName::SystemAdmin->value);

    $user = User::factory()->create();
    $user->assignRole(RoleName::SystemAdmin->value);

    $ministry = Ministry::factory()->create();

    $this->actingAs($user);
    Filament::setCurrentPanel('admin');

    $undoRepeaterFake = Repeater::fake();

    Livewire::test(CreateFormation::class)
        ->set('data', [
            'title' => 'Formacao com aulas sequenciais',
            'ministry_id' => $ministry->getKey(),
            'slug' => 'formacao-com-aulas-sequenciais',
            'minimum_score' => 70,
            'status' => FormationStatus::Published->value,
            'certificate_enabled' => true,
            'is_required' => true,
            'quiz' => [
                'title' => 'Prova final',
                'minimum_score' => 70,
                'max_attempts' => 3,
                'is_active' => false,
                'questions' => [],
            ],
            'lessons' => [
                [
                    'title' => 'Aula inicial',
                    'source_type' => LessonSourceType::Youtube->value,
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'display_order' => 1,
                    'is_required' => true,
                    'is_active' => true,
                ],
                [
                    'title' => 'Aula seguinte',
                    'source_type' => LessonSourceType::Youtube->value,
                    'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                    'display_order' => 1,
                    'is_required' => true,
                    'is_active' => true,
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();

    $formation = Formation::query()
        ->where('slug', 'formacao-com-aulas-sequenciais')
        ->with('lessons')
        ->firstOrFail();

    expect($formation->lessons->pluck('display_order')->all())
        ->toBe([1, 2]);
});
