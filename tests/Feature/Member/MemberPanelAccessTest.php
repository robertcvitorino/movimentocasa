<?php

use App\Enums\RoleName;
use App\Models\Member;
use App\Models\User;
use Database\Seeders\RoleSeeder;

test('unverified member cannot access member panel', function () {
    $this->seed(RoleSeeder::class);

    $user = User::factory()->unverified()->create();
    $user->assignRole(RoleName::Member->value);

    Member::factory()->create([
        'user_id' => $user->getKey(),
        'full_name' => $user->name,
        'email' => $user->email,
    ]);

    $this->actingAs($user)
        ->get('/member')
        ->assertRedirect(route('filament.member.auth.email-verification.prompt'));
});

test('verified member can access member panel', function () {
    $this->seed(RoleSeeder::class);

    $user = User::factory()->create();
    $user->assignRole(RoleName::Member->value);

    Member::factory()->create([
        'user_id' => $user->getKey(),
        'full_name' => $user->name,
        'email' => $user->email,
    ]);

    $this->actingAs($user)
        ->get('/member')
        ->assertOk();
});

test('member login screen shows register link', function () {
    $this->get('/member/login')
        ->assertOk()
        ->assertSee('/member/register', false);
});
