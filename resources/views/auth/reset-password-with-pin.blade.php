<x-guest-layout>
    <div class="tfg-page">

        {{-- Fond animé partagé --}}
        <div class="tfg-bg" aria-hidden="true">
            <div class="tfg-blob tfg-blob-1"></div>
            <div class="tfg-blob tfg-blob-2"></div>
            <div class="tfg-blob tfg-blob-3"></div>
            <div class="tfg-blob tfg-blob-4"></div>
            <div class="tfg-grid"></div>

            <span class="tfg-watermark tfg-wm-1">TFG Sarl</span>
            <span class="tfg-watermark tfg-wm-2">TFG Sarl</span>
            <span class="tfg-watermark tfg-wm-3">TFG Sarl</span>
            <span class="tfg-watermark tfg-wm-4">TFG Sarl</span>
            <span class="tfg-watermark tfg-wm-5">TFG Sarl</span>
            <span class="tfg-watermark tfg-wm-6">TFG Sarl</span>
            <span class="tfg-watermark tfg-wm-7">TFG Sarl</span>
            <span class="tfg-watermark tfg-wm-8">TFG Sarl</span>
            <span class="tfg-watermark tfg-wm-9">TFG Sarl</span>
            <span class="tfg-watermark tfg-wm-10">TFG Sarl</span>
        </div>

        {{-- Carte de réinitialisation --}}
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
                    Entrez votre nouveau mot de passe ci-dessous.
                </p>

                <x-auth-session-status class="mb-3" :status="session('status')" />

                <form method="POST" action="{{ route('password.update-with-pin') }}" class="space-y-4">
                    @csrf

                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="pin" value="{{ $pin }}">

                    {{-- Nouveau mot de passe --}}
                    <div>
                        <x-input-label for="password" :value="__('Nouveau mot de passe')" class="tfg-field-label" />
                        <div class="relative">
                            <x-text-input id="password" type="password" name="password" required
                                class="tfg-input pr-10" placeholder="••••••••" />
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 tfg-eye-btn focus:outline-none" onclick="togglePassword('password')">
                                <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="tfg-error" />
                    </div>

                    {{-- Confirmation --}}
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="tfg-field-label" />
                        <div class="relative">
                            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                                class="tfg-input pr-10" placeholder="••••••••" />
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 tfg-eye-btn focus:outline-none" onclick="togglePassword('password_confirmation')">
                                <svg id="password_confirmation-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="tfg-error" />
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="tfg-btn-primary">
                            Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye');

            if (field.type === 'password') {
                field.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                `;
            } else {
                field.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</x-guest-layout>