<x-app-layout title="Antécédents {{ $user->name }}">
<x-slot name="header">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">{{ $user->name }}</h1>
            <p class="mt-1 text-xl text-slate-600">
                @if($userStats['is_etudiant'])
                    {{ $user->etudiant->nom }} {{ $user->etudiant->prenom }} - Stagiaire
                @else
                    Employé - {{ $user->domaine?->nom ?? 'Non assigné' }}
                @endif
            </p>
        </div>
    </div>
</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    {{-- Période tabs --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-1">
        <nav class="-mb-px flex space-x-8">
            @foreach(['month' => 'Mois', 'week' => 'Semaine', 'year' => 'Année'] as $periodKey => $label)
                <a href="?period={{ $periodKey }}" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === $periodKey ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Cards utilisateur --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stats-card title="Jours Pointés" value="{{ $userStats['total_days'] }}" icon="calendar" color="slate">
            <x-slot:subtitle>{{ $userStats['present_days'] }} présents</x-slot:subtitle>
        </x-stats-card>

        <x-stats-card title="Taux Présence" value="{{ round(($userStats['present_days'] / max(1, $userStats['total_days'])) * 100, 1) }}%" icon="check-circle" color="emerald">
            <x-slot:subtitle>+{{ $userStats['late_days'] ?? 0 }} retards</x-slot:subtitle>
        </x-stats-card>

        <x-stats-card title="Heures Totales" value="{{ $userStats['total_worked_hours'] }}h" icon="clock" color="blue">
            <x-slot:subtitle>Moy. {{ $userStats['avg_daily_hours'] }}h/jour</x-slot:subtitle>
        </x-stats-card>

        <x-stats-card title="Retards Cumulés" value="{{ $userStats['total_late_minutes'] }}min" icon="exclamation" color="amber">
            <x-slot:subtitle>{{ $userStats['open_anomalies'] }} anomalies ouvertes</x-slot:subtitle>
        </x-stats-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Graph heures travaillées --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
            <h3 class="text-lg font-bold text-slate-900 mb-6">⏱️ Heures Travaillées</h3>
            <canvas id="workedHoursChart" height="300"></canvas>
        </div>

        {{-- Graph retards --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
            <h3 class="text-lg font-bold text-slate-900 mb-6">⚠️ Retards Journaliers</h3>
            <canvas id="lateChart" height="300"></canvas>
        </div>
    </div>

    {{-- Anomalies récentes --}}
    @if($anomalies->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                    🚨 {{ $anomalies->count() }} Anomalie{{ $anomalies->count() > 1 ? 's' : '' }} Ouverte{{ $anomalies->count() > 1 ? 's' : '' }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Sévérité</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($anomalies as $anomaly)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900">
                                    {{ $anomaly->detected_at->format('d/M H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-800 text-xs font-semibold rounded-full">
                                        {{ ucfirst(str_replace('_', ' ', $anomaly->anomaly_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php $severityColor = ['low' => 'green', 'medium' => 'amber', 'high' => 'rose'][$anomaly->severity] @endphp
                                    <span class="px-2 py-1 bg-{{ $severityColor }}-100 text-{{ $severityColor }}-800 text-xs font-bold rounded-full">
                                        {{ ucfirst($anomaly->severity) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="#" class="text-emerald-600 hover:text-emerald-700 font-semibold">Résoudre</a>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Heures chart
    const workedCtx = document.getElementById('workedHoursChart')?.getContext('2d');
    if (workedCtx) {
        new Chart(workedCtx, {
            type: 'line',
            data: {
                labels: @json($userStats['chart_data']['labels']),
                datasets: [{
                    label: 'Heures',
                    data: @json($userStats['chart_data']['worked_hours']),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    // Retards chart
    const lateCtx = document.getElementById('lateChart')?.getContext('2d');
    if (lateCtx) {
        new Chart(lateCtx, {
            type: 'bar',
            data: {
                labels: @json($userStats['chart_data']['labels']),
                datasets: [{ 
                    label: 'Min Retard',
                    data: @json($userStats['chart_data']['late_minutes']),
                    backgroundColor: 'rgb(251, 191, 36)'
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }
});
</script>
@endpush>

