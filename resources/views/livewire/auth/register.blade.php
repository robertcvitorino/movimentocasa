<x-layouts::auth :title="__('Cadastro')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Crie sua conta de membro')" :description="__('Preencha o formulario para se cadastrar')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="name"
                :label="__('Nome completo')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Seu nome completo')"
            />

            <flux:input
                name="email"
                :label="__('E-mail')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@exemplo.com"
            />

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input
                    name="birth_date"
                    :label="__('Data de nascimento')"
                    :value="old('birth_date')"
                    type="date"
                    required
                />

                <flux:input
                    name="phone"
                    :label="__('Telefone')"
                    :value="old('phone')"
                    type="text"
                    required
                    autocomplete="tel"
                    inputmode="numeric"
                    maxlength="15"
                    placeholder="(11) 99999-9999"
                />
            </div>

            <flux:checkbox
                name="is_whatsapp"
                :label="__('Este telefone e WhatsApp')"
                :checked="old('is_whatsapp')"
            />

            <flux:input
                name="instagram"
                :label="__('Instagram')"
                :value="old('instagram')"
                type="text"
                required
                placeholder="@seuperfil"
            />

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input
                    name="zip_code"
                    :label="__('CEP')"
                    :value="old('zip_code')"
                    type="text"
                    required
                    inputmode="numeric"
                    maxlength="9"
                    placeholder="00000-000"
                />

                <flux:input
                    name="state"
                    :label="__('Estado (UF)')"
                    :value="old('state')"
                    type="text"
                    required
                    maxlength="2"
                    placeholder="SP"
                />
            </div>

            <flux:input
                name="street"
                :label="__('Rua')"
                :value="old('street')"
                type="text"
                required
            />

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input
                    name="number"
                    :label="__('Numero')"
                    :value="old('number')"
                    type="text"
                    required
                />

                <flux:input
                    name="complement"
                    :label="__('Complemento')"
                    :value="old('complement')"
                    type="text"
                />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input
                    name="district"
                    :label="__('Bairro')"
                    :value="old('district')"
                    type="text"
                    required
                />

                <flux:input
                    name="city"
                    :label="__('Cidade')"
                    :value="old('city')"
                    type="text"
                    required
                />
            </div>

            <div class="space-y-3">
                <flux:text class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                    {{ __('Sacramentos') }}
                </flux:text>
                <flux:text class="text-xs text-zinc-600 dark:text-zinc-400">
                    {{ __('Selecione pelo menos um sacramento.') }}
                </flux:text>

                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($sacramentalTitles as $title)
                        <flux:checkbox
                            name="member_titles[]"
                            :value="$title->id"
                            :label="$title->name"
                            :checked="in_array((string) $title->id, old('member_titles', []), true)"
                        />
                    @endforeach
                </div>

                @error('member_titles')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('member_titles.*')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <flux:input
                name="password"
                :label="__('Senha')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Senha')"
                viewable
            />

            <flux:input
                name="password_confirmation"
                :label="__('Confirmar senha')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirmar senha')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Criar conta') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Ja tem uma conta?') }}</span>
            <flux:link :href="route('home')" wire:navigate>{{ __('Entrar') }}</flux:link>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const phoneInput = document.querySelector('input[name="phone"]');
            const zipInput = document.querySelector('input[name="zip_code"]');
            const streetInput = document.querySelector('input[name="street"]');
            const districtInput = document.querySelector('input[name="district"]');
            const cityInput = document.querySelector('input[name="city"]');
            const stateInput = document.querySelector('input[name="state"]');

            const setFieldValue = (field, value) => {
                if (!field) {
                    return;
                }

                field.value = value ?? '';
                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.dispatchEvent(new Event('change', { bubbles: true }));
            };

            const maskPhone = (value) => {
                const digits = value.replace(/\D/g, '').slice(0, 11);

                if (digits.length <= 10) {
                    return digits
                        .replace(/^(\d{2})(\d)/, '($1) $2')
                        .replace(/(\d{4})(\d)/, '$1-$2');
                }

                return digits
                    .replace(/^(\d{2})(\d)/, '($1) $2')
                    .replace(/(\d{5})(\d)/, '$1-$2');
            };

            const maskZipCode = (value) => {
                const digits = value.replace(/\D/g, '').slice(0, 8);
                return digits.replace(/(\d{5})(\d)/, '$1-$2');
            };

            phoneInput?.addEventListener('input', (event) => {
                event.target.value = maskPhone(event.target.value);
            });

            zipInput?.addEventListener('input', (event) => {
                event.target.value = maskZipCode(event.target.value);
            });

            zipInput?.addEventListener('blur', async (event) => {
                const cep = event.target.value.replace(/\D/g, '');

                if (cep.length !== 8) {
                    return;
                }

                try {
                    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();

                    if (data.erro) {
                        return;
                    }

                    setFieldValue(streetInput, data.logradouro ?? '');
                    setFieldValue(districtInput, data.bairro ?? '');
                    setFieldValue(cityInput, data.localidade ?? '');
                    setFieldValue(stateInput, (data.uf ?? '').toUpperCase());
                } catch (error) {
                    console.error('Falha ao consultar CEP no ViaCEP.', error);
                }
            });
        });
    </script>
</x-layouts::auth>
