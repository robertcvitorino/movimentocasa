<?php

namespace App\Filament\Resources\Members\Pages;

use App\Actions\Member\CreateMemberUserAction;
use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use App\Models\User;
use App\Notifications\MemberAccountCreatedNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Password;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected ?User $createdUser = null;

    protected ?string $temporaryPassword = null;

    protected ?string $passwordResetUrl = null;

    protected function handleRecordCreation(array $data): Model
    {
        $account = app(CreateMemberUserAction::class)->execute($data);

        $this->createdUser = $account['user'];
        $this->temporaryPassword = $account['temporary_password'];

        $token = Password::broker()->createToken($this->createdUser);

        $this->passwordResetUrl = route('password.reset', [
            'token' => $token,
            'email' => $this->createdUser->email,
        ]);

        return Member::query()->create([
            ...$data,
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
