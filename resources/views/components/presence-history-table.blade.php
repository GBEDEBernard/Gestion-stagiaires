<div class="mt-8">
    <div class="flex border-b border-slate-200">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('presence.historique', ['period' => 'week']) }}"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $period === 'week' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-600' }}">
                Semaine
            </a>
            <a href="{{ route('presence.historique', ['period' => 'month']) }}"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $period === 'month' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-600' }}">
                Mois
            </a>
            <a href="{{ route('presence.historique', ['period' => 'year']) }}"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $period === 'year' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-600' }}">
                Année
            </a>
        </nav>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-semibold text-slate-900">Historique de présence</h2>
            <p class="text-sm text-slate-500 mt-1">{{ $attendanceDays->flatten()->count() }} jours pointés</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Arrivée</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Départ</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Heures</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Anomalies</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($attendanceDays->flatten()->sortByDesc('attendance_date') as $day)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-slate-900">{{ $day->attendance_date->format('d M Y') }}</div>
                            <div class="text-xs text-slate-500">{{ $day->attendance_date->format('l') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            {{ $day->first_check_in_at?->format('H:i') ?? '-' }}
                            @if($day->late_minutes > 0)
                            <span class="ml-1 px-2 py-0.5 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">
                                +{{ $day->late_minutes }}min
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            {{ $day->last_check_out_at?->format('H:i') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-slate-900">
                                {{ number_format(($day->worked_minutes ?? 0) / 60, 1) }}h
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($day->arrival_status === 'ontime')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                À l'heure
                            </span>
                            @elseif($day->arrival_status === 'warning')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                Proche retard
                            </span>
                            @elseif($day->day_status === 'late')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                En retard
                            </span>
                            @elseif($day->day_status === 'incomplete')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                Départ anticipé
                            </span>
                            @elseif($day->first_check_in_at && $day->last_check_out_at)
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Complet
                            </span>
                            @else
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-slate-100 text-slate-800 border border-slate-200">
                                Partiel
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($day->anomalies->count() > 0)
                            <span class="px-2 py-1 bg-rose-100 text-rose-800 text-xs font-semibold rounded-full">
                                {{ $day->anomalies->count() }}
                            </span>
                            @else
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full">
                                OK
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-slate-900">Aucun pointage</h3>
                            <p class="mt-1 text-slate-500">Tes pointages apparaîtront ici dès que tu commenceras.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>