<?php

namespace App\Filament\Resources\Members\Pages;

use App\Actions\Member\CreateMemberUserAction;
use App\Filament\Resources\Members\Pages\Concerns\InteractsWithMemberUserData;
use App\Filament\Resources\Members\MemberResource;
use App\Enums\MemberStatus;
use App\Models\Member;
use App\Models\User;
use App\Notifications\MemberAccountCreatedNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;

class CreateMember extends CreateRecord
{
    use InteractsWithMemberUserData;

    protected static string $resource = MemberResource::class;

    protected ?User $createdUser = null;

    protected ?string $temporaryPassword = null;

    protected ?string $passwordResetUrl = null;

    protected function handleRecordCreation(array $data): Model
    {
        $account = app(CreateMemberUserAction::class)->execute($this->extractUserData($data));

        $this->createdUser = $account['user'];
        $this->temporaryPassword = $account['temporary_password'];

        $token = Password::broker()->createToken($this->createdUser);

        $this->passwordResetUrl = route('password.reset', [
            'token' => $token,
            'email' => $this->createdUser->email,
        ]);

        $memberData = $this->extractMemberData($data);
        $memberData['status'] ??= MemberStatus::Active->value;
        $memberData['joined_at'] ??= Carbon::today()->toDateString();

        return Member::query()->create([
            ...$memberData,
            'full_name' => $this->createdUser->name,
            'email' => $this->createdUser->email,
            'user_id' => $this->createdUser->getKey(),
        ]);
    }

    protected function afterCreate(): void
    {
        if (! $this->createdUser || ! $this->temporaryPassword || ! $this->passwordResetUrl) {
            return;
        }

        $this->createdUser->notify(new MemberAccountCreatedNotification(
            temporaryPassword: $this->temporaryPassword,
            passwordResetUrl: $this->passwordResetUrl,
        ));
    }
}
