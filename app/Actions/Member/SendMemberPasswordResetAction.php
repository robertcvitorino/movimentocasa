<?php

namespace App\Actions\Member;

use App\Models\Member;
use App\Notifications\MemberPasswordResetNotification;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class SendMemberPasswordResetAction
{
    public function execute(Member $member): void
    {
        $user = $member->user;

        if (! $user || blank($user->email)) {
            throw ValidationException::withMessages([
                'member' => 'Membro sem usuario com e-mail valido para reset de senha.',
            ]);
        }

        $token = Password::broker()->createToken($user);
        $memberPanel = Filament::getPanel('member');

        if (! $memberPanel) {
            throw ValidationException::withMessages([
                'member' => 'Painel de membros nao encontrado para gerar link de redefinicao.',
            ]);
        }

        $passwordResetUrl = $memberPanel->getResetPasswordUrl($token, $user);

        $user->notify(new MemberPasswordResetNotification(
            passwordResetUrl: $passwordResetUrl,
        ));
    }
}
