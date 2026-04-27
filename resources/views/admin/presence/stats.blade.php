<x-app-layout title="Statistiques de presence">
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-600">Presence admin</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Statistiques consolidees</h1>
                <p class="mt-1 text-slate-500">Vue synthetique pour la periode selectionnee.</p>
            </div>

            <a href="{{ route('admin.presence.index', ['period' => $period, 'group' => $group]) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Retour a la supervision
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Taux de presence</p>
                <p class="mt-3 text-3xl font-bold text-emerald-600">{{ $globalStats['taux_presence'] ?? 0 }}%</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Jours presents</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $globalStats['present_days'] ?? 0 }}</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Heures cumulees</p>
                <p class="mt-3 text-3xl font-bold text-blue-600">{{ $globalStats['total_worked_hours'] ?? 0 }}h</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Anomalies ouvertes</p>
                <p class="mt-3 text-3xl font-bold text-amber-600">{{ $globalStats['total_anomalies'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Presence par jour</h2>
                <div class="mt-4 h-80">
                    <canvas id="presenceChart"></canvas>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Retards par jour</h2>
                <div class="mt-4 h-80">
                    <canvas id="lateChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">Top retards</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Retard total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Jours</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($topLate as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $item->user_name ?? $item->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ $item->total_late ?? 0 }} min</td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ $item->days_count ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">Aucun retard sur cette periode.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">Absences detectees</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Jours</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($absences as $userName => $count)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $userName }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ $count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-8 text-center text-sm text-slate-500">Aucune absence detectee sur cette periode.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const labels = @json($globalStats['chart_data']['labels'] ?? []);
                const present = @json($globalStats['chart_data']['present'] ?? []);
                const lateMinutes = @json($globalStats['chart_data']['late_minutes'] ?? []);

                const presenceCanvas = document.getElementById('presenceChart');
                if (presenceCanvas) {
                    new Chart(presenceCanvas, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Presents',
                                data: present,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                                fill: true,
                                tension: 0.35,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                },
                            },
                        },
                    });
                }

                const lateCanvas = document.getElementById('lateChart');
                if (lateCanvas) {
                    new Chart(lateCanvas, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Retards (min)',
                                data: lateMinutes,
                                backgroundColor: '#f59e0b',
                                borderRadius: 8,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                },
                            },
                        },
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
