<x-app-layout title="Historique de présence">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Historique de présence</h1>
                <p class="mt-2 text-xl text-slate-600">Tous tes pointages par période</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('presence.pointage') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z">
                    </svg>
                    Nouveau pointage
                </a>
            </div>
        </div>

        {{-- Period Tabs --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-1 shadow-sm">
            <nav class="-mb-px flex space-x-8">
                <a href="?period=week" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'week' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ request('period') === 'week' ? '📅 Semaine' : 'Semaine' }}
                </a>
                <a href="?period=month" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'month' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ request('period') === 'month' ? '📊 Mois' : 'Mois' }}
                </a>
                <a href="?period=year" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'year' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ request('period') === 'year' ? '📈 Année' : 'Année' }}
                </a>
            </nav>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-emerald-100">
                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600">Jours pointés</p>
                        <p class="text-3xl font-bold text-slate-900">{{ $attendanceDays->flatten()->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600">Taux présence</p>
                        <p class="text-3xl font-bold text-slate-900">{{ number_format(($attendanceDays->flatten()->where('day_status', '!=', 'late')->count() / max(1, $attendanceDays->flatten()->count())) * 100, 0) }}%</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-amber-100">
                        <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 001-1v-3a1 1 0 00-1-1H9z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600">Retards</p>
                        <p class="text-3xl font-bold text-slate-900">{{ $attendanceDays->flatten()->where('late_minutes', '>', 0)->sum('late_minutes') }} min</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    Pointages récents
                    <span class="px-2 py-1 bg-slate-200 text-slate-800 text-xs font-semibold rounded-full">
                        {{ $attendanceDays->flatten()->count() }} jours
                    </span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Arrivée</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Départ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Durée</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Anomalies</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($attendanceDays->flatten()->sortByDesc('attendance_date') as $day)
                        <tr class="hover:bg-slate-50 group transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $day->attendance_date->format('d MMM') }}</div>
                                <div class="text-xs text-slate-500">{{ $day->attendance_date->translatedFormat('l') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold">
                                    {{ $day->first_check_in_at?->format('H:i') ?? '—' }}
                                </div>
                                @if($day->late_minutes > 0)
                                <div class="mt-1 px-2 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 001-1v-3a1 1 0 00-1-1H9z"></path>
                                    </svg>
                                    {{ $day->late_minutes }}min retard
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold">
                                {{ $day->last_check_out_at?->format('H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-900">
                                    {{ number_format(($day->worked_minutes ?? 0) / 60, 1) }}h
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($day->arrival_status === 'ontime')
                                <span class="px-4 py-2 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg">
                                    À l'heure
                                </span>
                                @elseif($day->arrival_status === 'warning')
                                <span class="px-4 py-2 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-amber-400 to-amber-500 text-white shadow-lg">
                                    Proche retard
                                </span>
                                @elseif($day->day_status === 'late')
                                <span class="px-4 py-2 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-rose-400 to-rose-500 text-white shadow-lg">
                                    En retard
                                </span>
                                @elseif($day->day_status === 'incomplete')
                                <span class="px-4 py-2 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-rose-400 to-rose-500 text-white shadow-lg">
                                    Départ anticipé
                                </span>
                                @elseif($day->first_check_in_at && $day->last_check_out_at)
                                <span class="px-4 py-2 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg">
                                    Journée complète
                                </span>
                                @else
                                <span class="px-4 py-2 inline-flex text-xs font-bold rounded-full bg-slate-200 text-slate-800">
                                    Partiel
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($day->anomalies->count() > 0)
                                <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full shadow-sm">
                                    {{ $day->anomalies->count() }}
                                </span>
                                @else
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full shadow-sm">
                                    Parfait
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-16 w-16 mb-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">Aucun pointage trouvé</h3>
                                <p class="text-slate-500">Tes premiers pointages apparaîtront ici.</p>
                                <div class="mt-6">
                                    <a href="{{ route('presence.pointage') }}" class="inline-flex items-center px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-lg transition-all">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Faire mon premier pointage
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>