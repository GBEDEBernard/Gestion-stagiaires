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

        {{-- Carte PIN --}}
        <div class="tfg-card">

            <div class="tfg-card-header">
                <div class="tfg-logo-ring">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG Sarl">
                </div>
                <span class="tfg-card-eyebrow"><span class="tfg-live-dot"></span>Espace sécurisé</span>
                <h2 class="tfg-card-title">TFG Sarl</h2>
            </div>

            <div class="tfg-card-body">

                <h2 class="tfg-card-body-title">Vérifier le code PIN</h2>
                <p class="tfg-card-description">
                    Un code PIN a été envoyé à <strong>{{ $email }}</strong>. Entrez-le ci-dessous.
                </p>

                <x-auth-session-status class="mb-3" :status="session('status')" />

                <form method="POST" action="{{ route('password.verify-pin') }}" class="space-y-4">
                    @csrf

                    <input type="hidden" name="email" value="{{ $email }}">

                    <div>
                        <x-input-label for="pin" :value="__('Code PIN (6 chiffres)')" class="tfg-field-label" />
                        <x-text-input id="pin" type="text" name="pin"
                            :value="old('pin')" required autofocus
                            maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                            class="tfg-input text-center text-2xl tracking-widest"
                            placeholder="000000" />
                        <x-input-error :messages="$errors->get('pin')" class="tfg-error" />
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="tfg-btn-primary">
                            Vérifier le code
                        </button>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('password.request') }}" class="tfg-link">
                            ← Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pinInput = document.getElementById('pin');
            if (pinInput) {
                pinInput.addEventListener('keypress', function(e) {
                    if (!/[0-9]/.test(e.key)) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</x-guest-layout>