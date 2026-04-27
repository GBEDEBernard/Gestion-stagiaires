<x-app-layout title="Historique de présence">
    @php
        $flatAttendanceDays = $attendanceDays->flatten()->sortByDesc('attendance_date');
        $presentDays = $userStats['present_days'] ?? $flatAttendanceDays->whereNotNull('first_check_in_at')->count();
        $totalDays = max(1, $attendanceDays->count());
        $lateDays = $userStats['late_days'] ?? $flatAttendanceDays->where('late_minutes', '>', 0)->count();
        $workedHours = round($flatAttendanceDays->sum('worked_minutes') / 60, 1);
        $averageHours = round($flatAttendanceDays->avg('worked_minutes') / 60, 1);
        $lateMinutes = $flatAttendanceDays->sum('late_minutes');
        $pageLabel = isset($user) && $user->id !== auth()->id()
            ? 'Pointages de ' . $user->name
            : 'Tous tes pointages';
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition
                class="bg-emerald-500 text-white p-5 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-semibold">{{ session('success') }}</p>
                            <p class="text-sm text-white/80">Historique mis a jour</p>
                        </div>
                    </div>
                    <button @click="show = false" class="p-2 rounded-xl hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-gray-100 tracking-tight">Historique de présence</h1>
                <p class="mt-2 text-slate-600 dark:text-slate-300">{{ $pageLabel }} par période</p>
            </div>
            <a href="{{ route('presence.pointage') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-sm transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Nouveau pointage
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 p-1 shadow-sm">
            <nav class="flex flex-wrap gap-1">
                @foreach(['week' => 'Semaine', 'month' => 'Mois', 'year' => 'Année'] as $periodKey => $label)
                    <a
                        href="?period={{ $periodKey }}"
                        class="px-4 py-3 rounded-xl text-sm transition-colors {{ request('period') === $periodKey ? 'bg-emerald-500 text-white font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500 dark:text-gray-400">Jours pointés</p>
                <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ $attendanceDays->count() }}</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $presentDays }} présents</p>
            </div>

            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500 dark:text-gray-400">Taux de présence</p>
                <p class="mt-3 text-3xl font-bold text-emerald-600">{{ round(($presentDays / $totalDays) * 100, 1) }}%</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $lateDays }} retard(s)</p>
            </div>

            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500 dark:text-gray-400">Heures totales</p>
                <p class="mt-3 text-3xl font-bold text-blue-600">{{ $workedHours }}h</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $averageHours }}h/jour en moyenne</p>
            </div>

            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500 dark:text-gray-400">Retards cumulés</p>
                <p class="mt-3 text-3xl font-bold text-amber-600">{{ $lateMinutes }} min</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $flatAttendanceDays->where('late_minutes', '>', 0)->count() }} jours concernés</p>
            </div>
        </div>

        @if(isset($userStats['chart_data']))
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-gray-100 mb-6">Évolution présence</h3>
                    <div class="h-[18rem]">
                        <canvas id="personalPresenceChart"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-gray-100 mb-6">Retards journaliers</h3>
                    <div class="h-[18rem]">
                        <canvas id="personalLateChart"></canvas>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900/50">
                <h3 class="text-lg font-bold text-slate-900 dark:text-gray-100">Pointages récents</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Arrivée</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Départ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Rapport</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-gray-700">
                        @forelse($flatAttendanceDays as $day)
                            @php
                                $arrivalTime = $day->first_check_in_at;
                                $status = 'Inconnu';
                                $statusClasses = 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-gray-200';

                                if ($arrivalTime) {
                                    $timeInMinutes = ($arrivalTime->hour * 60) + $arrivalTime->minute;

                                    if ($timeInMinutes >= 7 * 60 && $timeInMinutes <= (7 * 60) + 45) {
                                        $status = "À l'heure";
                                        $statusClasses = 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300';
                                    } elseif ($timeInMinutes <= (7 * 60) + 59) {
                                        $status = 'Tard vers retard';
                                        $statusClasses = 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300';
                                    } elseif ($timeInMinutes <= 13 * 60) {
                                        $status = 'En retard';
                                        $statusClasses = 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300';
                                    }
                                }

                                $report = $day->dailyReports->first();
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-gray-800 transition-colors {{ $day->attendance_date->isToday() ? 'bg-emerald-50/60 dark:bg-emerald-950/30' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900 dark:text-gray-100">{{ $day->attendance_date->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
                                    <div class="text-xs text-slate-500 dark:text-gray-400 capitalize">{{ $day->attendance_date->locale('fr')->isoFormat('dddd') }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-gray-100">
                                    {{ $day->first_check_in_at?->format('H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-gray-100">
                                    {{ $day->last_check_out_at?->format('H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700 dark:text-gray-300">
                                    @if($day->stage && $day->stage->site)
                                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 text-sm font-semibold rounded-full">
                                            {{ $day->stage->site->name ?? 'Site' }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm font-semibold rounded-full">
                                            À distance
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusClasses }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($report)
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                            {{ $report->status === 'submitted' ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300' : '' }}
                                            {{ $report->status === 'draft' ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300' : '' }}
                                            {{ $report->status === 'approved' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300' : '' }}
                                            {{ !in_array($report->status, ['submitted', 'draft', 'approved']) ? 'bg-slate-100 dark:bg-gray-800 text-slate-800 dark:text-gray-200' : '' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
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
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if(isset($userStats['chart_data']))
                    const labels = @json($userStats['chart_data']['labels']);
                    const workedHours = @json($userStats['chart_data']['worked_hours']);
                    const lateMinutes = @json($userStats['chart_data']['late_minutes']);

                    let presenceChart;
                    let lateChart;

                    const renderCharts = () => {
                        const theme = window.getAppChartTheme();

                        if (presenceChart) {
                            presenceChart.destroy();
                        }

                        if (lateChart) {
                            lateChart.destroy();
                        }

                        const presenceCtx = document.getElementById('personalPresenceChart')?.getContext('2d');
                        if (presenceCtx) {
                            presenceChart = new Chart(presenceCtx, {
                                type: 'line',
                                data: {
                                    labels,
                                    datasets: [{
                                        label: 'Heures travaillées',
                                        data: workedHours,
                                        borderColor: '#3b82f6',
                                        backgroundColor: 'rgba(59, 130, 246, 0.12)',
                                        tension: 0.4,
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            labels: {
                                                color: theme.tickColor,
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { color: theme.gridColor },
                                            ticks: { color: theme.tickColor }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: theme.gridColor },
                                            ticks: { color: theme.tickColor }
                                        }
                                    }
                                }
                            });
                        }

                        const lateCtx = document.getElementById('personalLateChart')?.getContext('2d');
                        if (lateCtx) {
                            lateChart = new Chart(lateCtx, {
                                type: 'bar',
                                data: {
                                    labels,
                                    datasets: [{
                                        label: 'Minutes de retard',
                                        data: lateMinutes,
                                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                                        borderColor: '#f59e0b',
                                        borderRadius: 6,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            labels: {
                                                color: theme.tickColor,
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { color: theme.gridColor },
                                            ticks: { color: theme.tickColor }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: theme.gridColor },
                                            ticks: { color: theme.tickColor }
                                        }
                                    }
                                }
                            });
                        }
                    };

                    renderCharts();
                    document.addEventListener('theme:changed', renderCharts);
                @endif
            });
        </script>
    @endpush
</x-app-layout>
