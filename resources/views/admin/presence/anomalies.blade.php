<x-app-layout title="Anomalies de Présence - Admin">

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🚨 Anomalies de Présence
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if($anomalies->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucune anomalie ouverte</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Toutes les anomalies de présence ont été résolues.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Utilisateur</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Événement</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Détecté</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800">
                                    @foreach($anomalies as $anomaly)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $anomaly->attendanceEvent->stage?->etudiant?->nom ?? $anomaly->user?->name ?? 'Inconnu' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $anomaly->attendanceEvent->type }} ({{ $anomaly->attendanceEvent->occurred_at->format('H:i') }})
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $anomaly->detected_at->format('d/m H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">
                                                    {{ ucfirst($anomaly->type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <form action="{{ route('admin.presence.anomalies.resolve', $anomaly->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" 
                                                            onclick="return confirm('Voulez-vous vraiment résoudre cette anomalie ?')"
                                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-200">
                                                        ✓ Résoudre
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-between items-center">
                        <a href="{{ route('admin.presence.index') }}" 
                           class="text-indigo-600 hover:text-indigo-900 flex items-center gap-1">
                            ← Retour Présence
                        </a>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $anomalies->count() }} anomalie(s) ouverte(s)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>