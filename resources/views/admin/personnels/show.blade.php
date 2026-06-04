<x-app-layout>
    @php
        // Le personnel est passé depuis le contrôleur
        $personnel = $personnel;
        $user = $personnel->user; // Peut être null

        $displayName = trim(($personnel->nom ?? '') . ' ' . ($personnel->prenom ?? ''));
        $displayName = $displayName !== '' ? $displayName : ($user?->email ?? 'Personnel');
        $displayEmail = $personnel->email ?? $user?->email ?? 'Email manquant';
        $initials = strtoupper(substr($personnel->prenom ?? $displayName, 0, 1) . substr($personnel->nom ?? '', 0, 1));
        $roleLabels = $user?->getRoleNames()?->implode(', ') ?? 'Aucun rôle';
        $statusColor = ($user?->status ?? null) === 'actif' ? 'bg-green-500' : 'bg-red-500';
        $statusText = ($user?->status ?? null) === 'actif' ? 'Actif' : 'Inactif';
        $typeLabel = $personnel->type_label;
        $typeColor = match($typeLabel) {
            'Étudiant' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'Employé'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
            default    => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        };

        // Gestion robuste de la date de naissance
        $dateNaissanceFormatted = '-';
        $rawDate = $personnel->date_naissance;
        if ($rawDate) {
            if ($rawDate instanceof \DateTimeInterface) {
                $dateNaissanceFormatted = $rawDate->format('d/m/Y');
            } elseif (is_string($rawDate)) {
                try {
                    $dateNaissanceFormatted = \Carbon\Carbon::parse($rawDate)->format('d/m/Y');
                } catch (\Exception $e) {
                    $dateNaissanceFormatted = $rawDate;
                }
            }
        }

        // Superviseur (si profil existe)
        $profil = $personnel->personnable; // Directement le profil (Etudiant ou Employe)
        $supervisorName = '-';
        if ($profil instanceof \App\Models\Etudiant && $profil->supervisor_id) {
            $supervisorName = optional($profil->supervisor)->name ?? '-';
        } elseif ($profil instanceof \App\Models\Employe && $profil->supervisor_id) {
            $supervisorName = optional($profil->supervisor)->name ?? '-';
        }
    @endphp

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center gap-6">
                    <div class="relative">
                        @if($user && $user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                        @else
                            <div class="w-24 h-24 rounded-full bg-blue-300 flex items-center justify-center border-4 border-white shadow-lg">
                                <span class="text-4xl font-bold text-blue-800">{{ $initials }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $displayName }}</h1>
                        <p class="text-blue-100">{{ $displayEmail }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            @if($user)
                                <span class="flex items-center gap-1 text-sm {{ $statusColor }} text-white px-2 py-0.5 rounded">{{ $statusText }}</span>
                                @if($user->hasVerifiedEmail())
                                    <span class="flex items-center gap-1 text-sm bg-green-500 text-white px-2 py-0.5 rounded">Email vérifié</span>
                                @else
                                    <span class="flex items-center gap-1 text-sm bg-yellow-500 text-white px-2 py-0.5 rounded">Email non vérifié</span>
                                @endif
                                @if($roleLabels && $roleLabels !== 'Aucun rôle')
                                    <span class="text-sm bg-purple-500 text-white px-2 py-0.5 rounded">{{ $roleLabels }}</span>
                                @endif
                            @else
                                <span class="flex items-center gap-1 text-sm bg-gray-500 text-white px-2 py-0.5 rounded">Aucun compte</span>
                            @endif
                            <span class="text-sm {{ $typeColor }} px-2 py-0.5 rounded">{{ $typeLabel }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne gauche -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Infos personnelles -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Informations personnelles
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">Nom</label><p class="text-gray-900 dark:text-white font-medium">{{ $personnel->nom ?? '-' }}</p></div>
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">Prénom</label><p class="text-gray-900 dark:text-white font-medium">{{ $personnel->prenom ?? '-' }}</p></div>
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">Email</label><p class="text-gray-900 dark:text-white font-medium">{{ $displayEmail }}</p></div>
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">Téléphone</label><p class="text-gray-900 dark:text-white font-medium">{{ $personnel->telephone ?? '-' }}</p></div>
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">Genre</label><p class="text-gray-900 dark:text-white font-medium">{{ $personnel->genre ?? '-' }}</p></div>
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">Date de naissance</label><p class="text-gray-900 dark:text-white font-medium">{{ $dateNaissanceFormatted }}</p></div>
                                @if($personnel->adresse)
                                <div class="md:col-span-2"><label class="block text-sm text-gray-500 dark:text-gray-400">Adresse</label><p class="text-gray-900 dark:text-white font-medium">{{ $personnel->adresse }}</p></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Infos spécifiques -->
                    @if($profil instanceof \App\Models\Etudiant)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                                Informations étudiant
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-4">
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">École</label><p class="text-gray-900 dark:text-white font-medium">{{ $profil->ecole ?? '-' }}</p></div>
                            </div>
                        </div>
                    </div>
                    @elseif($profil instanceof \App\Models\Employe)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Informations professionnelles
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">Poste</label><p class="text-gray-900 dark:text-white font-medium">{{ $profil->poste ?? '-' }}</p></div>
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">Matricule</label><p class="text-gray-900 dark:text-white font-medium">{{ $profil->matricule ?? '-' }}</p></div>
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">Domaine</label><p class="text-gray-900 dark:text-white font-medium">{{ optional($profil->domaine)->nom ?? '-' }}</p></div>
                                <div><label class="block text-sm text-gray-500 dark:text-gray-400">Site</label><p class="text-gray-900 dark:text-white font-medium">{{ optional($profil->site)->name ?? '-' }}</p></div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Colonne droite -->
                <div class="space-y-6">
                    @if($user)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Activité du compte
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Créé le</span><span class="font-medium text-gray-800 dark:text-white">{{ $user->created_at->format('d/m/Y') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Dernière mise à jour</span><span class="font-medium text-gray-800 dark:text-white">{{ $user->updated_at->format('d/m/Y') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Statut</span><span class="font-medium text-gray-800 dark:text-white {{ $user->status === 'actif' ? 'text-green-600' : 'text-red-600' }}">{{ $statusText }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Mot de passe</span><span class="font-medium text-gray-800 dark:text-white">{{ $user->must_change_password ? 'Temporaire' : 'Permanent' }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Rôle(s)</span><div class="flex flex-wrap gap-1">@foreach($user->getRoleNames() as $role)<span class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs px-2 py-1 rounded">{{ $role }}</span>@endforeach</div></div>
                            <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Superviseur attitré</span><span class="font-medium text-gray-800 dark:text-white">{{ $supervisorName }}</span></div>
                        </div>
                    </div>
                    @else
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Compte utilisateur
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-center text-gray-500 dark:text-gray-400 mb-4">Aucun compte utilisateur associé à ce personnel.</p>
                            <form action="{{ route('personnels.generate-account', $personnel) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-500 to-blue-600 text-white rounded-xl hover:from-sky-600 hover:to-blue-700 transition shadow-md text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Générer un compte
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                                Actions
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('personnels.edit', $personnel) }}" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-xl hover:bg-yellow-200 dark:hover:bg-yellow-800/50 transition text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Modifier le personnel
                            </a>

                            <form action="{{ route('personnels.destroy', $personnel) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce personnel ? Cette action est irréversible.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-300 rounded-xl hover:bg-red-200 dark:hover:bg-red-800/50 transition text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>