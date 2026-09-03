
<x-guest-layout>

    <div class="tfg-page">

        {{-- Fond image pleine page --}}
        <div class="tfg-bg" aria-hidden="true"></div>

        <div class="tfg-card">

            <div class="tfg-card-header">
                <div class="tfg-logo-ring">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG Sarl">
                </div>
                <span class="tfg-card-eyebrow"><span class="tfg-live-dot"></span>Espace sécurisé</span>
                <h2 class="tfg-card-title">TFG Sarl</h2>
            </div>

            <div class="tfg-card-body">

                <h2 class="tfg-card-body-title">Réinitialiser le mot de passe</h2>
                <p class="tfg-card-description">
                    Entrez votre adresse email et nous vous enverrons un code PIN à 6 chiffres pour réinitialiser votre mot de passe.
                </p>

                <x-auth-session-status class="mb-3" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email')" class="tfg-field-label" />
                        <x-text-input id="email" type="email" name="email"
                            :value="old('email')" required autofocus
                            autocomplete="username" class="tfg-input"
                            placeholder="exemple@domaine.com" />
                        <x-input-error :messages="$errors->get('email')" class="tfg-error" />
                    </div>

                    <div style="margin-top: 18px;">
                        <button type="submit" class="tfg-btn-primary">
                            Envoyer le code PIN
                        </button>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}" class="tfg-link">
                            ← Retour à la connexion
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-guest-layout>