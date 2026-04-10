<x-guest-layout>
    <!-- Header avec logo rond -->
    <div class="bg-blue-600 py-6 flex justify-center -mx-6 -mt-4 mb-6 rounded-t-lg">
        <img src="{{ asset('images/TGFpdf.jpg') }}"
            alt="Logo Stage TFG"
            class="h-32 w-32 object-cover rounded-full border-4 border-white shadow-md">
    </div>

    <!-- Titre -->
    <h2 class="text-2xl font-bold text-blue-800 text-center mb-2">Créer un nouveau mot de passe</h2>

    <!-- Description -->
    <p class="text-sm text-gray-600 text-center mb-6">
        Entrez votre nouveau mot de passe ci-dessous.
    </p>

    <form method="POST" action="{{ route('password.update-with-pin') }}" class="space-y-4">
        @csrf

        <!-- Email Hidden -->
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="pin" value="{{ $pin }}">

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Nouveau mot de passe')" class="font-medium text-gray-700" />
            <div class="relative">
                <x-text-input
                    id="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 pr-10"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password" />
                <button type="button" class="absolute right-3 top-3 text-gray-600 hover:text-gray-900 focus:outline-none" onclick="togglePassword('password')">
                    <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="font-medium text-gray-700" />
            <div class="relative">
                <x-text-input
                    id="password_confirmation"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 pr-10"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password" />
                <button type="button" class="absolute right-3 top-3 text-gray-600 hover:text-gray-900 focus:outline-none" onclick="togglePassword('password_confirmation')">
                    <svg id="password_confirmation-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Bouton Réinitialiser -->
        <div class="mt-6">
            <x-primary-button class="w-full text-center py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-md font-semibold transition">
                Changer le mot de passe
            </x-primary-button>
        </div>
    </form>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye');

            if (field.type === 'password') {
                field.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
            } else {
                field.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</x-guest-layout>