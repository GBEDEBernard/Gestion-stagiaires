<x-guest-layout>
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- Header avec logo rond -->
        <div class="bg-blue-600 py-6 flex justify-center">
            <img src="{{ asset('images/TGFpdf.jpg') }}"
                alt="Logo Stage TFG"
                class="h-32 w-32 object-cover rounded-full border-4 border-white shadow-md">
        </div>

        <!-- Contenu de la carte -->
        <div class="px-6 py-8">

            <!-- Titre -->
            <h2 class="text-2xl font-bold text-blue-800 text-center mb-5">TFG Gestion Stagiaire</h2>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="font-medium text-gray-700" />
                    <x-text-input id="email" type="email" name="email"
                        :value="old('email')" required autofocus
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Mot de passe')" class="font-medium text-gray-700" />
                    <div class="relative">
                        <x-text-input id="password" type="password" name="password" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 pr-10" />
                        <button type="button" class="absolute right-3 top-3 text-gray-600 hover:text-gray-900 focus:outline-none" onclick="togglePassword('password')">
                            <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Remember Me et Mot de passe oublié -->
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                    </label>

                    @if (Route::has('password.request'))
                    <a class="text-sm text-blue-600 hover:text-blue-800 underline" href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                    @endif
                </div>

                <!-- Bouton Connexion -->
                <div>
                    <x-primary-button class="w-full text-center py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-md font-semibold transition">
                        Se connecter
                    </x-primary-button>
                </div>

                <!-- Premier utilisateur -->
                @if(\App\Models\User::count() === 0)
                <div class="mt-3 text-center">
                    <a href="{{ route('register') }}"
                        class="text-sm text-blue-600 hover:text-blue-800 font-semibold underline">
                        Aucun utilisateur trouvé ? Créer le premier compte (Admin)
                    </a>
                </div>
                @endif

            </form>
        </div>
    </div>

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