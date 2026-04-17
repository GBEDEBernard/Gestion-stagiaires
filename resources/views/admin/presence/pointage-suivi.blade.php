<x-app-layout title="Suivi Pointage - Admin">

    <x-slot name="header">
        <div class="flex justify-between items-start gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">📍 Suivi Pointage Temps Réel</h1>
                <p class="mt-1 text-xl text-slate-600">Tous les pointages récents • Géolocalisés • Filtrables</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.presence.index') }}"
                    class="px-6 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                    ← Retour Suivi Pro
                </a>
                <a href="{{ route('admin.presence.export-pointages') }}"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                    📥 Exporter CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- Filtres --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 shadow-sm p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Date</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-white transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Utilisateur</label>
                    <select name="user_id" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Tous</option>
                        @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end gap-3">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all">
                        🔍 Filtrer
                    </button>
                    <a href="{{ route('admin.presence.pointage-suivi') }}" class="px-6 py-2.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-semibold rounded-xl shadow-sm transition-all">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Stats rapides --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-stats-card title="Pointages Aujourd'hui" value="{{ $todayCount ?? 0 }}" icon="map-pin" color="emerald">
                <x-slot:subtitle>{{ $checkinsToday ?? 0 }} entrées / {{ $checkoutsToday ?? 0 }} sorties</x-slot:subtitle>
            </x-stats-card>
            <x-stats-card title="Anomalies Détectées" value="{{ $recentAnomalies ?? 0 }}" icon="exclamation-triangle" color="rose">
                <x-slot:subtitle>Sur {{ $periodDays ?? 7 }} derniers jours</x-slot:subtitle>
            </x-stats-card>
            <x-stats-card title="Précision Moyenne" value="{{ number_format($avgAccuracy ?? 0, 0) }}m" icon="target" color="blue">
                <x-slot:subtitle>GPS précision localisation</x-slot:subtitle>
            </x-stats-card>
        </div>

        {{-- Tableau pointages --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 dark:bg-gray-700">
                <h3 class="font-bold text-lg text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    📋 Historique Récent ({{ $events->count() }} résultats)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Heure</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Site</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Précision</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($events ?? [] as $event)
                        <tr class="hover:bg-slate-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">
                                {{ $event->user?->name ?? $event->attendanceDay->etudiant?->user?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $event->event_type === 'check_in' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                    {{ $event->event_type === 'check_in' ? 'Entrée' : 'Sortie' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">
                                {{ $event->occurred_at?->format('d/m H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ $event->attendanceDay?->stage?->site?->nom ?? 'Hors site' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-cyan-100 text-cyan-800 text-xs font-semibold rounded-full dark:bg-cyan-900 dark:text-cyan-200">
                                    {{ $event->gps_accuracy ?? 'N/A' }}m
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $event->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900' : 'bg-amber-100 text-amber-800 dark:bg-amber-900' }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2 justify-end">
                                    @if($event->anomalies->count() > 0)
                                    <span class="text-rose-600 dark:text-rose-400 font-semibold">{{ $event->anomalies->count() }} anomalie{{ $event->anomalies->count() > 1 ? 's' : '' }}</span>
                                    @endif
                                    <a href="#" class="text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 px-3 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                        Détails
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0 -3.332.477 -4.5 1.253" />
                                </svg>
                                Aucun pointage trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($events->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-700 border-t border-slate-200 dark:border-slate-600">
                {{ $events->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

    </div>

    @push('scripts')
    <script>
        // Auto-refresh toutes les 30s pour temps réel
        setInterval(() => {
            if (window.location.pathname.includes('pointage-suivi')) {
                location.reload();
            }
        }, 30000);
    </script>
    @endpush>

</x-app-layout>