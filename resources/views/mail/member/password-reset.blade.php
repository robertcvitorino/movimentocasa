@component('mail::message')
<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ $logoUrl }}" alt="Movimento Casa" style="max-height: 72px; width: auto;">
</div>

# Olá, {{ $name }}!

Recebemos uma solicitação para redefinir sua senha no Movimento Casa.

@component('mail::button', ['url' => $passwordResetUrl])
Redefinir senha
@endcomponent

Se você não solicitou esta alteração, ignore este e-mail.

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
