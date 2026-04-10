<x-app-layout>
<<<<<<< HEAD
    <!-- En-tête -->
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Étudiants</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gérez les étudiants(stagiaires)</p>
            </div>
            <div class="flex gap-3">
=======
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Etudiants</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gere les stagiaires et leur acces de connexion.</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <form action="{{ route('etudiants.syncAccounts') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-xl hover:bg-blue-200 dark:hover:bg-blue-900/50 transition font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m14.836 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-14.837-2m14.837 2H15" />
                        </svg>
                        Synchroniser les comptes
                    </button>
                </form>
>>>>>>> e9635ab
                <a href="{{ route('etudiants.trash') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Corbeille
                </a>
<<<<<<< HEAD
                <a href="{{ route('etudiants.create') }}"
=======
                <a href="{{ route('admin.users.create', ['role' => 'etudiant']) }}"
>>>>>>> e9635ab
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition shadow-lg shadow-emerald-600/20 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6" />
                    </svg>
<<<<<<< HEAD
                    Nouvel Étudiant
                </a>
            </div>
        </div>
    </div>

    <!-- Tableau moderne -->
=======
                    Nouvel Etudiant
                </a>
            </div>
        </div>

        @if(session('generated_accounts'))
            @php
                $generatedAccounts = session('generated_accounts', []);
            @endphp
            @if(count($generatedAccounts) > 0)
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <h2 class="text-base font-semibold text-amber-800">Comptes stagiaires crees automatiquement</h2>
                    <p class="mt-1 text-sm text-amber-700">Note ces identifiants maintenant, ils ne seront plus affiches ensuite.</p>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-amber-800">
                                    <th class="py-2 pr-4">Etudiant</th>
                                    <th class="py-2 pr-4">Email</th>
                                    <th class="py-2">Mot de passe temporaire</th>
                                    <th class="py-2 pl-4">Verification email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($generatedAccounts as $account)
                                    <tr class="border-t border-amber-200">
                                        <td class="py-2 pr-4 text-amber-900 font-medium">{{ $account['etudiant'] }}</td>
                                        <td class="py-2 pr-4 text-amber-900">{{ $account['email'] }}</td>
                                        <td class="py-2 text-amber-900 font-semibold">{{ $account['password'] }}</td>
                                        <td class="py-2 pl-4 text-amber-900">{{ !empty($account['verification_email_sent']) ? 'Envoye' : 'A verifier manuellement' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    </div>

>>>>>>> e9635ab
    <div class="bg-white ml-4 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
<<<<<<< HEAD
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prénom</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Genre</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Téléphone</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">École</th>
=======
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Identite</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ecole</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Compte</th>
>>>>>>> e9635ab
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($etudiants as $etudiant)
<<<<<<< HEAD
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">#{{ $etudiant->id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $etudiant->nom }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600 dark:text-gray-300">{{ $etudiant->prenom }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium
                                    {{ $etudiant->genre === 'Masculin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400' }}">
                                {{ $etudiant->genre }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600 dark:text-gray-300">{{ $etudiant->email }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600 dark:text-gray-300">{{ $etudiant->telephone ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600 dark:text-gray-300">{{ $etudiant->ecole ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ encrypted_route('etudiants.edit', $etudiant) }}"
                                    class="p-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition" title="Modifier" data-confirm-edit>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ encrypted_route('etudiants.destroy', $etudiant) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292zM15 12H9m4.354-11.646l2.121 2.121a4 4 0 01-5.656 5.656l-2.121-2.121a4 4 0 015.656-5.656z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Aucun étudiant trouvé</p>
                            </div>
                        </td>
                    </tr>
=======
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">#{{ $etudiant->id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $etudiant->nom }} {{ $etudiant->prenom }}</p>
                                    <span class="inline-flex items-center mt-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $etudiant->genre === 'Masculin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400' }}">
                                        {{ $etudiant->genre }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm space-y-1">
                                    <p class="text-gray-600 dark:text-gray-300">{{ $etudiant->email }}</p>
                                    <p class="text-gray-500 dark:text-gray-400">{{ $etudiant->telephone ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600 dark:text-gray-300">{{ $etudiant->ecole ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($etudiant->user)
                                    <div class="text-sm space-y-1">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-medium">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                            Pret a se connecter
                                        </span>
                                        <p class="text-gray-600 dark:text-gray-300">{{ $etudiant->user->email }}</p>
                                    </div>
                                @else
                                    <div class="text-sm space-y-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-medium">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                            Compte manquant
                                        </span>
                                        <form action="{{ encrypted_route('etudiants.syncAccount', $etudiant) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                                Creer / rattacher le compte
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ $etudiant->user ? encrypted_route('admin.users.edit', $etudiant->user) : encrypted_route('etudiants.edit', $etudiant) }}"
                                        class="p-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition" title="Modifier" data-confirm-edit>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ encrypted_route('etudiants.destroy', $etudiant) }}" method="POST" class="inline" data-confirm-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292zM15 12H9m4.354-11.646l2.121 2.121a4 4 0 01-5.656 5.656l-2.121-2.121a4 4 0 015.656-5.656z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Aucun etudiant trouve</p>
                                </div>
                            </td>
                        </tr>
>>>>>>> e9635ab
                    @endforelse
                </tbody>
            </table>
        </div>

<<<<<<< HEAD
        <!-- Pagination -->
=======
>>>>>>> e9635ab
        <div class="p-5 border-t border-gray-100 dark:border-gray-700">
            {{ $etudiants->links() }}
        </div>
    </div>
<<<<<<< HEAD
</x-app-layout>
=======
</x-app-layout>
>>>>>>> e9635ab
