<?php

use App\Enums\MemberMinistryStatus;
use App\Enums\RoleName;
use App\Enums\TaskPriority;
use App\Enums\TaskResponsibleType;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\Task;
use Spatie\Permission\Models\Role;

it('shows person tasks only to responsible member and coordinator', function () {
    $responsibleMember = Member::factory()->create();
    $otherMember = Member::factory()->create();
    $coordinatorMember = Member::factory()->create();

    Role::findOrCreate(RoleName::GeneralCoordinator->value, 'web');
    $coordinatorMember->user->assignRole(RoleName::GeneralCoordinator->value);

    $task = Task::factory()->create([
        'title' => 'Tarefa pessoa',
        'priority' => TaskPriority::High,
        'responsible_type' => TaskResponsibleType::Member,
        'responsible_member_id' => $responsibleMember->getKey(),
        'responsible_ministry_id' => null,
    ]);

    $responsibleVisible = Task::query()
        ->visibleToUser($responsibleMember->user)
        ->pluck('title')
        ->all();

    $otherVisible = Task::query()
        ->visibleToUser($otherMember->user)
        ->pluck('title')
        ->all();

    $coordinatorVisible = Task::query()
        ->visibleToUser($coordinatorMember->user)
        ->pluck('title')
        ->all();

    expect($responsibleVisible)->toContain($task->title);
    expect($otherVisible)->not->toContain($task->title);
    expect($coordinatorVisible)->toContain($task->title);
});

it('shows ministry tasks only to members in the responsible ministry', function () {
    $ministry = Ministry::factory()->create();
    $otherMinistry = Ministry::factory()->create();
    $memberInMinistry = Member::factory()->create();
    $memberOutMinistry = Member::factory()->create();

    $ministry->members()->attach($memberInMinistry->getKey(), [
        'status' => MemberMinistryStatus::Active->value,
    ]);

    $otherMinistry->members()->attach($memberOutMinistry->getKey(), [
        'status' => MemberMinistryStatus::Active->value,
    ]);

    $task = Task::factory()->create([
        'title' => 'Tarefa ministerio',
        'priority' => TaskPriority::Medium,
        'responsible_type' => TaskResponsibleType::Ministry,
        'responsible_member_id' => null,
        'responsible_ministry_id' => $ministry->getKey(),
    ]);

    $inMinistryVisible = Task::query()
        ->visibleToUser($memberInMinistry->user)
        ->pluck('title')
        ->all();

    $outMinistryVisible = Task::query()
        ->visibleToUser($memberOutMinistry->user)
        ->pluck('title')
        ->all();

    expect($inMinistryVisible)->toContain($task->title);
    expect($outMinistryVisible)->not->toContain($task->title);
});
