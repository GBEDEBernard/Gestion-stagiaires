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
                    <x-text-input id="password" type="password" name="password" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
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
</x-guest-layout>
