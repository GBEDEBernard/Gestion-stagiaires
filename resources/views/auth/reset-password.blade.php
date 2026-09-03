<x-guest-layout>
    <div class="tfg-page">
        <x-tfg-background />

        <div class="tfg-card">
            <div class="tfg-card-header">
                <div class="tfg-logo-ring">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG Sarl">
                </div>
                <span class="tfg-card-eyebrow"><span class="tfg-live-dot"></span>Espace sécurisé</span>
                <h2 class="tfg-card-title">TFG Sarl</h2>
            </div>

            <div class="tfg-card-body">
                <h2 class="tfg-card-body-title">Créer un nouveau mot de passe</h2>
                <p class="tfg-card-description">
                    Entrez votre email et votre nouveau mot de passe pour réinitialiser votre compte.
                </p>

                <x-auth-session-status class="mb-3" :status="session('status')" />

                <form method="POST" action="{{ route('password.store') }}" class="space-y-3">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <x-input-label for="email" :value="__('Email')" class="tfg-field-label" />
                        <x-text-input
                            id="email"
                            type="email"
                            name="email"
                            :value="old('email', $request->email)"
                            required
                            autofocus
                            autocomplete="username"
                            class="tfg-input"
                            placeholder="exemple@domaine.com" />
                        <x-input-error :messages="$errors->get('email')" class="tfg-error" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Mot de passe')" class="tfg-field-label" />
                        <div class="relative">
                            <x-text-input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                class="tfg-input pr-10"
                                placeholder="••••••••" />
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 tfg-eye-btn focus:outline-none" onclick="togglePassword('password')">
                                <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="tfg-error" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="tfg-field-label" />
                        <div class="relative">
                            <x-text-input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                class="tfg-input pr-10"
                                placeholder="••••••••" />
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 tfg-eye-btn focus:outline-none" onclick="togglePassword('password_confirmation')">
                                <svg id="password_confirmation-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="tfg-error" />
                    </div>

                    <div style="margin-top: 18px;">
                        <button type="submit" class="tfg-btn-primary">
                            Réinitialiser le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            if (field.type === 'password') {
                field.type = 'text';
                eye.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                `;
            } else {
                field.type = 'password';
                eye.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</x-guest-layout>