@component('mail::message')
<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ $logoUrl }}" alt="Movimento Casa" style="max-height: 72px; width: auto;">
</div>

# Olá, {{ $name }}!

Recebemos seu cadastro no Movimento Casa.

@component('mail::button', ['url' => $verificationUrl])
Verificar e-mail
@endcomponent

Se você não criou esta conta, pode ignorar este e-mail.

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
