<x-guest-layout>
    <!-- Header avec logo rond -->
    <div class="bg-blue-600 py-6 flex justify-center -mx-6 -mt-4 mb-6 rounded-t-lg">
        <img src="{{ asset('images/TGFpdf.jpg') }}"
            alt="Logo Stage TFG"
            class="h-32 w-32 object-cover rounded-full border-4 border-white shadow-md">
    </div>

    <!-- Titre -->
    <h2 class="text-2xl font-bold text-blue-800 text-center mb-2">Vérifier votre email</h2>

    <!-- Description -->
    <p class="text-sm text-gray-600 text-center mb-6">
        Un code PIN à 4 chiffres a été envoyé à <strong>{{ $email }}</strong>. Entrez-le ci-dessous pour vérifier votre email.
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('verification.pin.verify') }}" class="space-y-4">
        @csrf

        <!-- Email Hidden -->
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- PIN -->
        <div>
            <x-input-label for="pin" :value="__('Code PIN (4 chiffres)')" class="font-medium text-gray-700" />
            <x-text-input
                id="pin"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-center text-3xl tracking-widest"
                type="text"
                name="pin"
                :value="old('pin')"
                required
                autofocus
                maxlength="4"
                inputmode="numeric"
                pattern="[0-9]{4}"
                placeholder="0000" />
            <x-input-error :messages="$errors->get('pin')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Bouton Vérifier -->
        <div class="mt-6">
            <x-primary-button class="w-full text-center py-3 text-white bg-blue-600 hover:bg-blue-700 rounded-md font-semibold transition">
                Vérifier le code
            </x-primary-button>
        </div>
    </form>

    <!-- Renvoyer le code -->
    <div class="mt-6 text-center">
        <form method="POST" action="{{ route('verification.pin.resend') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 font-semibold underline">
                Renvoyer le code
            </button>
        </form>
    </div>

    <!-- Retour à l'inscription -->
    <div class="text-center mt-4">
        <a href="{{ route('register') }}"
            class="text-sm text-gray-500 hover:text-gray-700 font-semibold">
            ← Retour à l'inscription
        </a>
    </div>

    <script>
        // Autoriser uniquement les chiffres
        document.getElementById('pin').addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Auto-focus et navigation entre les champs
        document.getElementById('pin').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</x-guest-layout>