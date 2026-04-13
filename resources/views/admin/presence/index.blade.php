<x-app-layout title="Supervision Présences">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Supervision des présences</h1>
                <p class="mt-2 text-xl text-slate-600">Statistiques et suivi en temps réel</p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.presence.export') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10l-5.5 5.5m0 0L12 21l5.5-5.5m-5.5 5.5V8a1 1 0 011-1h6a1 1 0 011 1v5"></path>
                    </svg>
                    Exporter CSV
                </a>
                <a href="{{ route('admin.presence.anomalies') }}" class="inline-flex items-center bg-rose-600 px-4 py-2 rounded-xl text-sm font-medium text-white hover:bg-rose-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    {{ $overview['open_anomalies'] ?? 0 }} Anomalies
                </a>
            </div>
        </div>

        {{-- Stats du jour --}}
        @if(isset($overview))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="text-emerald-600">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path>
                    </svg>
                </div>
                <div class="mt-4">
                    <p class="text-sm font-medium text-slate-600">Arrivées aujourd'hui</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $overview['total_checkins'] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="text-slate-600">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="mt-4">
                    <p class="text-sm font-medium text-slate-600">Départs aujourd'hui</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $overview['total_checkouts'] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="text-amber-600">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="mt-4">
                    <p class="text-sm font-medium text-slate-600">Retards</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $overview['late_arrivals'] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="text-rose-600">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 001-1v-3a1 1 0 00-1-1H9z"></path>
                    </svg>
                </div>
                <div class="mt-4">
                    <p class="text-sm font-medium text-slate-600">Départs anticipés</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $overview['early_departures'] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="text-rose-600">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 001-1v-3a1 1 0 00-1-1H9z"></path>
                    </svg>
                </div>
                <div class="mt-4">
                    <p class="text-sm font-medium text-slate-600">Anomalies ouvertes</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $overview['open_anomalies'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Filtres --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Filtres</h2>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Date début</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Date fin</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Site</label>
                    <select name="site_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les sites</option>
                        @foreach($sites ?? [] as $site)
                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                            {{ $site->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <button type="submit" class="w-full bg-indigo-600 px-4 py-2 rounded-xl text-white font-medium hover:bg-indigo-700">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        {{-- Tableau des présences --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Présences récentes</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Stagiaire/Personnel</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Site</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Arrivée</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Départ</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Heures</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Anomalies</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($days ?? [] as $day)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                {{ $day->attendance_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ $day->stage?->etudiant?->nom ?? 'Personnel' }}
                                </div>
                                <div class="text-sm text-slate-500">
                                    {{ $day->stage?->etudiant?->user?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ $day->site?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ $day->first_check_in_at?->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ $day->last_check_out_at?->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-slate-900">
                                    {{ number_format(($day->worked_minutes ?? 0) / 60, 1) }}h
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($day->day_status === 'late')
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                    Retard ({{ $day->late_minutes }}min)
                                </span>
                                @elseif($day->day_status === 'incomplete')
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-rose-100 text-rose-800">
                                    Départ anticipé
                                </span>
                                @else
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    Présent
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.presence.anomalies') }}" class="text-rose-600 hover:text-rose-900">
                                    {{ $day->anomaly_count ?? 0 }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-slate-900">Aucune présence</h3>
                                <p class="mt-1">Aucune donnée ne correspond aux filtres sélectionnés.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(($days ?? collect())->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $days->links() }}
            </div>
            @endif
        </div>
    </div>

</x-app-layout>