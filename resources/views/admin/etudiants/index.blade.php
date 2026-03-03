<x-app-layout>
    <!-- En-tête -->
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Étudiants</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gérez les étudiants(stagiaires)</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('etudiants.trash') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Corbeille
                </a>
                <a href="{{ route('etudiants.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition shadow-lg shadow-emerald-600/20 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6" />
                    </svg>
                    Nouvel Étudiant
                </a>
            </div>
        </div>
    </div>

    <!-- Tableau moderne -->
    <div class="bg-white ml-4 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prénom</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Genre</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Téléphone</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">École</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($etudiants as $etudiant)
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
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-5 border-t border-gray-100 dark:border-gray-700">
            {{ $etudiants->links() }}
        </div>
    </div>
</x-app-layout>