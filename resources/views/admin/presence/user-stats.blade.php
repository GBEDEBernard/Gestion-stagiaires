<x-app-layout title="Présence {{ $user->name }}">
    @php
        $presenceRate = round(($userStats['present_days'] / max(1, $userStats['total_days'])) * 100, 1);
        $profileLabel = $userStats['is_etudiant']
            ? trim(($user->etudiant?->nom ?? '') . ' ' . ($user->etudiant?->prenom ?? '')) . ' - Stagiaire'
            : 'Employé - ' . ($user->domaine?->nom ?? 'Non assigné');
        $severityClasses = [
            'low' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
            'medium' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
            'high' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
        ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $user->name }}</h1>
                <p class="mt-2 text-slate-600 dark:text-slate-300">{{ $profileLabel }}</p>
            </div>

            <a href="{{ route('admin.presence.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7-7l-7 7 7 7" />
                </svg>
                Retour
            </a>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-1">
            <nav class="flex flex-wrap gap-1">
                @foreach(['week' => 'Semaine', 'month' => 'Mois', 'year' => 'Année'] as $periodKey => $label)
                    <a
                        href="{{ route('admin.presence.user-stats', ['user' => $user->id, 'period' => $periodKey]) }}"
                        class="px-4 py-3 rounded-xl text-sm transition-colors {{ $period === $periodKey ? 'bg-emerald-500 text-white font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500 dark:text-slate-400">Jours pointés</p>
                <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ $userStats['total_days'] }}</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $userStats['present_days'] }} présents</p>
            </div>

            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500 dark:text-slate-400">Taux de présence</p>
                <p class="mt-3 text-3xl font-bold text-emerald-600">{{ $presenceRate }}%</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $userStats['late_days'] ?? 0 }} retards</p>
            </div>

            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500 dark:text-slate-400">Heures totales</p>
                <p class="mt-3 text-3xl font-bold text-blue-600">{{ $userStats['total_worked_hours'] }}h</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Moy. {{ $userStats['avg_daily_hours'] }}h / jour</p>
            </div>

            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500 dark:text-slate-400">Retards cumulés</p>
                <p class="mt-3 text-3xl font-bold text-amber-600">{{ $userStats['total_late_minutes'] }} min</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $userStats['open_anomalies'] }} anomalies ouvertes</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-6">Heures travaillées</h3>
                <div class="h-[18rem]">
                    <canvas id="workedHoursChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-6">Retards journaliers</h3>
                <div class="h-[18rem]">
                    <canvas id="lateChart"></canvas>
                </div>
            </div>
        </div>

        @if($anomalies->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-slate-100">
                        {{ $anomalies->count() }} anomalie(s) ouverte(s)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Sévérité</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($anomalies as $anomaly)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40">
                                    <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                                        {{ $anomaly->detected_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold rounded-full">
                                            {{ ucfirst(str_replace('_', ' ', $anomaly->anomaly_type)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $severityClasses[$anomaly->severity] ?? $severityClasses['low'] }}">
                                            {{ ucfirst($anomaly->severity) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('admin.presence.anomalies') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold">
                                            Résoudre
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const labels = @json($userStats['chart_data']['labels'] ?? []);
                const workedHours = @json($userStats['chart_data']['worked_hours'] ?? []);
                const lateMinutes = @json($userStats['chart_data']['late_minutes'] ?? []);

                let workedChart;
                let lateChart;

                const renderCharts = () => {
                    const theme = window.getAppChartTheme();

                    if (workedChart) {
                        workedChart.destroy();
                    }

                    if (lateChart) {
                        lateChart.destroy();
                    }

                    const workedCtx = document.getElementById('workedHoursChart')?.getContext('2d');
                    if (workedCtx) {
                        workedChart = new Chart(workedCtx, {
                            type: 'line',
                            data: {
                                labels,
                                datasets: [{
                                    label: 'Heures',
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
                                        labels: { color: theme.tickColor }
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

                    const lateCtx = document.getElementById('lateChart')?.getContext('2d');
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
                                    borderRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        labels: { color: theme.tickColor }
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
            });
        </script>
    @endpush
</x-app-layout>
