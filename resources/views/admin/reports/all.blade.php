<x-app-layout title="Tous les Rapports">
    <div class="w-full space-y-3.5 pb-8 text-slate-900 dark:text-[#f8fafc]">
        {{-- ── 1. BANNIÈRE D'EN-TÊTE ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-[#f8fafc]">
                                Tous les rapports
                            </h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                Vue exhaustive
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Tableau complet et recherche globale de tous les rapports (stagiaires et employés).
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reports.index') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-xs font-semibold text-slate-700 dark:text-slate-200 transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V4a2 2 0 0 1 2-2Z"/>
                        </svg>
                        <span>Vue par section</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Flash messages ── --}}
        @if(session('success'))
        <div class="flex items-center gap-2.5 p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-[10px] text-xs">
            <svg class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-2.5 p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-[10px] text-xs">
            <svg class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- ── 2. CARTES KPIS ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
            <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3.5 sm:p-4 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total</span>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-[#f8fafc] mt-1">{{ $summary['total'] }}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-[#1f232d] text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>

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
        </div>

        {{-- ── 3. BARRE DE FILTRES & RECHERCHE ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] p-3 sm:p-3.5 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="inline-flex p-1 rounded-lg bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] gap-1 self-start sm:self-auto flex-wrap">
                    @foreach(['all' => 'Tout', 'daily' => 'Jour', 'weekly' => 'Semaine', 'monthly' => 'Mois'] as $key => $label)
                    <a href="{{ route('admin.reports.all', array_merge(['period' => $key, 'date' => $filterDate->format('Y-m-d')], $search ? ['q' => $search] : [])) }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold transition {{ $period === $key ? 'bg-[#1c3e96] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    @if($period !== 'all')
                    <input type="date" id="reportDate" value="{{ $filterDate->format('Y-m-d') }}"
                           class="rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] px-3 py-1.5 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-[#1c3e96]" />
                    <button id="applyFilter"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#1c3e96] hover:bg-[#162f73] text-white text-xs font-semibold transition shadow-xs">
                        Filtrer
                    </button>
                    @endif
                    <form method="GET" action="{{ route('admin.reports.all') }}" class="flex items-center gap-2 w-full sm:w-auto">
                        @if($period && $period !== 'all')<input type="hidden" name="period" value="{{ $period }}">@endif
                        @if($period && $period !== 'all')<input type="hidden" name="date" value="{{ $filterDate->format('Y-m-d') }}">@endif
                        <input type="text" name="q" value="{{ $search }}" placeholder="Rechercher utilisateur ou mot-clé…"
                               class="w-full sm:w-60 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] px-3 py-1.5 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#1c3e96] focus:ring-1 focus:ring-[#1c3e96]" />
                        <button class="px-3 py-1.5 rounded-lg bg-[#f6f5f0] hover:bg-[#eeece2] dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] text-slate-700 dark:text-slate-200 text-xs font-semibold transition shrink-0">
                            Rechercher
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── 4. TABLEAU DES RAPPORTS ── --}}
        <div class="rounded-[10px] bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] overflow-hidden shadow-xs">
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-[#dcdad2] dark:border-[#222630] bg-[#f6f5f0] dark:bg-[#131519] text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                            <th class="w-[24%] px-4 py-3">Utilisateur</th>
                            <th class="w-[36%] px-4 py-3">Contenu / Résumé</th>
                            <th class="w-[10%] px-3 py-3">Statut</th>
                            <th class="w-[10%] px-3 py-3">Date</th>
                            <th class="w-[6%] px-2 py-3 text-center">Heures</th>
                            <th class="w-[14%] px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dcdad2] dark:divide-[#222630]">
                        @forelse($reports as $report)
                        @php
                            $authorName = $report->etudiant?->user?->name ?? $report->user?->name ?? 'N/A';
                            $isStudent  = $report->etudiant_id !== null;
                            $summary    = $report->summary ?? $report->introduction ?? '—';
                            $initials   = collect(explode(' ', $authorName))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                        @endphp
                        <tr class="hover:bg-[#faf9f5] dark:hover:bg-[#1a1e27] transition">
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full {{ $isStudent ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }} flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ $initials ?: 'U' }}
                                    </div>
                                    <div class="truncate max-w-[170px]">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-slate-900 dark:text-[#f8fafc] truncate">{{ $authorName }}</span>
                                            <span class="inline-flex rounded px-1 py-0.2 text-[9px] font-bold uppercase tracking-wide {{ $isStudent ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                                                {{ $isStudent ? 'Stage' : 'Emp' }}
                                            </span>
                                        </div>
                                        @if($report->stage?->theme)
                                        <p class="text-[11px] text-slate-400 truncate" title="{{ $report->stage->theme }}">{{ $report->stage->theme }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                @if($report->title)
                                <p class="text-xs font-semibold text-slate-900 dark:text-[#f8fafc] truncate">{{ $report->title }}</p>
                                @endif
                                <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed">{{ $summary }}</p>
                            </td>
                            <td class="px-3 py-3 align-middle whitespace-nowrap">
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
                            </td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium align-middle whitespace-nowrap">{{ $report->report_date->format('d/m/Y') }}</td>
                            <td class="px-2 py-3 text-slate-900 dark:text-slate-100 font-bold text-center align-middle whitespace-nowrap">{{ $report->hours_declared ? $report->hours_declared . 'h' : '—' }}</td>
                            <td class="px-4 py-3 align-middle text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.reports.show', $report->id) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-slate-700 bg-[#f6f5f0] hover:bg-[#eeece2] dark:text-slate-200 dark:bg-[#1f232d] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] rounded-md transition"
                                       title="Consulter les détails">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Détails
                                    </a>
                                    <button type="button"
                                            onclick="openBilanModal({{ $report->id }}, '{{ str_replace(["'", "\\"], ["\\'", "\\\\"], $authorName) }}', '{{ $report->report_date->format('d/m/Y') }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-md transition shadow-xs"
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
                            <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-500 dark:text-slate-400">Aucun rapport trouvé pour ces critères.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile (< md / 375px) --}}
            <div class="block md:hidden divide-y divide-[#dcdad2] dark:divide-[#222630]">
                @forelse($reports as $report)
                @php
                    $authorName = $report->etudiant?->user?->name ?? $report->user?->name ?? 'N/A';
                    $isStudent  = $report->etudiant_id !== null;
                    $summary    = $report->summary ?? $report->introduction ?? '—';
                    $initials   = collect(explode(' ', $authorName))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('');
                @endphp
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full {{ $isStudent ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }} flex items-center justify-center font-bold text-xs shrink-0">
                                {{ $initials ?: 'U' }}
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <p class="text-xs font-bold text-slate-900 dark:text-[#f8fafc]">{{ $authorName }}</p>
                                    <span class="inline-flex rounded px-1 py-0.2 text-[9px] font-bold uppercase {{ $isStudent ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                                        {{ $isStudent ? 'Stage' : 'Emp' }}
                                    </span>
                                </div>
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

                    <p class="text-xs text-slate-600 dark:text-slate-300 bg-[#f6f5f0] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-lg p-2.5 line-clamp-2">
                        {{ $summary }}
                    </p>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <a href="{{ route('admin.reports.show', $report->id) }}"
                           class="w-full inline-flex items-center justify-center gap-1 py-1.5 rounded-lg text-xs font-medium text-slate-700 dark:text-slate-200 bg-[#f6f5f0] dark:bg-[#1f232d] border border-[#dcdad2] dark:border-[#222630] transition">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Détails
                        </a>
                        <button type="button"
                                onclick="openBilanModal({{ $report->id }}, '{{ str_replace(["'", "\\"], ["\\'", "\\\\"], $authorName) }}', '{{ $report->report_date->format('d/m/Y') }}')"
                                class="w-full inline-flex items-center justify-center gap-1 py-1.5 rounded-lg text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l12.429 3.2L3 14.4V8zm14 5.5 3 3-3 3m-3-3h6"/>
                            </svg>
                            Bilan
                        </button>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-xs text-slate-500 dark:text-slate-400">
                    Aucun rapport trouvé pour ces critères.
                </div>
                @endforelse
            </div>

            @if($reports->hasPages())
            <div class="px-4 py-3 border-t border-[#dcdad2] dark:border-[#222630]">
                {{ $reports->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ── MODAL BILAN ── --}}
    <div id="bilanModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-3 sm:p-4">
            <div class="bg-[#fffefa] dark:bg-[#161a22] border border-[#dcdad2] dark:border-[#222630] rounded-[12px] max-w-md w-full shadow-2xl overflow-hidden">
                <div class="p-5">
                    <div class="flex items-start gap-3.5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l12.429 3.2L3 14.4V8zm14 5.5 3 3-3 3m-3-3h6"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-[#f8fafc]">Envoyer le bilan hebdomadaire</h3>
                            <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-400">
                                Confirmer l'envoi du bilan de présence à
                                <span id="bilanName" class="font-semibold text-slate-900 dark:text-[#f8fafc]"></span>
                                pour la semaine du rapport du
                                <span id="bilanDate" class="font-semibold text-slate-900 dark:text-[#f8fafc]"></span> ?
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-end gap-2 pt-3 border-t border-[#dcdad2] dark:border-[#222630]">
                        <button type="button" onclick="closeBilanModal()"
                                class="px-3.5 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 bg-[#f6f5f0] dark:bg-[#1f232d] hover:bg-[#eeece2] dark:hover:bg-[#282d3a] border border-[#dcdad2] dark:border-[#222630] rounded-lg transition">
                            Annuler
                        </button>
                        <form id="bilanSendForm" method="POST" class="inline-block">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        @if($period !== 'all')
        document.getElementById('applyFilter')?.addEventListener('click', function() {
            const date = document.getElementById('reportDate').value;
            window.location.href = `?period={{ $period }}&date=${date}${'{{ $search }}' ? '&q={{ $search }}' : ''}`;
        });
        @endif
    </script>
</x-app-layout>