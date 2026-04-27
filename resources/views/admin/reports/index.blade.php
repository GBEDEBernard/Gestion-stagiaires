<x-app-layout title="Suivi des rapports de travail">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 text-slate-900 dark:text-slate-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Rapports de travail</h1>
                <p class="mt-2 text-lg text-slate-600 dark:text-slate-300">Suivi propre des rapports étudiants et employés.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Total</p>
                <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ $summary['total'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Soumis</p>
                <p class="mt-3 text-3xl font-bold text-emerald-600">{{ $summary['submitted'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Brouillons / à revoir</p>
                <p class="mt-3 text-3xl font-bold text-amber-600">{{ $summary['draft'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="?period=daily" class="px-4 py-2 rounded-xl {{ $period === 'daily' ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700' }} transition-all">Jour</a>
                    <a href="?period=weekly" class="px-4 py-2 rounded-xl {{ $period === 'weekly' ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700' }} transition-all">Semaine</a>
                    <a href="?period=monthly" class="px-4 py-2 rounded-xl {{ $period === 'monthly' ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700' }} transition-all">Mois</a>
                </div>

                <div class="flex items-center gap-3">
                    <input type="date" id="reportDate" name="date" value="{{ $filterDate->format('Y-m-d') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" />
                    <button id="applyFilter" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-all">Filtrer</button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Étudiants</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Rapports liés aux stagiaires.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Stage</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Heures</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($studentReports as $report)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $report->etudiant?->user?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ $report->stage?->theme ?? 'Non défini' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $report->status === 'submitted' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ $report->report_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ $report->hours_declared ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">Aucun rapport étudiant trouvé pour cette période.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Employés</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Rapports des employés présents.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Service / Stage</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Heures</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($employeeReports as $report)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $report->user?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ $report->stage?->theme ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $report->status === 'submitted' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ $report->report_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ $report->hours_declared ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">Aucun rapport employé trouvé pour cette période.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('applyFilter')?.addEventListener('click', function() {
                const date = document.getElementById('reportDate').value;
                const period = new URLSearchParams(window.location.search).get('period') || 'daily';
                window.location.href = `?period=${period}&date=${date}`;
            });
        </script>
    @endpush
</x-app-layout>
