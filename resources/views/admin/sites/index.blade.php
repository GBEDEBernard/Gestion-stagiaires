<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Sites</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gere les lieux de presence et leurs zones GPS.</p>
            </div>
            <a href="{{ route('sites.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition shadow-lg shadow-emerald-600/20 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6" />
                </svg>
                Nouveau Site
            </a>
        </div>
    </div>

    <div class="bg-white ml-4 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ville</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Geofence</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Etat</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($sites as $site)
                        @php
                            $primaryGeofence = $site->geofences->firstWhere('is_primary', true) ?? $site->geofences->first();
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $site->code }}</td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $site->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $site->address ?: 'Adresse non renseignee' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $site->city ?: '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                @if($primaryGeofence)
                                    <div>
                                        <p>{{ $primaryGeofence->name }}</p>
                                        <p class="text-gray-500 dark:text-gray-400">{{ $primaryGeofence->radius_meters }} m / precision {{ $primaryGeofence->allowed_accuracy_meters }} m</p>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $site->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }} rounded-full text-xs font-medium">
                                    <span class="w-1.5 h-1.5 {{ $site->is_active ? 'bg-emerald-500' : 'bg-gray-400' }} rounded-full"></span>
                                    {{ $site->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ encrypted_route('sites.edit', $site) }}"
                                        class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ encrypted_route('sites.destroy', $site) }}" method="POST" class="inline" data-confirm-delete>
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
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
                                Aucun site n'est encore configure.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-gray-100 dark:border-gray-700">
            {{ $sites->links() }}
        </div>
    </div>
</x-app-layout>
