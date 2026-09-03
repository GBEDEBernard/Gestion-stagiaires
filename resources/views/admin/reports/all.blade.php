<x-app-layout title="Tous les Rapports">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 text-slate-900 dark:text-slate-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Tous les rapports</h1>
                <p class="mt-2 text-lg text-slate-600 dark:text-slate-300">Tableau complet des rapports de tous les utilisateurs (stagiaires et employés).</p>
            </div>
            <a href="{{ route('admin.reports.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V4a2 2 0 0 1 2-2Z"/>
                </svg>
                Page rapports existante
            </a>
        </div>

        {{-- ── Flash ── --}}
        @if(session('success'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-2xl text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-2xl text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Total</p>
                <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ $summary['total'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Soumis</p>
                <p class="mt-3 text-3xl font-bold text-emerald-600">{{ $summary['submitted'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Brouillons</p>
                <p class="mt-3 text-3xl font-bold text-amber-600">{{ $summary['draft'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Révisés</p>
                <p class="mt-3 text-3xl font-bold text-blue-600">{{ $summary['reviewed'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    @foreach(['all' => '🗂 Tout', 'daily' => '📅 Jour', 'weekly' => '📊 Semaine', 'monthly' => '📈 Mois'] as $key => $label)
                    <a href="{{ route('admin.reports.all', array_merge(['period' => $key, 'date' => $filterDate->format('Y-m-d')], $search ? ['q' => $search] : [])) }}"
                       class="px-4 py-2 rounded-xl {{ $period === $key ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }} transition-all">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    @if($period !== 'all')
                    <input type="date" id="reportDate" value="{{ $filterDate->format('Y-m-d') }}"
                           class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" />
                    <button id="applyFilter" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-all">Filtrer</button>
                    @endif
                    <form method="GET" action="{{ route('admin.reports.all') }}" class="flex items-center gap-2">
                        @if($period && $period !== 'all')<input type="hidden" name="period" value="{{ $period }}">@endif
                        @if($period && $period !== 'all')<input type="hidden" name="date" value="{{ $filterDate->format('Y-m-d') }}">@endif
                        <input type="text" name="q" value="{{ $search }}" placeholder="Rechercher un nom ou une description…"
                               class="w-full sm:w-64 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" />
                        <button class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 transition-all">Rechercher</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden w-full">
            <div class="w-full">
                <table class="w-full table-fixed divide-y divide-slate-200 dark:divide-slate-700 text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800">
                            <th class="w-[22%] px-4 py-3.5 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Utilisateur</th>
                            <th class="w-[36%] px-4 py-3.5 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Description</th>
                            <th class="w-[10%] px-3 py-3.5 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="w-[10%] px-3 py-3.5 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Date</th>
                            <th class="w-[6%] px-2 py-3.5 text-center text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Heures</th>
                            <th class="w-[16%] px-4 py-3.5 text-right text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($reports as $report)
                        @php
                            $authorName = $report->etudiant?->user?->name ?? $report->user?->name ?? 'N/A';
                            $isStudent  = $report->etudiant_id !== null;
                            $summary    = $report->summary ?? $report->introduction ?? '—';
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors">
                            <td class="px-4 py-3.5 align-middle">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-slate-900 dark:text-slate-100 truncate max-w-[150px]">{{ $authorName }}</span>
                                    <span class="inline-flex rounded px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide {{ $isStudent ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' }}">
                                        {{ $isStudent ? 'Stagiaire' : 'Employé' }}
                                    </span>
                                </div>
                                @if($report->stage?->theme)
                                <p class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-400 truncate" title="{{ $report->stage->theme }}">{{ $report->stage->theme }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                @if($report->title)
                                <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $report->title }}</p>
                                @endif
                                <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed break-words">{{ $summary }}</p>
                            </td>
                            <td class="px-3 py-3.5 align-middle">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold
                                    {{ $report->status === 'submitted' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : ($report->status === 'reviewed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300') }}">
                                    {{ ['submitted' => 'Soumis', 'reviewed' => 'Validé', 'draft' => 'Brouillon'][$report->status] ?? ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-3.5 text-slate-700 dark:text-slate-300 font-medium align-middle whitespace-nowrap">{{ $report->report_date->format('d/m/Y') }}</td>
                            <td class="px-2 py-3.5 text-slate-900 dark:text-slate-100 font-bold text-center align-middle whitespace-nowrap">{{ $report->hours_declared ? $report->hours_declared . 'h' : '—' }}</td>
                            <td class="px-4 py-3.5 align-middle">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('admin.reports.show', $report->id) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 dark:text-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg transition"
                                       title="Consulter les détails">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Détails
                                    </a>
                                    <button type="button"
                                            onclick="openBilanModal({{ $report->id }}, '{{ str_replace(["'", "\\"], ["\\'", "\\\\"], $authorName) }}', '{{ $report->report_date->format('d/m/Y') }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-900/40 dark:hover:bg-emerald-900/60 rounded-lg transition"
                                            title="Envoyer le bilan">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l12.429 3.2L3 14.4V8zm14 5.5 3 3-3 3m-3-3h6"/>
                                        </svg>
                                        Bilan
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">Aucun rapport trouvé pour ces critères.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reports->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $reports->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- MODAL DÉTAILS DU RAPPORT -->
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

    <!-- MODAL CONFIRMATION ENVOI DU BILAN -->
    <div id="bilanModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full shadow-2xl ws-modal-appear">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                            <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l12.429 3.2L3 14.4V8zm14 5.5 3 3-3 3m-3-3h6"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Envoyer le bilan hebdomadaire</h3>
                            <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-slate-400">
                                Confirmer l'envoi du bilan de présence à
                                <span id="bilanName" class="font-semibold text-slate-900 dark:text-slate-100"></span>
                                pour la semaine du rapport du
                                <span id="bilanDate" class="font-semibold text-slate-900 dark:text-slate-100"></span> ?
                            </p>
                        </div>
                        <button type="button" onclick="closeBilanModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeBilanModal()"
                            class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            Annuler
                        </button>
                        <form id="bilanSendForm" method="POST" class="inline-block">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l12.429 3.2L3 14.4V8zm14 5.5 3 3-3 3m-3-3h6"/>
                                </svg>
                                Confirmer l'envoi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ─── MODAL ENVOI DU BILAN ───────────────────────────────────────────────
        function openBilanModal(reportId, userName, date) {
            document.getElementById('bilanName').textContent  = userName;
            document.getElementById('bilanDate').textContent  = date;
            document.getElementById('bilanSendForm').action   = `/admin/reports/${reportId}/send-bilan`;
            document.getElementById('bilanModal').classList.remove('hidden');
        }

        function closeBilanModal() {
            document.getElementById('bilanModal').classList.add('hidden');
        }

        document.getElementById('bilanModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeBilanModal();
        });

        function viewReportDetails(reportId) {
            fetch(`/admin/reports/${reportId}`)
                .then(r => r.json())
                .then(data => {
                    const report  = data.report;
                    const reviews = data.reviews || [];

                    let statusBadge = '';
                    if (report.status === 'submitted') {
                        statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Soumis</span>';
                    } else if (report.status === 'reviewed') {
                        statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-700">Révisé</span>';
                    } else {
                        statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-700">Brouillon</span>';
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
                                </div>`;
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
                                    <p class="text-slate-900 dark:text-slate-100 whitespace-pre-line">${report.summary || '—'}</p>
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
                        </div>`;

                    document.getElementById('reportDetailsContent').innerHTML = content;
                    document.getElementById('reportDetailsModal').classList.remove('hidden');
                })
                .catch(() => alert('Erreur lors du chargement du rapport'));
        }

        function closeReportDetailsModal() {
            document.getElementById('reportDetailsModal').classList.add('hidden');
        }

        @if($period !== 'all')
        document.getElementById('applyFilter')?.addEventListener('click', function() {
            const date = document.getElementById('reportDate').value;
            window.location.href = `?period={{ $period }}&date=${date}${'{{ $search }}' ? '&q={{ $search }}' : ''}`;
        });
        @endif
    </script>
</x-app-layout>