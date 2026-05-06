<x-app-layout title="Suivi des Rapports de travail">
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
                    <a href="?period=daily" class="px-4 py-2 rounded-xl {{ $period === 'daily' ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">📅 Jour</a>
                    <a href="?period=weekly" class="px-4 py-2 rounded-xl {{ $period === 'weekly' ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">📊 Semaine</a>
                    <a href="?period=monthly" class="px-4 py-2 rounded-xl {{ $period === 'monthly' ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">📈 Mois</a>
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
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($studentReports as $report)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $report->etudiant?->user?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ $report->stage?->theme ?? 'Non défini' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $report->status === 'submitted' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ $report->report_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ $report->hours_declared ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <button onclick="viewReportDetails({{ $report->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Détails
                                        </button>
                                        <button onclick="openReportModal({{ $report->id }}, '{{ $report->etudiant?->user?->name ?? 'N/A' }}', '{{ $report->summary }}', '{{ $report->report_date->format('d/m/Y') }}', '{{ $report->hours_declared ?? 0 }}', '{{ $report->status }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"></path>
                                            </svg>
                                            Répondre
                                        </button>
                                    </div>
                                </td>
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
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($employeeReports as $report)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $report->user?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ $report->stage?->theme ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $report->status === 'submitted' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ $report->report_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ $report->hours_declared ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <button onclick="viewReportDetails({{ $report->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Détails
                                        </button>
                                        <button onclick="openReportModal({{ $report->id }}, '{{ $report->user?->name ?? 'N/A' }}', '{{ $report->summary }}', '{{ $report->report_date->format('d/m/Y') }}', '{{ $report->hours_declared ?? 0 }}', '{{ $report->status }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"></path>
                                            </svg>
                                            Répondre
                                        </button>
                                    </div>
                                </td>
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

    <!-- MODAL DÉTAILS DU RAPPORT (lecture complète) -->
    <div id="reportDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Détails du rapport</h3>
                        <button onclick="closeReportDetailsModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="reportDetailsContent"><!-- chargé dynamiquement --></div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL POUR VOIR ET RÉPONDRE AU RAPPORT -->
    <div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Détails du Rapport</h3>
                        <button onclick="closeReportModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div id="reportContent">
                        <!-- Le contenu sera chargé dynamiquement -->
                    </div>

                    <div class="mt-6">
                        <form id="responseForm" method="POST">
                            @csrf
                            <input type="hidden" name="report_id" id="responseReportId">
                            <div class="space-y-4">
                                <div>
                                    <label for="feedback" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Votre réponse</label>
                                    <textarea name="feedback" id="feedback" rows="4" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Écrivez votre retour sur ce rapport..."></textarea>
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" onclick="closeReportModal()" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                        Annuler
                                    </button>
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                        Envoyer la réponse
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentReportId = null;

        // ─── MODAL DÉTAILS ───────────────────────────────────────────────────────
        function viewReportDetails(reportId) {
            fetch(`/admin/reports/${reportId}`)
                .then(r => r.json())
                .then(data => {
                    const report  = data.report;
                    const reviews = data.reviews || [];

                    let statusBadge = '';
                    if (report.status === 'submitted') {
                        statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>Soumis</span>';
                    } else if (report.status === 'reviewed') {
                        statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Révisé</span>';
                    } else {
                        statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>Brouillon</span>';
                    }

                    let reviewsHtml = '';
                    if (reviews.length > 0) {
                        reviewsHtml = '<div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700"><h4 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Reviews et commentaires</h4>';
                        reviews.forEach(review => {
                            reviewsHtml += `
                                <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 mb-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-slate-900 dark:text-slate-100">${review.reviewer_name}</span>
                                        <span class="text-sm text-slate-500 dark:text-slate-400">${review.created_at}</span>
                                    </div>
                                    <p class="text-slate-700 dark:text-slate-300">${review.comment}</p>
                                </div>
                            `;
                        });
                        reviewsHtml += '</div>';
                    }

                    const stageInfo = report.stage_theme
                        ? `<p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Stage : ${report.stage_theme}</p>` : '';

                    const content = `
                        <div class="space-y-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-xl font-bold text-slate-900 dark:text-slate-100">${report.author_name}</h4>
                                    <p class="text-lg font-semibold text-slate-700 dark:text-slate-300 mt-1">${report.report_date_formatted}</p>
                                    ${stageInfo}
                                    <div class="mt-2">${statusBadge}</div>
                                </div>
                                <div class="text-right text-sm text-slate-500 dark:text-slate-400 space-y-1">
                                    <div>Créé ${report.created_at_formatted}</div>
                                    <div>Modifié ${report.updated_at_formatted}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
                                    <h5 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Résumé du travail</h5>
                                    <p class="text-slate-900 dark:text-slate-100 whitespace-pre-line">${report.summary}</p>
                                </div>

                                <div class="space-y-4">
                                    ${report.hours_declared ? `
                                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl p-4">
                                        <div class="flex items-center gap-2 text-blue-700 dark:text-blue-400 mb-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M6 6h12"></path></svg>
                                            <span class="font-medium">Heures travaillées</span>
                                        </div>
                                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-300">${report.hours_declared}h</p>
                                    </div>` : ''}

                                    ${report.blockers ? `
                                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4">
                                        <h5 class="text-sm font-semibold text-red-700 dark:text-red-400 mb-2">Blocages rencontrés</h5>
                                        <p class="text-red-900 dark:text-red-200 whitespace-pre-line">${report.blockers}</p>
                                    </div>` : ''}

                                    ${report.next_steps ? `
                                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4">
                                        <h5 class="text-sm font-semibold text-emerald-700 dark:text-emerald-400 mb-2">Prochaines étapes</h5>
                                        <p class="text-emerald-900 dark:text-emerald-200 whitespace-pre-line">${report.next_steps}</p>
                                    </div>` : ''}
                                </div>
                            </div>

                            ${reviewsHtml}
                        </div>
                    `;

                    document.getElementById('reportDetailsContent').innerHTML = content;
                    document.getElementById('reportDetailsModal').classList.remove('hidden');
                })
                .catch(() => alert('Erreur lors du chargement du rapport'));
        }

        function closeReportDetailsModal() {
            document.getElementById('reportDetailsModal').classList.add('hidden');
        }

        // ─── MODAL RÉPONDRE ──────────────────────────────────────────────────────
        function openReportModal(reportId, userName, summary, date, hours, status) {
            currentReportId = reportId;
            document.getElementById('responseReportId').value = reportId;

            // Charger les détails du rapport via AJAX
            fetch(`/admin/reports/${reportId}`)
                .then(response => response.json())
                .then(data => {
                    const report = data.report;
                    const reviews = data.reviews;

                    let reviewsHtml = '';
                    if (reviews.length > 0) {
                        reviewsHtml = '<h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Reviews précédentes</h5>';
                        reviews.forEach(review => {
                            reviewsHtml += `
                                <div class="bg-white dark:bg-slate-700 rounded-lg p-3 border border-slate-200 dark:border-slate-600">
                                    <div class="flex items-start justify-between mb-2">
                                        <span class="text-sm font-medium text-slate-900 dark:text-slate-100">${review.reviewer_name}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">${review.created_at}</span>
                                    </div>
                                    <p class="text-sm text-slate-700 dark:text-slate-300">${review.comment}</p>
                                </div>
                            `;
                        });
                    }

                    const content = `
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-900 dark:text-slate-100">${userName}</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">${date}</p>
                                </div>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ${status === 'submitted' ? 'bg-emerald-100 text-emerald-700' : status === 'reviewed' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700'}">
                                    ${status === 'submitted' ? 'Soumis' : status === 'reviewed' ? 'Révisé' : 'Brouillon'}
                                </span>
                            </div>

                            ${hours > 0 ? `
                            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M6 6h12"></path>
                                </svg>
                                ${hours} heure(s) déclarée(s)
                            </div>
                            ` : ''}

                            <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4">
                                <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Contenu du rapport</h5>
                                <p class="text-slate-900 dark:text-slate-100 whitespace-pre-line">${summary}</p>
                            </div>

                            <div id="existingReviews" class="space-y-3">
                                ${reviewsHtml}
                            </div>
                        </div>
                    `;

                    document.getElementById('reportContent').innerHTML = content;
                    document.getElementById('reportModal').classList.remove('hidden');
                    document.getElementById('feedback').focus();
                })
                .catch(error => {
                    console.error('Erreur lors du chargement du rapport:', error);
                    alert('Erreur lors du chargement du rapport');
                });
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
            document.getElementById('responseForm').reset();
            currentReportId = null;
        }

        // Gestionnaire pour le formulaire de réponse
        document.getElementById('responseForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const feedback = formData.get('feedback').trim();

            if (!feedback) {
                alert('Veuillez saisir une réponse.');
                return;
            }

            // Désactiver le bouton pendant l'envoi
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Envoi en cours...';

            // Envoyer via AJAX
            fetch('{{ route("admin.reports.respond") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Réponse envoyée avec succès !');
                        closeReportModal();
                        // Recharger la page pour voir les changements
                        window.location.reload();
                    } else {
                        alert('Erreur lors de l\'envoi de la réponse.');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de l\'envoi de la réponse.');
                })
                .finally(() => {
                    // Réactiver le bouton
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
        });

        document.getElementById('applyFilter').addEventListener('click', function() {
            const date = document.getElementById('reportDate').value;
            const period = new URLSearchParams(window.location.search).get('period') || 'daily';
            window.location.href = `?period=${period}&date=${date}`;
        });
    </script>
</x-app-layout>