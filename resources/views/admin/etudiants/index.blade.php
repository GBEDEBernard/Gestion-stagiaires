<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Étudiants</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gestion des stagiaires et génération de leurs comptes</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('etudiants.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition shadow-lg font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Nouvel étudiant
                </a>
                <a href="{{ route('etudiants.trash') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition">Corbeille</a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-xl">{{ session('success') }}</div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Identité</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">École</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Compte</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                       @forelse($etudiants as $etudiant)
                        @php $personnel = $etudiant->personnel; @endphp
                        @if(!$personnel)
                            <tr><td colspan="6" class="text-red-500">Erreur : personnel introuvable pour étudiant #{{ $etudiant->id }}</td></tr>
                            @continue
                        @endif
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-500">#{{ $etudiant->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $personnel->prenom }} {{ $personnel->nom }}</div>
                                    @if($personnel->genre)
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">{{ $personnel->genre }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div>{{ $personnel->email }}</div>
                                    <div class="text-gray-500">{{ $personnel->telephone ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $etudiant->ecole ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($personnel->user)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                            Compte actif
                                        </span>
                                    @else
                                        <form action="{{ route('etudiants.generate-account', $etudiant) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-blue-600 hover:underline">Générer le compte</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('etudiants.show', $etudiant) }}" class="inline-block p-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200" title="Voir">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ route('etudiants.edit', $etudiant) }}" class="inline-block p-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 rounded-lg hover:bg-yellow-200" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form action="{{ route('etudiants.destroy', $etudiant) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement cet étudiant ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-lg hover:bg-red-200" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-16 text-center text-gray-500">Aucun étudiant trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t border-gray-100 dark:border-gray-700">{{ $etudiants->links() }}</div>
        </div>
    </div>
</x-app-layout>