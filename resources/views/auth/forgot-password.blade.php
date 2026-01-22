<x-guest-layout>
    <!-- Header avec logo rond -->
    <div class="bg-blue-600 py-6 flex justify-center -mx-6 -mt-4 mb-6 rounded-t-lg">
        <img src="{{ asset('images/TGFpdf.jpg') }}"
            alt="Logo Stage TFG"
            class="h-32 w-32 object-cover rounded-full border-4 border-white shadow-md">
    </div>

    <!-- Titre -->
    <h2 class="text-2xl font-bold text-blue-800 text-center mb-2">Réinitialiser le mot de passe</h2>

    <!-- Description -->
    <p class="text-sm text-gray-600 text-center mb-6">
        Entrez votre adresse email et nous vous enverrons un code PIN à 6 chiffres pour réinitialiser votre mot de passe.
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-medium text-gray-700" />
            <x-text-input id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Bouton Envoyer -->
        <div class="mt-6">
            <x-primary-button class="w-full text-center py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-md font-semibold transition">
                Envoyer le code PIN
            </x-primary-button>
        </div>

        <!-- Retour à la connexion -->
        <div class="text-center mt-4">
            <a href="{{ route('login') }}"
                class="text-sm text-blue-600 hover:text-blue-800 font-semibold underline">
                Retour à la connexion
            </a>
        </div>
    </form>
</x-guest-layout>