<x-app-layout title="Historique de présence">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 dark:bg-gray-900/50 min-h-screen pb-24">
        {{-- 🎉 SUCCESS BANNER ANIMÉ --}}
        @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)"
            x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
            class="group bg-gradient-to-r from-emerald-500 to-green-600 text-white p-6 rounded-3xl shadow-2xl border-4 border-white/20 backdrop-blur-sm animate-pulse mb-8 dark:border-white/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center animate-bounce">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black drop-shadow-lg animate-pulse">{{ session('success') }}</h2>
                        <p class="text-sm opacity-90 font-medium">Redirection vers votre historique ✨</p>
                    </div>
                </div>
                <button @click="show = false" class="p-2 -m-2 rounded-2xl hover:bg-white/20 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-gray-100 tracking-tight animate-fade-in">
                    <p class="mt-2 text-xl text-slate-600 dark:text-indigo-300 animate-fade-in delay-200">
                        {{ isset($user) && $user->id !== auth()->id() ? 'Pointages de ' . $user->name : 'Tous tes pointages' }} par période
                    </p>
                </h1>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('presence.pointage') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white rounded-xl font-medium shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z">
                    </svg>
                    Nouveau pointage
                </a>
            </div>
        </div>

        {{-- Period Tabs --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-1 shadow-sm">
            <nav class="-mb-px flex space-x-8">
                <a href="?period=week" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'week' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200' }} text-sm transition-colors">
                    {{ request('period') === 'week' ? '📅 Semaine' : 'Semaine' }}
                </a>
                <a href="?period=month" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'month' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200' }} text-sm transition-colors">
                    {{ request('period') === 'month' ? '📊 Mois' : 'Mois' }}
                </a>
                <a href="?period=year" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'year' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200' }} text-sm transition-colors">
                    {{ request('period') === 'year' ? '📈 Année' : 'Année' }}
                </a>
            </nav>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-stats-card title="Jours Pointés" value="{{ $attendanceDays->count() }}" icon="calendar-days" color="slate">
                <x-slot:subtitle>{{ $userStats['present_days'] ?? $attendanceDays->whereNotNull('first_check_in_at')->count() }} présents</x-slot:subtitle>
            </x-stats-card>

            <x-stats-card title="Taux Présence" value="{{ round(($userStats['present_days'] ?? $attendanceDays->whereNotNull('first_check_in_at')->count()) / max(1, $attendanceDays->count()) * 100, 1) }}%" icon="check-circle" color="emerald">
                <x-slot:subtitle>+{{ $userStats['late_days'] ?? $attendanceDays->where('arrival_status', 'late')->count() }} retards</x-slot:subtitle>
            </x-stats-card>

            <x-stats-card title="Heures Totales" value="{{ round($attendanceDays->sum('worked_minutes') / 60, 1) }}h" icon="briefcase" color="blue">
                <x-slot:subtitle>{{ round($attendanceDays->avg('worked_minutes') / 60, 1) }}h/jour moyenne</x-slot:subtitle>
            </x-stats-card>

            <x-stats-card title="Retards Cumulés" value="{{ $attendanceDays->sum('late_minutes') }}min" icon="clock" color="amber">
                <x-slot:subtitle>{{ $attendanceDays->where('late_minutes', '>', 0)->count() }} jours retard</x-slot:subtitle>
            </x-stats-card>
        </div>

        {{-- Graphs --}}
        @if(isset($userStats) && isset($userStats['chart_data']))
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm p-8">
                <h3 class="text-lg font-bold text-slate-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                    📈 Évolution Présence
                </h3>
                <canvas id="personalPresenceChart" height="300"></canvas>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm p-8">
                <h3 class="text-lg font-bold text-slate-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                    ⚠️ Retards Journaliers ({{ $userStats['total_late_minutes'] }}min)
                </h3>
                <canvas id="personalLateChart" height="300"></canvas>
            </div>
        </div>
        @endif

        {{-- Main Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900/50">
                <h3 class="text-lg font-bold text-slate-900 dark:text-gray-100 flex items-center gap-2">
                    Pointages récents
                    <span class="px-2 py-1 bg-slate-200 dark:bg-gray-700 text-slate-800 dark:text-gray-200 text-xs font-semibold rounded-full">
                        {{ $attendanceDays->flatten()->count() }} jours
                    </span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Heure d'arrivée</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Heure de départ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Rapport</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-gray-700">
                        @forelse($attendanceDays->flatten()->sortByDesc('attendance_date') as $index => $day)
                        <tr class="hover:bg-slate-50 dark:hover:bg-gray-800 group transition-all duration-300 animate-fade-in-up 
                                   {{ $index % 2 === 0 ? 'delay-100' : 'delay-200' }}
                                   {{ $day->attendance_date->isToday() ? 'bg-emerald-50 dark:bg-emerald-950/50 border-l-4 border-emerald-400 dark:border-emerald-500 ring-2 ring-emerald-200/50 dark:ring-emerald-900/50' : '' }}"
                            style="animation-fill-mode: both;">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900 dark:text-gray-100">{{ $day->attendance_date->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
                                <div class="text-xs text-slate-500 dark:text-gray-400 capitalize">{{ $day->attendance_date->locale('fr')->isoFormat('dddd') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900 dark:text-gray-100">
                                    {{ $day->first_check_in_at?->format('H:i') ?? '—' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-gray-100">
                                {{ $day->last_check_out_at?->format('H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-gray-300">
                                @if($day->stage && $day->stage->site)
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 text-sm font-semibold rounded-full">
                                    TFG SARL (TECHNOLOGY FOREVER GROUP)
                                </span>
                                @else
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm font-semibold rounded-full">
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
                                            <span class="px-4 py-2 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-{{ $color }}-400 to-{{ $color }}-500 text-white shadow-lg dark:from-{{ $color }}-500 dark:to-{{ $color }}-600">
                                            @if($icon === 'thumbs-up')
                                            👍
                                            @elseif($icon === 'thumbs-down')
                                            👎
                                            @endif
                                            {{ $status }}
                                            </span>
                                            @else
                                            <span class="px-4 py-2 inline-flex text-xs font-bold rounded-full bg-slate-200 dark:bg-gray-700 text-slate-800 dark:text-gray-200">
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
                                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 text-xs font-semibold rounded-full">
                                    Soumis
                                </span>
                                @elseif($report->status === 'draft')
                                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 text-xs font-semibold rounded-full">
                                    Brouillon
                                </span>
                                @elseif($report->status === 'approved')
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 text-xs font-semibold rounded-full">
                                    Approuvé
                                </span>
                                @else
                                <span class="px-3 py-1 bg-slate-100 dark:bg-gray-800 text-slate-800 dark:text-gray-200 text-xs font-semibold rounded-full">
                                    {{ ucfirst($report->status) }}
                                </span>
                                @endif
                                @else
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-xs font-semibold rounded-full">
                                    Aucun
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-gray-400">
                                <svg class="mx-auto h-16 w-16 mb-4 text-slate-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-gray-100 mb-2">Aucun pointage trouvé</h3>
                                <p class="text-slate-500 dark:text-gray-400">Tes premiers pointages apparaîtront ici.</p>
                                <div class="mt-6">
                                    <a href="{{ route('presence.pointage') }}" class="inline-flex items-center px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-semibold shadow-lg transition-all">
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

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if(isset($userStats) && isset($userStats['chart_data']))
                // Chart Présence
                const presenceCtx = document.getElementById('personalPresenceChart')?.getContext('2d');
                if (presenceCtx) {
                    new Chart(presenceCtx, {
                        type: 'line',
                        data: {
                            labels: @json($userStats['chart_data']['labels']),
                            datasets: [{
                                label: 'Heures travaillées',
                                data: @json($userStats['chart_data']['worked_hours']),
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }

                // Chart Retards
                const lateCtx = document.getElementById('personalLateChart')?.getContext('2d');
                if (lateCtx) {
                    new Chart(lateCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($userStats['chart_data']['labels']),
                            datasets: [{
                                label: 'Minutes de retard',
                                data: @json($userStats['chart_data']['late_minutes']),
                                backgroundColor: 'rgb(251, 191, 36)',
                                borderColor: 'rgb(251, 146, 60)',
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }
                @endif
            });
        </script>
        @endpush
</x-app-layout>
