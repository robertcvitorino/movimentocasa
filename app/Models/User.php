<?php

namespace App\Models;

use App\Enums\RoleName;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected string $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->hasAnyRole([
                RoleName::SystemAdmin->value,
                RoleName::GeneralCoordinator->value,
                RoleName::MinistryCoordinator->value,
                RoleName::FinancialCoordinator->value,
            ]),
            'member' => $this->isSystemAdmin()
                || ($this->hasRole(RoleName::Member->value) && $this->member()->exists()),
            default => false,
        };
    }

    public function isSystemAdmin(): bool
    {
        return $this->hasRole(RoleName::SystemAdmin->value);
    }

    public function isGeneralCoordinator(): bool
    {
        return $this->hasRole(RoleName::GeneralCoordinator->value);
    }

    public function isMinistryCoordinator(): bool
    {
        return $this->hasRole(RoleName::MinistryCoordinator->value);
    }

    public function isFinancialCoordinator(): bool
    {
        return $this->hasRole(RoleName::FinancialCoordinator->value);
    }

    public function isMember(): bool
    {
        return $this->hasRole(RoleName::Member->value);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
