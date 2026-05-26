<x-app-layout>
    @php
        $personnel = $user->personnel;
        $displayName = trim(($personnel?->nom ?? '') . ' ' . ($personnel?->prenom ?? ''));
        $displayName = $displayName !== '' ? $displayName : ($user->getRawOriginal('email') ?? $user->email ?? 'Utilisateur');
        $displayEmail = $personnel?->email ?? $user->getRawOriginal('email') ?? $user->email ?? 'Email manquant';
        $initials = strtoupper(substr($personnel?->prenom ?? $displayName, 0, 1) . substr($personnel?->nom ?? '', 0, 1));
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Mon Profil') }}
            </h2>
            <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm">
                {{ $displayName }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Carte d'en-tête -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center gap-6">
                    <!-- Avatar -->
                    <div class="relative">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                        @else
                                    <div class="w-24 h-24 rounded-full bg-blue-300 flex items-center justify-center border-4 border-white shadow-lg">
                                <!-- Utiliser les initiales depuis le personnel -->
                                <span class="text-4xl font-bold text-blue-800">
                                    {{ $initials }}
                                </span>
                            </div>
                        @endif
                        <label for="avatar-upload" class="absolute bottom-0 right-0 bg-white text-blue-600 p-1.5 rounded-full cursor-pointer shadow-lg hover:bg-blue-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </label>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $displayName }}</h1>
                        <p class="text-blue-100">{{ $displayEmail }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            @if(Auth::user()->email_verified_at)
                            
                                <span class="flex items-center gap-1 text-sm bg-green-500 text-white px-2 py-0.5 rounded">Email vérifié</span>
                            @else
                                <span class="flex items-center gap-1 text-sm bg-yellow-500 text-white px-2 py-0.5 rounded">Email non vérifié</span>
                            @endif
                            @if(Auth::user()->getRoleNames()->first())
                                <span class="text-sm bg-purple-500 text-white px-2 py-0.5 rounded">{{ Auth::user()->getRoleNames()->first() }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne gauche : infos + mot de passe -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informations du profil -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Informations du profil
                            </h3>
                        </div>
                        <div class="p-6">
                            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                @method('put')
                                <input type="file" id="avatar-upload" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Nom (personnels) -->
                                    <div>
                                        <x-input-label for="nom" :value="__('Nom')" class="font-medium text-gray-700 dark:text-gray-200 mb-1" />
                                        <x-text-input id="nom" name="nom" type="text" :value="old('nom', $user->personnel?->nom ?? '')" required autofocus class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" />
                                        <x-input-error :messages="$errors->get('nom')" class="mt-1 text-sm" />
                                    </div>

                                    <!-- Prénom (personnels) -->
                                    <div>
                                        <x-input-label for="prenom" :value="__('Prénom')" class="font-medium text-gray-700 dark:text-gray-200 mb-1" />
                                        <x-text-input id="prenom" name="prenom" type="text" :value="old('prenom', $user->personnel?->prenom ?? '')" required class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" />
                                        <x-input-error :messages="$errors->get('prenom')" class="mt-1 text-sm" />
                                    </div>

                                    <!-- Email (personnels) -->
                                    <div>
                                        <x-input-label for="email" :value="__('Adresse email')" class="font-medium text-gray-700 dark:text-gray-200 mb-1" />
                                        <x-text-input id="email" name="email" type="email" :value="old('email', $user->personnel?->email ?? $user->email)" required autocomplete="username" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" />
                                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm" />
                                    </div>

                                    <!-- Téléphone (personnels) -->
                                    <div>
                                        <x-input-label for="phone" :value="__('Téléphone')" class="font-medium text-gray-700 dark:text-gray-200 mb-1" />
                                        <x-text-input id="phone" name="phone" type="tel" :value="old('phone', $user->personnel?->telephone ?? '')" autocomplete="tel" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="+33 6 00 00 00 00" />
                                        <x-input-error :messages="$errors->get('phone')" class="mt-1 text-sm" />
                                    </div>

                                    <!-- Bio (colonne 'bio' dans users, si elle existe) -->
                                    @if(Schema::hasColumn('users', 'bio'))
                                    <div class="md:col-span-2">
                                        <x-input-label for="bio" :value="__('Biographie')" class="font-medium text-gray-700 dark:text-gray-200 mb-1" />
                                        <textarea id="bio" name="bio" rows="3" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Parlez-nous de vous...">{{ old('bio', $user->bio) }}</textarea>
                                        <x-input-error :messages="$errors->get('bio')" class="mt-1 text-sm" />
                                    </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-4 pt-2">
                                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg">{{ __('Enregistrer') }}</x-primary-button>
                                    @if (session('status') === 'profile-updated')
                                        <p class="text-sm text-green-600">Modifications enregistrées</p>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modification du mot de passe -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Sécurité – Modifier le mot de passe
                            </h3>
                        </div>
                        <div class="p-6">
                            <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                                @csrf @method('put')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="update_password_current_password" :value="__('Mot de passe actuel')" class="font-medium text-gray-700 dark:text-gray-200 mb-1" />
                                        <div class="relative">
                                            <x-text-input id="update_password_current_password" name="current_password" type="password" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg pr-10" placeholder="••••••••" />
                                            <button type="button" class="absolute right-3 top-3 text-gray-500" onclick="togglePassword('update_password_current_password', this)">👁️</button>
                                        </div>
                                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1 text-sm" />
                                    </div>
                                    <div>
                                        <x-input-label for="update_password_password" :value="__('Nouveau mot de passe')" class="font-medium text-gray-700 dark:text-gray-200 mb-1" />
                                        <div class="relative">
                                            <x-text-input id="update_password_password" name="password" type="password" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg pr-10" placeholder="••••••••" />
                                            <button type="button" class="absolute right-3 top-3 text-gray-500" onclick="togglePassword('update_password_password', this)">👁️</button>
                                        </div>
                                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1 text-sm" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <x-input-label for="update_password_password_confirmation" :value="__('Confirmer le mot de passe')" class="font-medium text-gray-700 dark:text-gray-200 mb-1" />
                                        <div class="relative">
                                            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg pr-10" placeholder="••••••••" />
                                            <button type="button" class="absolute right-3 top-3 text-gray-500" onclick="togglePassword('update_password_password_confirmation', this)">👁️</button>
                                        </div>
                                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1 text-sm" />
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 pt-2">
                                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg">{{ __('Mettre à jour') }}</x-primary-button>
                                    @if (session('status') === 'password-updated')
                                        <p class="text-sm text-green-600">Mot de passe mis à jour</p>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : activité et zone dangereuse -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Activité du compte
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Membre depuis</span><span class="font-medium text-gray-800 dark:text-white">{{ $user->created_at->format('d/m/Y') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Dernière connexion</span><span class="font-medium text-gray-800 dark:text-white">{{ $user->updated_at->format('d/m/Y à H:i') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Rôle(s)</span><div class="flex flex-wrap gap-1">@foreach($user->getRoleNames() as $role)<span class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs px-2 py-1 rounded">{{ $role }}</span>@endforeach</div></div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-red-200 dark:border-red-800">
                        <div class="bg-red-50 dark:bg-red-900/20 px-6 py-4 border-b border-red-100 dark:border-red-800">
                            <h3 class="text-lg font-semibold text-red-800 dark:text-red-300 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Zone Dangereuse
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Une fois votre compte supprimé, toutes vos données seront définitivement perdues. Cette action est irréversible.</p>
                            <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="w-full justify-center">{{ __('Supprimer mon compte') }}</x-danger-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf @method('delete')
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Veuillez entrer votre mot de passe pour confirmer.') }}</p>
            <div class="mt-6">
                <x-input-label for="password" :value="__('Mot de passe')" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" placeholder="{{ __('Mot de passe') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Annuler') }}</x-secondary-button>
                <x-danger-button class="bg-red-600 hover:bg-red-700">{{ __('Supprimer définitivement') }}</x-danger-button>
            </div>
        </form>
    </x-modal>

    <script>
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const eyeIcon = btn.querySelector('svg');
            if (field.type === 'password') {
                field.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
            } else {
                field.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</x-app-layout>
