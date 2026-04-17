<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $domaine->nom }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Détails du domaine</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ encrypted_route('domaines.edit', $domaine) }}"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition shadow-lg shadow-indigo-600/20 font-medium">
                    Modifier
                </a>
                <a href="{{ route('domaines.index') }}"
                    class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                    Retour
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 ml-4">
        <!-- Informations principales -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Informations</h2>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Nom</label>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $domaine->nom }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</label>
                    <p class="text-gray-600 dark:text-gray-300">{{ $domaine->description ?? 'Aucune description' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Utilisateurs</label>
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-xl text-lg font-semibold">
                            {{ $domaine->users_count }}
                        </span>
                        <a href="{{ route('employes.by_domaine', $domaine) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-1 block">Voir la liste →</a>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Stages</label>
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 rounded-xl text-lg font-semibold">
                            {{ $domaine->stages_count }}
                        </span>
                        <a href="{{ route('stages.index') }}?domaine_id={{ $domaine->id }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline mt-1 block">Voir les stages →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Statistiques</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ $domaine->users_count }}</div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 uppercase tracking-wide font-medium">Employés</p>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-xl">
                    <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">{{ $domaine->stages_count }}</div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 uppercase tracking-wide font-medium">Stages associés</p>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Créé le <span class="font-medium">{{ $domaine->created_at->format('d/m/Y à H:i') }}</span>
                </p>
                @if($domaine->created_by)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Par <span class="font-medium">{{ $domaine->creator->name ?? 'Utilisateur supprimé' }}</span>
                </p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>