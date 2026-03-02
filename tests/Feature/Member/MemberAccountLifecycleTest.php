<?php

use App\Actions\Member\CreateMemberUserAction;
use App\Actions\Member\SyncMemberUserAction;
use App\Enums\MemberStatus;
use App\Enums\RoleName;
use App\Models\Member;
use App\Models\User;
use App\Notifications\MemberAccountCreatedNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('creates a member user with a temporary password and member role', function () {
    $this->seed(RoleSeeder::class);

    $account = app(CreateMemberUserAction::class)->execute([
        'full_name' => 'Maria da Silva',
        'email' => 'maria@example.com',
    ]);

    $user = $account['user'];

    expect($user->name)->toBe('Maria da Silva');
    expect($user->email)->toBe('maria@example.com');
    expect($user->is_active)->toBeTrue();
    expect($user->hasRole(RoleName::Member->value))->toBeTrue();
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
    ]);

    $member->refresh();
    $member->user->refresh();

    expect($member->user->name)->toBe('Maria Atualizada');
    expect($member->user->email)->toBe('maria.atualizada@example.com');
    expect($member->user->hasRole(RoleName::Member->value))->toBeTrue();
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
