<?php

use App\Actions\Member\CreateMemberUserAction;
use App\Actions\Member\SendMemberPasswordResetAction;
use App\Actions\Member\SyncMemberUserAction;
use App\Enums\MemberStatus;
use App\Enums\RoleName;
use App\Models\Member;
use App\Models\User;
use App\Notifications\MemberAccountCreatedNotification;
use App\Notifications\MemberPasswordResetNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('creates a member user with a temporary password and selected shield role', function () {
    $this->seed(RoleSeeder::class);

    $account = app(CreateMemberUserAction::class)->execute([
        'full_name' => 'Maria da Silva',
        'email' => 'maria@example.com',
        'profile_photo_path' => 'users/profile-photos/maria.jpg',
        'is_active' => false,
        'role_name' => RoleName::MinistryCoordinator->value,
    ]);

    $user = $account['user'];

    expect($user->name)->toBe('Maria da Silva');
    expect($user->email)->toBe('maria@example.com');
    expect($user->profile_photo_path)->toBe('users/profile-photos/maria.jpg');
    expect($user->is_active)->toBeFalse();
    expect($user->hasRole(RoleName::MinistryCoordinator->value))->toBeTrue();
    expect($user->hasRole(RoleName::Member->value))->toBeFalse();
    expect(Hash::check($account['temporary_password'], $user->password))->toBeTrue();
});

it('syncs the linked user data when the member data changes', function () {
    $this->seed(RoleSeeder::class);

    $member = Member::factory()->create([
        'full_name' => 'Maria Antiga',
        'email' => 'maria.antiga@example.com',
        'status' => MemberStatus::Active,
    ]);

    app(SyncMemberUserAction::class)->execute($member, [
        'full_name' => 'Maria Atualizada',
        'email' => 'maria.atualizada@example.com',
        'profile_photo_path' => 'users/profile-photos/maria-atualizada.jpg',
        'is_active' => false,
        'role_name' => RoleName::FinancialCoordinator->value,
    ]);

    $member->refresh();
    $member->user->refresh();

    expect($member->user->name)->toBe('Maria Atualizada');
    expect($member->user->email)->toBe('maria.atualizada@example.com');
    expect($member->user->profile_photo_path)->toBe('users/profile-photos/maria-atualizada.jpg');
    expect($member->user->is_active)->toBeFalse();
    expect($member->user->hasRole(RoleName::FinancialCoordinator->value))->toBeTrue();
    expect($member->user->hasRole(RoleName::Member->value))->toBeFalse();
});

it('sends an onboarding email with temporary password and password reset link', function () {
    Notification::fake();

    $user = User::factory()->create([
        'name' => 'Maria da Silva',
        'email' => 'maria@example.com',
    ]);

    $temporaryPassword = 'SenhaTemporaria#2026';
    $passwordResetUrl = route('password.reset', [
        'token' => Password::broker()->createToken($user),
        'email' => $user->email,
    ]);

    $user->notify(new MemberAccountCreatedNotification(
        temporaryPassword: $temporaryPassword,
        passwordResetUrl: $passwordResetUrl,
    ));

    Notification::assertSentTo(
        $user,
        MemberAccountCreatedNotification::class,
        function (MemberAccountCreatedNotification $notification) use ($temporaryPassword, $passwordResetUrl): bool {
            expect($notification->temporaryPassword)->toBe($temporaryPassword);
            expect($notification->passwordResetUrl)->toBe($passwordResetUrl);

            return true;
        },
    );
});

it('persists the user profile photo path', function () {
    $user = User::factory()->create([
        'profile_photo_path' => 'users/profile-photos/maria.jpg',
    ]);

    expect($user->refresh()->profile_photo_path)
        ->toBe('users/profile-photos/maria.jpg');
});

it('sends a password reset email to the member user', function () {
    Notification::fake();

    $member = Member::factory()->create();
    $user = $member->user;

    app(SendMemberPasswordResetAction::class)->execute($member);

    Notification::assertSentTo(
        $user,
        MemberPasswordResetNotification::class,
        function (MemberPasswordResetNotification $notification) use ($user): bool {
            expect($notification->passwordResetUrl)->toContain('/member/password-reset/');
            expect($notification->passwordResetUrl)->toContain('email='.urlencode($user->email));

            return true;
        },
    );
});
