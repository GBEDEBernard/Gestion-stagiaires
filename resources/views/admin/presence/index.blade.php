<x-app-layout title="Statistiques de Présence - Admin">

    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">📊 Statistiques Globales</h1>
                <p class="mt-1 text-xl text-slate-600">Présence étudiants & employés en temps réel</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.presence.export') }}" 
                   class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:bg-emerald-700 transition-all">
                    📥 Exporter CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- Période tabs --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-1">
            <nav class="-mb-px flex space-x-8">
                @foreach(['today' => 'Aujourd\'hui', 'week' => 'Semaine', 'month' => 'Mois', 'year' => 'Année'] as $periodKey => $label)
                    <a href="?period={{ $periodKey }}"
                       class="group inline-flex items-center px-4 py-3 border-b-2 
                              {{ request('period') === $periodKey 
                                  ? 'border-emerald-500 text-emerald-600 font-bold' 
                                  : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} 
                              text-sm transition-all duration-200">
                        {{ $label }}
                        @if(request('period') === $periodKey)
                            <svg class="ml-1 w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Cards Globales --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-stats-card
                title="Taux de Présence"
                value="{{ $globalStats['taux_presence'] ?? 0 }}%"
                change="+{{ number_format(($globalStats['present_days'] ?? 0) / max(1, $globalStats['total_days'] ?? 1) * 100, 1) }}%"
                icon="calendar-days"
                color="emerald">
                <x-slot:subtitle>{{ $globalStats['present_days'] ?? 0 }} / {{ $globalStats['total_days'] ?? 0 }} jours</x-slot:subtitle>
            </x-stats-card>

            <x-stats-card
                title="Retards Cumulés"
                value="{{ ($globalStats['total_late_minutes'] ?? 0) / 60 }}h"
                change="{{ $globalStats['total_late_days'] ?? 0 }} jours"
                icon="clock"
                color="amber">
                <x-slot:subtitle>{{ number_format($globalStats['total_late_minutes'] ?? 0) }} min total</x-slot:subtitle>
            </x-stats-card>

            <x-stats-card
                title="Heures Travaillées"
                value="{{ $globalStats['total_worked_hours'] ?? 0 }}h"
                change="+{{ number_format(($globalStats['total_worked_hours'] ?? 0), 1) }}h"
                icon="briefcase"
                color="blue">
                <x-slot:subtitle>{{ number_format($globalStats['total_days'] ?? 0) }} jours pointés</x-slot:subtitle>
            </x-stats-card>

            <x-stats-card
                title="Anomalies Ouvertes"
                value="{{ $globalStats['total_anomalies'] ?? 0 }}"
                change="‑{{ \App\Models\AttendanceAnomaly::where('status', 'resolved')->whereDate('reviewed_at', today())->count() }}"
                icon="exclamation-triangle"
                color="rose">
                <x-slot:subtitle>À reviewer</x-slot:subtitle>
            </x-stats-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Graph Présence --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                    📈 Évolution Présence
                </h3>
                <canvas id="presenceChart" height="300"></canvas>
            </div>

            {{-- Groupes Étudiants/Employés --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
                    <h4 class="font-bold text-lg mb-4">👥 Étudiants</h4>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-2xl font-bold text-emerald-600">
                            {{ $groupStats['etudiants']['present'] ?? 0 }}/{{ $groupStats['etudiants']['count'] ?? 0 }}
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-slate-500">Moy. {{ $groupStats['etudiants']['avg_worked_hours'] ?? 0 }}h/jour</span>
                        </div>
                    </div>
                    <div class="h-2 bg-slate-200 rounded-full">
                        <div class="h-2 bg-emerald-500 rounded-full" 
                             style="width: {{ ($groupStats['etudiants']['present'] ?? 0) / max(1, $groupStats['etudiants']['count'] ?? 1) * 100 }}%"></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
                    <h4 class="font-bold text-lg mb-4">👔 Employés</h4>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-2xl font-bold text-emerald-600">
                            {{ $groupStats['employes']['present'] ?? 0 }}/{{ $groupStats['employes']['count'] ?? 0 }}
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-slate-500">Moy. {{ $groupStats['employes']['avg_worked_hours'] ?? 0 }}h/jour</span>
                        </div>
                    </div>
                    <div class="h-2 bg-slate-200 rounded-full">
                        <div class="h-2 bg-emerald-500 rounded-full" 
                             style="width: {{ ($groupStats['employes']['present'] ?? 0) / max(1, $groupStats['employes']['count'] ?? 1) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Retards & Absences --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top 10 Retards --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                        🚨 Top 10 Retards ({{ $period ?? 'mois' }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Min retard</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Jours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topLate ?? [] as $user)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-amber-600">{{ $user->total_late }} min</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $user->days_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                        Aucun retard détecté
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Absences --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                        ⭕ Absences ({{ $period ?? 'mois' }})
                    </h3>
                </div>
                <div class="p-6">
                    @if(empty($absences))
                        <p class="text-slate-500 text-center py-12">Aucune absence détectée</p>
                    @else
                        <div class="space-y-3">
                            @foreach($absences as $userName => $count)
                                <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                                    <span class="text-slate-900 font-medium">{{ $userName }}</span>
                                    <span class="bg-rose-100 text-rose-800 px-3 py-1 rounded-full text-sm font-bold">{{ $count }} jours</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Script Chart.js --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('presenceChart').getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($globalStats['chart_data']['labels'] ?? []),
                        datasets: [{
                            label: 'Présents',
                            data: @json($globalStats['chart_data']['present'] ?? []),
                            backgroundColor: 'rgb(34, 197, 94)',
                        }, {
                            label: 'Retards',
                            data: @json($globalStats['chart_data']['late'] ?? []),
                            backgroundColor: 'rgb(251, 191, 36)',
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Présence vs Retards'
                            }
                        }
                    }
                });
            });
        </script>
    @endpush

</x-app-layout>