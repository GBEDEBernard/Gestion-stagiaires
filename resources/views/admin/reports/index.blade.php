<x-app-layout title="Suivi des Rapports de travail">
    <div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">
        {{-- ── 1. BANNIÈRE D'EN-TÊTE MON STAGE ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">
                                Rapports de travail
                            </h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                Suivi quotidien
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Supervision et validation des rapports d'activité des stagiaires et des employés.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reports.all') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-xs font-semibold text-slate-700 dark:text-slate-200 transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        <span>Vue exhaustive</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ── 2. CARTES KPIS (4 Indicateurs) ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
            {{-- Total --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total rapports</span>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-[#f8fafc] mt-1">{{ $summary['total'] }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-[#1f232d] text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>

            {{-- Soumis --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Soumis</span>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $summary['submitted'] }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
            </div>

            {{-- Révisés / Validés --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Révisés / Validés</span>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $summary['reviewed'] }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Brouillons --}}
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Brouillons</span>
                    <p class="text-xl sm:text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $summary['draft'] }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ── 3. BARRE DE FILTRES SEGMENTÉS (Période & Date) ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3 sm:p-3.5 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                {{-- Onglets segmentés --}}
                <div class="inline-flex p-1 rounded-lg bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] gap-1 self-start sm:self-auto">
                    <a href="?period=daily&date={{ $filterDate->format('Y-m-d') }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold transition {{ $period === 'daily' ? 'bg-[#1c3e96] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                        Jour
                    </a>
                    <a href="?period=weekly&date={{ $filterDate->format('Y-m-d') }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold transition {{ $period === 'weekly' ? 'bg-[#1c3e96] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                        Semaine
                    </a>
                    <a href="?period=monthly&date={{ $filterDate->format('Y-m-d') }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold transition {{ $period === 'monthly' ? 'bg-[#1c3e96] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                        Mois
                    </a>
                </div>

                {{-- Sélecteur de date et bouton --}}
                <div class="flex items-center gap-2">
                    <input type="date" id="reportDate" name="date" value="{{ $filterDate->format('Y-m-d') }}"
                           class="rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] px-3 py-1.5 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-[#1c3e96]" />
                    <button id="applyFilter"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#1c3e96] hover:bg-[#162f73] text-white text-xs font-semibold transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtrer
                    </button>
                </div>
            </div>
        </div>

        {{-- ── 4. SECTION STAGIAIRES ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
            <div class="px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] bg-[#faf9f5] dark:bg-[#181c24] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]">Stagiaires</h2>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        {{ $studentReports->count() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">
                    Rapports d'activité soumis par les stagiaires
                </p>
            </div>

            {{-- Tableau Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#dcdad2] dark:border-[#222630] bg-[#f6f5f0] dark:bg-[#131519] text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                            <th class="px-4 py-3">Stagiaire</th>
                            <th class="px-4 py-3">Sujet de stage</th>
                            <th class="px-4 py-3">Statut</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-center">Heures</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2] dark:divide-[#222630] text-xs">
                        @forelse($studentReports as $report)
                        @php
                            $stName = $report->etudiant?->user?->name ?? 'Stagiaire';
                            $initials = collect(explode(' ', $stName))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                        @endphp
                        <tr class="hover:bg-[#faf9f5] dark:hover:bg-[#1a1e27] transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ $initials ?: 'ST' }}
                                    </div>
                                    <div class="truncate max-w-[180px]">
                                        <p class="font-semibold text-slate-900 dark:text-[#f8fafc] truncate">{{ $stName }}</p>
                                        <p class="text-[11px] text-slate-400 truncate">{{ $report->etudiant?->user?->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-slate-700 dark:text-slate-300 line-clamp-1" title="{{ $report->stage?->theme ?? 'Non défini' }}">
                                    {{ $report->stage?->theme ?? 'Non défini' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($report->status === 'submitted')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Soumis
                                    </span>
                                @elseif($report->status === 'reviewed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Validé
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Brouillon
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600 dark:text-slate-300">
                                {{ $report->report_date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 dark:bg-[#1f232d] text-slate-700 dark:text-slate-300">
                                    {{ $report->hours_declared ? $report->hours_declared . 'h' : '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button onclick="viewReportDetails({{ $report->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium text-slate-700 dark:text-slate-200 bg-[#f6f5f0] dark:bg-[#1f232d] hover:bg-[#eeece2] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] transition">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Détails
                                    </button>
                                    <button onclick="openReportModal({{ $report->id }}, '{{ addslashes($stName) }}', '{{ addslashes($report->summary ?? '') }}', '{{ $report->report_date->format('d/m/Y') }}', '{{ $report->hours_declared ?? 0 }}', '{{ $report->status }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold text-white bg-[#1c3e96] hover:bg-[#162f73] transition shadow-xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/>
                                        </svg>
                                        Répondre
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                                Aucun rapport stagiaire trouvé pour cette période.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Cartes Mobile (< md / 375px) --}}
            <div class="block md:hidden divide-y divide-[#dcdad2] dark:divide-[#222630]">
                @forelse($studentReports as $report)
                @php
                    $stName = $report->etudiant?->user?->name ?? 'Stagiaire';
                    $initials = collect(explode(' ', $stName))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                @endphp
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 flex items-center justify-center font-bold text-xs shrink-0">
                                {{ $initials ?: 'ST' }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900 dark:text-[#f8fafc]">{{ $stName }}</p>
                                <p class="text-[11px] text-slate-400">{{ $report->report_date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        @if($report->status === 'submitted')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Soumis
                            </span>
                        @elseif($report->status === 'reviewed')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Validé
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Brouillon
                            </span>
                        @endif
                    </div>

                    <div class="text-[11px] text-slate-600 dark:text-slate-300 bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-lg p-2.5">
                        <span class="text-slate-400 font-medium">Stage :</span> {{ $report->stage?->theme ?? 'Non défini' }}
                        @if($report->hours_declared)
                            <span class="block mt-0.5 text-slate-500 dark:text-slate-400"><span class="text-slate-400 font-medium">Temps :</span> {{ $report->hours_declared }}h</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button onclick="viewReportDetails({{ $report->id }})"
                                class="w-full inline-flex items-center justify-center gap-1 py-1.5 rounded-lg text-xs font-medium text-slate-700 dark:text-slate-200 bg-[#f6f5f0] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] transition">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Détails
                        </button>
                        <button onclick="openReportModal({{ $report->id }}, '{{ addslashes($stName) }}', '{{ addslashes($report->summary ?? '') }}', '{{ $report->report_date->format('d/m/Y') }}', '{{ $report->hours_declared ?? 0 }}', '{{ $report->status }}')"
                                class="w-full inline-flex items-center justify-center gap-1 py-1.5 rounded-lg text-xs font-semibold text-white bg-[#1c3e96] hover:bg-[#162f73] transition shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/>
                            </svg>
                            Répondre
                        </button>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-xs text-slate-500 dark:text-slate-400">
                    Aucun rapport stagiaire trouvé pour cette période.
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── 5. SECTION EMPLOYÉS ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
            <div class="px-4 py-3 border-b border-[#dcdad2] dark:border-[#222630] bg-[#faf9f5] dark:bg-[#181c24] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]">Employés</h2>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        {{ $employeeReports->count() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">
                    Rapports d'activité du personnel
                </p>
            </div>

            {{-- Tableau Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#dcdad2] dark:border-[#222630] bg-[#f6f5f0] dark:bg-[#131519] text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                            <th class="px-4 py-3">Employé</th>
                            <th class="px-4 py-3">Service / Contexte</th>
                            <th class="px-4 py-3">Statut</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-center">Heures</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2] dark:divide-[#222630] text-xs">
                        @forelse($employeeReports as $report)
                        @php
                            $empName = $report->user?->name ?? 'Employé';
                            $initials = collect(explode(' ', $empName))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                        @endphp
                        <tr class="hover:bg-[#faf9f5] dark:hover:bg-[#1a1e27] transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ $initials ?: 'EM' }}
                                    </div>
                                    <div class="truncate max-w-[180px]">
                                        <p class="font-semibold text-slate-900 dark:text-[#f8fafc] truncate">{{ $empName }}</p>
                                        <p class="text-[11px] text-slate-400 truncate">{{ $report->user?->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-slate-700 dark:text-slate-300 line-clamp-1" title="{{ $report->stage?->theme ?? 'Activité interne' }}">
                                    {{ $report->stage?->theme ?? 'Activité interne' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($report->status === 'submitted')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Soumis
                                    </span>
                                @elseif($report->status === 'reviewed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Validé
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Brouillon
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600 dark:text-slate-300">
                                {{ $report->report_date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 dark:bg-[#1f232d] text-slate-700 dark:text-slate-300">
                                    {{ $report->hours_declared ? $report->hours_declared . 'h' : '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button onclick="viewReportDetails({{ $report->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium text-slate-700 dark:text-slate-200 bg-[#f6f5f0] dark:bg-[#1f232d] hover:bg-[#eeece2] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] transition">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Détails
                                    </button>
                                    <button onclick="openReportModal({{ $report->id }}, '{{ addslashes($empName) }}', '{{ addslashes($report->summary ?? '') }}', '{{ $report->report_date->format('d/m/Y') }}', '{{ $report->hours_declared ?? 0 }}', '{{ $report->status }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold text-white bg-[#1c3e96] hover:bg-[#162f73] transition shadow-xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/>
                                        </svg>
                                        Répondre
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                                Aucun rapport employé trouvé pour cette période.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Cartes Mobile (< md / 375px) --}}
            <div class="block md:hidden divide-y divide-[#dcdad2] dark:divide-[#222630]">
                @forelse($employeeReports as $report)
                @php
                    $empName = $report->user?->name ?? 'Employé';
                    $initials = collect(explode(' ', $empName))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                @endphp
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-bold text-xs shrink-0">
                                {{ $initials ?: 'EM' }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900 dark:text-[#f8fafc]">{{ $empName }}</p>
                                <p class="text-[11px] text-slate-400">{{ $report->report_date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        @if($report->status === 'submitted')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Soumis
                            </span>
                        @elseif($report->status === 'reviewed')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Validé
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Brouillon
                            </span>
                        @endif
                    </div>

                    <div class="text-[11px] text-slate-600 dark:text-slate-300 bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-lg p-2.5">
                        <span class="text-slate-400 font-medium">Service :</span> {{ $report->stage?->theme ?? 'Activité interne' }}
                        @if($report->hours_declared)
                            <span class="block mt-0.5 text-slate-500 dark:text-slate-400"><span class="text-slate-400 font-medium">Temps :</span> {{ $report->hours_declared }}h</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button onclick="viewReportDetails({{ $report->id }})"
                                class="w-full inline-flex items-center justify-center gap-1 py-1.5 rounded-lg text-xs font-medium text-slate-700 dark:text-slate-200 bg-[#f6f5f0] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] transition">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Détails
                        </button>
                        <button onclick="openReportModal({{ $report->id }}, '{{ addslashes($empName) }}', '{{ addslashes($report->summary ?? '') }}', '{{ $report->report_date->format('d/m/Y') }}', '{{ $report->hours_declared ?? 0 }}', '{{ $report->status }}')"
                                class="w-full inline-flex items-center justify-center gap-1 py-1.5 rounded-lg text-xs font-semibold text-white bg-[#1c3e96] hover:bg-[#162f73] transition shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/>
                            </svg>
                            Répondre
                        </button>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-xs text-slate-500 dark:text-slate-400">
                    Aucun rapport employé trouvé pour cette période.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── MODAL 1 : DÉTAILS COMPLETS DU RAPPORT ── --}}
    <div id="reportDetailsModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-3 sm:p-4">
            <div class="bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] rounded-[12px] max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
                {{-- Entête Modal --}}
                <div class="px-5 py-3.5 border-b border-[#dcdad2] dark:border-[#222630] bg-[#faf9f5] dark:bg-[#181c24] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]">Détails du rapport d'activité</h3>
                    </div>
                    <button onclick="closeReportDetailsModal()" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-[#f6f5f0] dark:hover:bg-[#1f232d] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Corps Modal --}}
                <div class="p-5 overflow-y-auto max-h-[calc(90vh-110px)]" id="reportDetailsContent">
                    {{-- Chargé dynamiquement --}}
                </div>
            </div>
        </div>
    </div>

    {{-- ── MODAL 2 : VOIR ET RÉPONDRE ── --}}
    <div id="reportModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-3 sm:p-4">
            <div class="bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] rounded-[12px] max-w-xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
                {{-- Entête Modal --}}
                <div class="px-5 py-3.5 border-b border-[#dcdad2] dark:border-[#222630] bg-[#faf9f5] dark:bg-[#181c24] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]">Répondre au rapport</h3>
                    </div>
                    <button onclick="closeReportModal()" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-[#f6f5f0] dark:hover:bg-[#1f232d] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Corps Modal --}}
                <div class="p-5 overflow-y-auto max-h-[calc(90vh-110px)] space-y-4">
                    <div id="reportContent">
                        {{-- Chargé dynamiquement --}}
                    </div>

                    <form id="responseForm" method="POST" class="pt-2">
                        @csrf
                        <input type="hidden" name="report_id" id="responseReportId">
                        <div class="space-y-3">
                            <div>
                                <label for="feedback" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                    Votre commentaire ou directive
                                </label>
                                <textarea name="feedback" id="feedback" rows="4"
                                          class="w-full rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] p-3 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#1c3e96] focus:ring-1 focus:ring-[#1c3e96] resize-none"
                                          placeholder="Saisissez votre retour pour orienter le collaborateur..."></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button" onclick="closeReportModal()"
                                        class="px-3.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-300 bg-[#f6f5f0] dark:bg-[#1f232d] hover:bg-[#eeece2] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] rounded-lg transition">
                                    Annuler
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 text-xs font-semibold text-white bg-[#1c3e96] hover:bg-[#162f73] rounded-lg transition shadow-xs flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Envoyer la réponse
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SCRIPTS ── --}}
    <script>
        let currentReportId = null;

        // ── MODAL DÉTAILS ──
        function viewReportDetails(reportId) {
            fetch(`/admin/reports/${reportId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                const report  = data.report;
                const reviews = data.reviews || [];

                let statusBadge = '';
                if (report.status === 'submitted') {
                    statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Soumis</span>';
                } else if (report.status === 'reviewed') {
                    statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Validé</span>';
                } else {
                    statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Brouillon</span>';
                }

                let reviewsHtml = '';
                if (reviews.length > 0) {
                    reviewsHtml = '<div class="mt-5 pt-4 border-t border-[#dcdad2] dark:border-[#222630]"><h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-3">Retours et commentaires (' + reviews.length + ')</h4><div class="space-y-2.5">';
                    reviews.forEach(review => {
                        reviewsHtml += `
                            <div class="bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-lg p-3">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-bold text-slate-900 dark:text-[#f8fafc]">${review.reviewer_name}</span>
                                    <span class="text-[11px] text-slate-400">${review.created_at}</span>
                                </div>
                                <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">${review.comment}</p>
                            </div>
                        `;
                    });
                    reviewsHtml += '</div></div>';
                }

                const stageInfo = report.stage_theme
                    ? `<p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"><span class="font-medium text-slate-600 dark:text-slate-300">Stage :</span> ${report.stage_theme}</p>` : '';

                const content = `
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 pb-3 border-b border-[#dcdad2] dark:border-[#222630]">
                            <div>
                                <h4 class="text-base font-bold text-slate-900 dark:text-[#f8fafc]">${report.author_name}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">${report.report_date_formatted}</p>
                                ${stageInfo}
                                <div class="mt-2">${statusBadge}</div>
                            </div>
                            <div class="flex flex-col sm:items-end text-xs text-slate-400 space-y-0.5">
                                <div>Créé le ${report.created_at_full || report.created_at_formatted}</div>
                                <div>Modifié le ${report.updated_at_full || report.updated_at_formatted}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <div class="bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-lg p-3.5">
                                <h5 class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Résumé des travaux</h5>
                                <p class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">${report.summary || 'Aucun résumé renseigné.'}</p>
                            </div>

                            <div class="space-y-3">
                                ${report.hours_declared ? `
                                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold text-blue-700 dark:text-blue-300">Heures travaillées</span>
                                        <span class="text-base font-bold text-blue-700 dark:text-blue-300">${report.hours_declared}h</span>
                                    </div>
                                </div>` : ''}

                                ${report.blockers ? `
                                <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-3">
                                    <h5 class="text-xs font-semibold text-amber-700 dark:text-amber-300 mb-1">Difficultés ou blocages</h5>
                                    <p class="text-xs text-slate-800 dark:text-slate-200 whitespace-pre-line">${report.blockers}</p>
                                </div>` : ''}

                                ${report.next_steps ? `
                                <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-3">
                                    <h5 class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-1">Prochaines étapes prévues</h5>
                                    <p class="text-xs text-slate-800 dark:text-slate-200 whitespace-pre-line">${report.next_steps}</p>
                                </div>` : ''}
                            </div>
                        </div>

                        ${reviewsHtml}

                        <div class="pt-4 flex flex-wrap items-center justify-between gap-2 border-t border-[#dcdad2] dark:border-[#222630]">
                            <a href="/admin/reports/${report.id}/download-pdf"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#f6f5f0] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-[#eeece2] dark:hover:bg-[#282d3a] transition">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Télécharger PDF
                            </a>
                            <div class="flex items-center gap-2">
                                <a href="/admin/reports/${report.id}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#1c3e96] hover:bg-[#162f73] text-white text-xs font-semibold transition shadow-xs">
                                    Vue détaillée
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                                <button type="button" onclick="closeReportDetailsModal()"
                                        class="px-3 py-1.5 rounded-lg bg-[#f6f5f0] dark:bg-[#1f232d] text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-[#eeece2] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] transition">
                                    Fermer
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('reportDetailsContent').innerHTML = content;
                document.getElementById('reportDetailsModal').classList.remove('hidden');
            })
            .catch(() => alert('Erreur lors du chargement des détails du rapport.'));
        }

        function closeReportDetailsModal() {
            document.getElementById('reportDetailsModal').classList.add('hidden');
        }

        // ── MODAL RÉPONDRE ──
        function openReportModal(reportId, userName, summary, date, hours, status) {
            currentReportId = reportId;
            document.getElementById('responseReportId').value = reportId;

            fetch(`/admin/reports/${reportId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const report = data.report;
                const reviews = data.reviews || [];

                let reviewsHtml = '';
                if (reviews.length > 0) {
                    reviewsHtml = '<h5 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Historique des commentaires</h5><div class="space-y-2 mb-3">';
                    reviews.forEach(review => {
                        reviewsHtml += `
                            <div class="bg-[#f6f5f0] dark:bg-[#131519] rounded-lg p-2.5 border border-[#dcdad2] dark:border-[#222630]">
                                <div class="flex items-start justify-between mb-1">
                                    <span class="text-xs font-bold text-slate-900 dark:text-[#f8fafc]">${review.reviewer_name}</span>
                                    <span class="text-[10px] text-slate-400">${review.created_at}</span>
                                </div>
                                <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">${review.comment}</p>
                            </div>
                        `;
                    });
                    reviewsHtml += '</div>';
                }

                const content = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-[#dcdad2] dark:border-[#222630]">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]">${userName}</h4>
                                <p class="text-xs text-slate-400">${date} ${hours > 0 ? '• ' + hours + 'h déclarée(s)' : ''}</p>
                            </div>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold ${status === 'submitted' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : (status === 'reviewed' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800')}">
                                ${status === 'submitted' ? 'Soumis' : (status === 'reviewed' ? 'Validé' : 'Brouillon')}
                            </span>
                        </div>

                        <div class="bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-lg p-3">
                            <h5 class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Contenu du rapport</h5>
                            <p class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">${summary || 'Aucun contenu.'}</p>
                        </div>

                        ${reviewsHtml}
                    </div>
                `;

                document.getElementById('reportContent').innerHTML = content;
                document.getElementById('reportModal').classList.remove('hidden');
                document.getElementById('feedback').focus();
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement du rapport');
            });
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
            document.getElementById('responseForm').reset();
            currentReportId = null;
        }

        // Formulaire de réponse
        document.getElementById('responseForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const feedback = formData.get('feedback').trim();

            if (!feedback) {
                alert('Veuillez saisir une réponse.');
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Envoi en cours...';

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
                    alert('Réponse enregistrée avec succès !');
                    closeReportModal();
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
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        document.getElementById('applyFilter').addEventListener('click', function() {
            const date = document.getElementById('reportDate').value;
            const period = new URLSearchParams(window.location.search).get('period') || 'daily';
            window.location.href = `?period=${period}&date=${date}`;
        });
    </script>
</x-app-layout>