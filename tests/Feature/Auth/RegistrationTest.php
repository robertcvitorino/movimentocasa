<?php

use App\Enums\RoleName;
use App\Models\Member;
use App\Models\SacramentalTitle;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SacramentalTitleSeeder;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(SacramentalTitleSeeder::class);
    $this->withoutMiddleware();

    $selectedTitleIds = SacramentalTitle::query()->limit(2)->pluck('id')->all();

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe Silva',
        'email' => 'test@example.com',
        'birth_date' => '1992-06-18',
        'phone' => '11999999999',
        'is_whatsapp' => '1',
        'instagram' => '@johnsilva',
        'zip_code' => '01001-000',
        'street' => 'Rua das Flores',
        'number' => '123',
        'complement' => 'Apto 45',
        'district' => 'Centro',
        'city' => 'Sao Paulo',
        'state' => 'sp',
        'member_titles' => $selectedTitleIds,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    /** @var User $user */
    $user = User::query()->where('email', 'test@example.com')->firstOrFail();

    expect($user->hasRole(RoleName::Member->value))->toBeTrue();

    $member = Member::query()->where('user_id', $user->getKey())->first();

    expect($member)->not->toBeNull();
    expect($member?->full_name)->toBe('John Doe Silva');
    expect($member?->email)->toBe('test@example.com');
    expect($member?->is_whatsapp)->toBeTrue();
    expect($member?->instagram)->toBe('johnsilva');
    expect($member?->state)->toBe('SP');
    expect($member?->status?->value)->toBe('active');
    expect($member?->joined_at?->toDateString())->toBe(now()->toDateString());
    expect($member?->titles()->pluck('sacramental_titles.id')->all())->toEqualCanonicalizing($selectedTitleIds);
});

test('new users can register with whatsapp checkbox default value', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(SacramentalTitleSeeder::class);
    $this->withoutMiddleware();

    $selectedTitleIds = SacramentalTitle::query()->limit(1)->pluck('id')->all();

    $response = $this->post(route('register.store'), [
        'name' => 'Maria Souza',
        'email' => 'maria@example.com',
        'birth_date' => '1994-05-22',
        'phone' => '(11) 99999-9999',
        'is_whatsapp' => 'on',
        'instagram' => '@mariasouza',
        'zip_code' => '01001-000',
        'street' => 'Rua A',
        'number' => '10',
        'district' => 'Centro',
        'city' => 'Sao Paulo',
        'state' => 'sp',
        'member_titles' => $selectedTitleIds,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $member = Member::query()
        ->where('email', 'maria@example.com')
        ->firstOrFail();

    expect($member->is_whatsapp)->toBeTrue();
});
