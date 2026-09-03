<x-guest-layout>

    <style>
        /* Compactage local à la page register : la carte tient
           sur écran sans défilement, fond image conservé. */
        .tfg-register {
            align-items: center;
            padding: 12px 14px;
        }
        .tfg-register .tfg-card-header {
            padding: 12px 24px 10px;
        }
        .tfg-register .tfg-logo-ring {
            width: 48px;
            height: 48px;
        }
        .tfg-register .tfg-card-eyebrow {
            margin-top: 6px;
        }
        .tfg-register .tfg-card-body {
            padding: 12px 20px 12px;
        }
        .tfg-register .tfg-card-body-title {
            font-size: 1rem;
            margin-bottom: 8px;
        }
        .tfg-register form > div + div {
            margin-top: 6px;
        }
        .tfg-register .tfg-input {
            padding: 6px 12px;
        }
        .tfg-register .tfg-btn-primary {
            padding: 8px 14px;
        }
        .tfg-register .tfg-eye-btn {
            top: 50%;
            transform: translateY(-50%);
        }
        @media (max-width: 380px) {
            .tfg-register {
                padding: 8px;
            }
            .tfg-register .tfg-card-header {
                padding: 10px 14px 8px;
            }
            .tfg-register .tfg-logo-ring {
                width: 42px;
                height: 42px;
            }
            .tfg-register .tfg-card-body {
                padding: 10px 14px 12px;
            }
            .tfg-register .tfg-card-body-title {
                font-size: 0.9rem;
                margin-bottom: 6px;
            }
        }
    </style>

    <div class="tfg-page tfg-register">

        {{-- Fond image pleine page --}}
        <div class="tfg-bg" aria-hidden="true"></div>

        {{-- Carte d'inscription --}}
        <div class="tfg-card">

            <div class="tfg-card-header">
                <div class="tfg-logo-ring">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo Stage TFG">
                </div>
                <span class="tfg-card-eyebrow"><span class="tfg-live-dot"></span>Nouveau compte</span>
                <h2 class="tfg-card-title">TFG Sarl</h2>
            </div>

            <div class="tfg-card-body">

                <h2 class="tfg-card-body-title">Créer un compte</h2>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Nom complet --}}
                    <div>
                        <x-input-label for="name" :value="__('Nom complet')" class="tfg-field-label" />
                        <x-text-input id="name" type="text" name="name"
                            :value="old('name')" required autofocus autocomplete="name"
                            class="tfg-input" placeholder="Votre nom complet" />
                        <x-input-error :messages="$errors->get('name')" class="tfg-error" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="tfg-field-label" />
                        <x-text-input id="email" type="email" name="email"
                            :value="old('email')" required autocomplete="username"
                            class="tfg-input" placeholder="votre@email.com" />
                        <x-input-error :messages="$errors->get('email')" class="tfg-error" />
                    </div>

                    {{-- Mot de passe --}}
                    <div>
                        <x-input-label for="password" :value="__('Mot de passe')" class="tfg-field-label" />
                        <div class="relative">
                            <x-text-input id="password" type="password" name="password" required
                                autocomplete="new-password"
                                class="tfg-input pr-10" placeholder="Minimum 8 caractères" />
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 tfg-eye-btn focus:outline-none" onclick="togglePassword('password')">
                                <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="tfg-error" />
                    </div>

                    {{-- Confirmer le mot de passe --}}
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="tfg-field-label" />
                        <div class="relative">
                            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                                autocomplete="new-password"
                                class="tfg-input pr-10" placeholder="Répétez votre mot de passe" />
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 tfg-eye-btn focus:outline-none" onclick="togglePassword('password_confirmation')">
                                <svg id="password_confirmation-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="tfg-error" />
                    </div>

                    {{-- Conditions d'utilisation --}}
                    <div class="flex items-start">
                        <input type="checkbox" name="terms" id="terms" required class="tfg-checkbox mt-1">
                        <label for="terms" class="ml-2 text-sm text-gray-600">
                            J'accepte les <a href="#" class="tfg-link">conditions d'utilisation</a>
                            et la <a href="#" class="tfg-link">politique de confidentialité</a>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('terms')" class="tfg-error" />

                    {{-- Bouton S'inscrire --}}
                    <div>
                        <button type="submit" class="tfg-btn-primary">
                            Créer mon compte
                        </button>
                    </div>

                    {{-- Déjà inscrit --}}
                    <div class="text-center">
                        <span class="text-sm text-gray-600">Déjà inscrit ? </span>
                        <a href="{{ route('login') }}" class="tfg-link font-semibold">
                            Se connecter
                        </a>
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