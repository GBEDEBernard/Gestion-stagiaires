<x-app-layout>
    <div class="mb-8 ml-4">
        <!-- En-tête avec actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Fiche du personnel</h1>
                <p class="text-gray-500 dark:text-gray-300 mt-1">Détails centralisés du personnel et des informations liées à son type.</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('personnels.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour
                </a>
                <a href="{{ route('personnels.edit', $personnel) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-xl hover:bg-yellow-200 dark:hover:bg-yellow-800/50 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
            </div>
        </div>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-xl">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-xl">{{ session('error') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Carte profil gauche - Avatar + infos clés -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="relative h-28 bg-gradient-to-r from-sky-400 to-blue-600 dark:from-sky-600 dark:to-blue-800"></div>
                <div class="px-6 pb-6 relative">
                    <div class="flex justify-center -mt-12 mb-4">
                        <div class="w-24 h-24 rounded-full bg-white dark:bg-gray-800 p-1 shadow-lg">
                            <div class="w-full h-full rounded-full bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center text-white text-2xl font-bold">
                                {{ strtoupper(substr($personnel->prenom, 0, 1)) }}{{ strtoupper(substr($personnel->nom, 0, 1)) }}
                            </div>
                        </div>
                    </div>
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $personnel->prenom }} {{ $personnel->nom }}</h2>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold mt-2 
                            @if($personnel->type_label === 'Employé') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                            @elseif($personnel->type_label === 'Étudiant') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                            @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                            @endif">
                            {{ $personnel->type_label }}
                        </span>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $personnel->email }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>{{ $personnel->telephone ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ $personnel->genre ?? 'Non spécifié' }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>{{ $personnel->user ? 'Compte actif' : 'Aucun compte associé' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails droite -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:col-span-2">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informations complémentaires
                </h2>
                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 text-sm mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Date de naissance</span>
                        </div>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $personnel->date_naissance ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 text-sm mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Adresse</span>
                        </div>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $personnel->adresse ?? '-' }}</p>
                    </div>

                    @if($personnel->personnable_type === App\Models\Etudiant::class)
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 md:col-span-2">
                            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 text-sm mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                                <span>École</span>
                            </div>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $personnel->personnable->ecole ?? '-' }}</p>
                        </div>
                    @elseif($personnel->personnable_type === App\Models\Employe::class)
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4">
                            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 text-sm mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>Poste</span>
                            </div>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $personnel->personnable->poste ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4">
                            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 text-sm mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-4 0h4" />
                                </svg>
                                <span>Matricule</span>
                            </div>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $personnel->personnable->matricule ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4">
                            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 text-sm mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Domaine</span>
                            </div>
                            <p class="text-gray-900 dark:text-white font-medium">{{ optional($personnel->personnable->domaine)->nom ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4">
                            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 text-sm mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>Site</span>
                            </div>
                            <p class="text-gray-900 dark:text-white font-medium">{{ optional($personnel->personnable->site)->name ?? '-' }}</p>
                        </div>
                    @endif
                </div>

                <!-- Actions supplémentaires -->
                <div class="mt-8 flex flex-wrap gap-3 border-t border-gray-100 dark:border-gray-700 pt-6">
                    @if(!$personnel->user)
                        <form action="{{ route('personnels.generate-account', $personnel) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 text-white rounded-xl hover:from-sky-600 hover:to-blue-700 transition shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Générer un compte
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('personnels.destroy', $personnel) }}" method="POST" onsubmit="return confirm('Supprimer ce personnel ?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-300 rounded-xl hover:bg-red-200 dark:hover:bg-red-800/50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>