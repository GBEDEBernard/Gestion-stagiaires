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
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Heure d'arrivée</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Heure de départ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Rapport</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($attendanceDays->flatten()->sortByDesc('attendance_date') as $day)
                        <tr class="hover:bg-slate-50 group transition-colors {{ $day->attendance_date->isToday() ? 'bg-emerald-50 border-l-4 border-emerald-400' : '' }}">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $day->attendance_date->format('d MMM Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $day->attendance_date->translatedFormat('l') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold">
                                    {{ $day->first_check_in_at?->format('H:i') ?? '—' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold">
                                {{ $day->last_check_out_at?->format('H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                @if($day->stage && $day->stage->site)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                    TFG SARL (TECHNOLOGY FOREVER GROUP)
                                </span>
                                @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-semibold rounded-full">
                                    À distance
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                $arrivalTime = $day->first_check_in_at;
                                $status = 'inconnu';
                                $color = 'gray';
                                $icon = 'question';
                                if ($arrivalTime) {
                                $hour = $arrivalTime->hour;
                                $minute = $arrivalTime->minute;
                                $timeInMinutes = $hour * 60 + $minute;
                                if ($timeInMinutes >= 7*60 && $timeInMinutes <= 7*60 + 45) {
                                    $status='À l\' heure';
                                    $color='emerald' ;
                                    $icon='thumbs-up' ;
                                    } elseif ($timeInMinutes>= 7*60 + 46 && $timeInMinutes <= 7*60 + 59) {
                                        $status='Tard vers retard' ;
                                        $color='amber' ;
                                        $icon='thumbs-up' ;
                                        } elseif ($timeInMinutes>= 8*60 && $timeInMinutes <= 13*60) {
                                            $status='En retard' ;
                                            $color='rose' ;
                                            $icon='thumbs-down' ;
                                            }
                                            }
                                            @endphp
                                            @if($status !=='inconnu' )
                                            <span class="px-4 py-2 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-{{ $color }}-400 to-{{ $color }}-500 text-white shadow-lg">
                                            @if($icon === 'thumbs-up')
                                            👍
                                            @elseif($icon === 'thumbs-down')
                                            👎
                                            @endif
                                            {{ $status }}
                                            </span>
                                            @else
                                            <span class="px-4 py-2 inline-flex text-xs font-bold rounded-full bg-slate-200 text-slate-800">
                                                Inconnu
                                            </span>
                                            @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                $report = $day->dailyReports->first();
                                @endphp
                                @if($report)
                                    @if($report->status === 'submitted')
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full">
                                            Soumis
                                        </span>
                                    @elseif($report->status === 'draft')
                                        <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">
                                            Brouillon
                                        </span>
                                    @elseif($report->status === 'approved')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                            Approuvé
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-slate-100 text-slate-800 text-xs font-semibold rounded-full">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                                        Aucun
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