<x-app-layout>
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('etudiants.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Corbeille</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Étudiants supprimés</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-xl text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ $etudiants->total() }} éléments
                </span>
            </div>
        </div>
    </div>

    <!-- Tableau moderne -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-red-50 dark:bg-red-900/20">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">Étudiant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">Genre</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">École</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">Supprimé le</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($etudiants as $etudiant)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-orange-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr($etudiant->prenom, 0, 1) }}{{ substr($etudiant->nom, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $etudiant->nom }} {{ $etudiant->prenom }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">ID: #{{ $etudiant->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($etudiant->genre === 'Masculin') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                    @else bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400
                                    @endif">
                                {{ $etudiant->genre }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $etudiant->email }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $etudiant->telephone ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $etudiant->ecole ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $etudiant->deleted_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $etudiant->deleted_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Restaurer -->
                                <form action="{{ route('etudiants.restore', $etudiant->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="p-2 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition" title="Restaurer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                </form>

                                <!-- Supprimer définitivement -->
                                <form action="{{ route('etudiants.forceDelete', $etudiant->id) }}" method="POST" class="inline" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition" title="Supprimer définitivement">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Corbeille vide</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Aucun étudiant supprimé</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($etudiants->hasPages())
        <div class="p-5 border-t border-gray-100 dark:border-gray-700">
            {{ $etudiants->links() }}
        </div>
        @endif
    </div>
</x-app-layout>