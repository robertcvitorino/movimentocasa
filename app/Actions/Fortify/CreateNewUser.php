<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\MemberStatus;
use App\Enums\RoleName;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input['email'] = mb_strtolower(trim((string) ($input['email'] ?? '')));
        $input['is_whatsapp'] = filter_var(
            $input['is_whatsapp'] ?? false,
            FILTER_VALIDATE_BOOL,
            FILTER_NULL_ON_FAILURE
        ) ?? false;

        Validator::make($input, [
            ...$this->profileRules(),
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'phone' => ['required', 'string', 'max:30'],
            'is_whatsapp' => ['nullable', 'boolean'],
            'instagram' => ['required', 'string', 'max:255'],
            'zip_code' => ['required', 'string', 'max:10'],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
            'member_titles' => ['required', 'array', 'min:1'],
            'member_titles.*' => [
                'integer',
                'distinct',
                Rule::exists('sacramental_titles', 'id')->where('is_active', true),
            ],
            'email' => [
                ...$this->emailRules(),
                Rule::unique(Member::class, 'email'),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $user = User::query()->create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'is_active' => true,
            ]);

            $member = Member::query()->create([
                'user_id' => $user->getKey(),
                'full_name' => $user->name,
                'birth_date' => $input['birth_date'],
                'email' => $user->email,
                'phone' => $input['phone'],
                'is_whatsapp' => (bool) ($input['is_whatsapp'] ?? false),
                'instagram' => ltrim((string) $input['instagram'], '@'),
                'zip_code' => $input['zip_code'],
                'street' => $input['street'],
                'number' => $input['number'],
                'complement' => $input['complement'] ?? null,
                'district' => $input['district'],
                'city' => $input['city'],
                'state' => mb_strtoupper((string) $input['state']),
                'status' => MemberStatus::Active,
                'joined_at' => Carbon::today(),
            ]);

            $member->titles()->sync($input['member_titles']);

            Role::findOrCreate(RoleName::Member->value, 'web');
            $user->assignRole(RoleName::Member->value);

            return $user;
        });
    }
}
