<x-app-layout title="Suivi des Pointages - Présence">

    {{-- ════════════════════════════════════════════════════════
         STYLES IDENTITÉ « MON STAGE » (COMPACT / ZERO ERREUR)
    ════════════════════════════════════════════════════════ --}}
    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }
        header { background-color: transparent !important; border-bottom: none !important; box-shadow: none !important; }
        .dark header { background-color: #131519 !important; border-bottom: none !important; box-shadow: none !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
        }

        .ks-label { font-size: .68rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: #6e6862; }
        .dark .ks-label { color: #a89f93 !important; }

        .ks-btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; background: #1c3e96; color: #fff; border: 1px solid #1c3e96; border-radius: 6px; font-size: .85rem; font-weight: 600; padding: .4rem .9rem; min-height: 34px; transition: background-color .15s ease, transform .1s ease; }
        .ks-btn-primary:hover { background: #162f73; border-color: #162f73; }
        .ks-btn-primary:active { transform: scale(.98); }

        .ks-btn-secondary { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; background: #fff; color: #241f1b; border: 1px solid #bfbab2; border-radius: 6px; font-size: .85rem; font-weight: 500; padding: .4rem .9rem; min-height: 34px; transition: all .15s ease; }
        .dark .ks-btn-secondary { background: #131519; color: #fff; border-color: #222630; }
        .ks-btn-secondary:hover { border-color: #1c3e96; color: #1c3e96; }

        .ks-btn-xs { min-height: 28px !important; padding: .25rem .65rem !important; font-size: .74rem !important; font-weight: 600; border-radius: 5px !important; flex: none; white-space: nowrap; }

        .ks-sec { background: #fffefa; border: 1px solid #dcdad2; border-radius: 10px; box-shadow: 0 1px 2px rgba(36,31,27,.04); transition: border-color .15s ease, box-shadow .18s ease-out; }
        .dark .ks-sec { background: #131519 !important; border: 1px solid #222630 !important; border-color: #222630 !important; box-shadow: none; }
        .ks-sec-head { padding: .65rem .9rem; border-bottom: 1px solid #dcdad2; display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
        .dark .ks-sec-head { border-bottom: 1px solid #222630 !important; border-color: #222630 !important; }

        .ks-stat-card { border: 1px solid #dcdad2; border-radius: 10px; background-color: #fffefa; }
        .dark .ks-stat-card { background: #131519 !important; border: 1px solid #222630 !important; }

        .ks-stat-icon { width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; flex: none; }
        .ks-stat-icon svg { width: 15px; height: 15px; stroke-width: 1.75; }

        .ks-num { font-variant-numeric: tabular-nums; }
        .ks-stat-change { font-size: .68rem; font-weight: 600; padding: .15rem .45rem; border-radius: 4px; display: inline-flex; align-items: center; gap: .25rem; white-space: nowrap; }
        .ks-stat-change--positive { color: #15803d; background-color: #ecfdf5; }
        .dark .ks-stat-change--positive { color: #4ade80 !important; background-color: rgba(22,128,74,0.22) !important; }
        .ks-stat-change--neutral { color: #475569; background-color: #f1f5f9; }
        .dark .ks-stat-change--neutral { color: #cbd5e1 !important; background-color: #262a30 !important; }
        .ks-stat-change--negative { color: #b91c1c; background-color: #fef2f2; }
        .dark .ks-stat-change--negative { color: #f87171 !important; background-color: rgba(185,28,28,0.22) !important; }

        .ks-track { background: #ecebe3; border-radius: 999px; overflow: hidden; }
        .dark .ks-track { background: #262a30 !important; }

        .tracking-table th, .tracking-table td { white-space: nowrap; }

        /* Dark mode overrides complets */
        .dark .text-slate-900, .dark .text-slate-800, .dark .text-gray-900 { color: #f8fafc !important; }
        .dark .text-slate-600, .dark .text-slate-500, .dark .text-gray-600 { color: #94a3b8 !important; }
        .dark .border-slate-200, .dark .border-slate-300, .dark .border-gray-200 { border-color: #222630 !important; }
    </style>

    <div class="space-y-3 sm:space-y-3.5 overflow-x-hidden">
        {{-- ════════════════════════════════════════════════════════
             BANNER SUIVI POINTAGES (STYLE MON STAGE COMPACT)
        ════════════════════════════════════════════════════════ --}}
        <div class="relative overflow-hidden rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-gradient-to-r from-[#1c3e96] via-[#1a3889] to-[#162f73] dark:from-[#131519] dark:via-[#161a22] dark:to-[#131519] shadow-sm">
            <div class="absolute inset-0 opacity-15 pointer-events-none"
                style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;)">
            </div>
            <div class="relative px-3.5 sm:px-4 py-2.5 sm:py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="ks-stat-icon !w-6 !h-6 rounded bg-white/15 text-white">
                                <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <h1 class="text-base sm:text-lg font-bold text-white tracking-tight leading-tight">Suivi des Pointages</h1>
                        </div>
                        <p class="text-[11px] sm:text-xs text-blue-200 dark:text-gray-400 mt-0.5">Contrôle journalier et périodique des présences, retards et absences</p>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/10 dark:bg-white/5 border border-white/15 dark:border-[#222630] text-xs font-semibold text-white whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-blue-200 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $filterDate->locale('fr')->isoFormat('DD MMMM YYYY') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             BARRE DE PÉRIODE & FILTRES (COMPACTE STYLE MON STAGE)
        ════════════════════════════════════════════════════════ --}}
        <div class="ks-sec p-2.5 sm:p-3">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-2.5">
                {{-- Onglets Période segmentés --}}
                <div class="inline-flex items-center p-0.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] self-start sm:self-auto">
                    @php $p = request('period', 'day'); @endphp
                    @foreach([
                        ['day', 'Jour'],
                        ['week', 'Semaine'],
                        ['month', 'Mois'],
                        ['year', 'Année']
                    ] as [$val, $label])
                    <a href="?period={{ $val }}{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}"
                       class="px-3 py-1 text-xs font-semibold rounded-md transition-all {{ $p === $val ? 'bg-[#1c3e96] text-white shadow-sm' : 'text-[#6e6862] dark:text-[#a89f93] hover:text-[#241f1b] dark:hover:text-white' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                {{-- Filtres Utilisateur & Date --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <div class="relative flex-1 sm:w-64">
                        <select id="userFilter" name="user_id"
                                class="w-full h-8 text-xs font-medium rounded-md border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] text-[#241f1b] dark:text-[#f8fafc] focus:border-[#1c3e96] focus:ring-1 focus:ring-[#1c3e96] pr-8">
                            <option value="">Tous les utilisateurs</option>
                            @foreach($allUsers as $user)
                            <option value="{{ $user['id'] }}" {{ request('user_id') == $user['id'] ? 'selected' : '' }}>
                                {{ $user['name'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative sm:w-40">
                        <input type="date" id="dateFilter" name="date" value="{{ $filterDate->format('Y-m-d') }}"
                               class="w-full h-8 text-xs font-medium rounded-md border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#131519] text-[#241f1b] dark:text-[#f8fafc] focus:border-[#1c3e96] focus:ring-1 focus:ring-[#1c3e96]">
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             VUE JOUR : STATS & TABLEAUX
        ════════════════════════════════════════════════════════ --}}
        @if(request('period', 'day') === 'day')
        @php
            $stTotal = (int)($summary['student_total'] ?? 0);
            $stPresent = (int)($summary['student_present'] ?? 0);
            $stRate = $stTotal > 0 ? round(($stPresent / $stTotal) * 100, 1) : 0;
            $stPerm = (int)($summary['student_permissioned'] ?? 0);

            $empTotal = (int)($summary['employee_total'] ?? 0);
            $empPresent = (int)($summary['employee_present'] ?? 0);
            $empRate = $empTotal > 0 ? round(($empPresent / $empTotal) * 100, 1) : 0;
            $empPerm = (int)($summary['employee_permissioned'] ?? 0);
        @endphp
        <div class="space-y-3 sm:space-y-3.5">
            {{-- 2 Blocs Résumé (Stagiaires / Employés) - Style Mon Stage Raffiné --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                {{-- Carte Stagiaires --}}
                <div class="ks-sec overflow-hidden">
                    <div class="ks-sec-head">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="ks-stat-icon bg-[#eef1fb] text-[#1c3e96] dark:bg-[rgba(28,62,150,0.25)] dark:text-[#60a5fa]">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc] truncate">Stagiaires</h3>
                                <p class="text-[11px] text-[#6e6862] dark:text-[#94a3b8] truncate">Suivi des présences stagiaires</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if($stPerm > 0)
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                {{ $stPerm }} perm.
                            </span>
                            @endif
                            <span class="badge ks-stat-change {{ $stRate > 0 ? 'ks-stat-change--positive' : 'ks-stat-change--neutral' }}">
                                {{ $stRate }}%
                            </span>
                        </div>
                    </div>

                    {{-- Jauge d'assiduité --}}
                    <div class="px-3 pt-2.5 pb-1">
                        <div class="flex items-center justify-between text-[11px] text-[#6e6862] dark:text-[#94a3b8] mb-1">
                            <span class="font-medium">Taux d'assiduité du jour</span>
                            <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ks-num">{{ $stPresent }} / {{ $stTotal }}</span>
                        </div>
                        <div class="ks-track h-1.5 w-full bg-[#ecebe3] dark:bg-[#262a30] rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $stRate > 0 ? 'bg-[#15803d] dark:bg-emerald-400' : 'bg-[#bfbab2] dark:bg-[#333948]' }}" style="width: {{ min(100, max(0, $stRate)) }}%"></div>
                        </div>
                    </div>

                    {{-- 2 Sous-Cartes KPI « Mon Stage » --}}
                    <div class="p-2.5 sm:p-3 grid grid-cols-2 gap-2 sm:gap-2.5">
                        {{-- Total inscrits --}}
                        <div class="p-2.5 sm:p-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#15181f] flex flex-col justify-between transition-all hover:border-[#1c3e96]/40">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="ks-label truncate">Total inscrits</span>
                                <span class="ks-stat-icon !w-5 !h-5 !rounded bg-[#eef1fb] text-[#1c3e96] dark:bg-[rgba(28,62,150,0.25)] dark:text-[#60a5fa]">
                                    <svg class="!w-3 !h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </span>
                            </div>
                            <div class="flex items-baseline justify-between gap-1 mt-0.5">
                                <p class="ks-num text-xl sm:text-2xl font-bold text-[#241f1b] dark:text-[#f8fafc]">{{ $stTotal }}</p>
                                <span class="text-[10px] sm:text-[11px] font-medium text-[#6e6862] dark:text-[#94a3b8]">inscrits</span>
                            </div>
                        </div>

                        {{-- Présents --}}
                        <div class="p-2.5 sm:p-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#15181f] flex flex-col justify-between transition-all hover:border-emerald-500/40">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="ks-label text-[#15803d] dark:text-emerald-400 truncate">Présents</span>
                                <span class="ks-stat-icon !w-5 !h-5 !rounded bg-[#ecfdf5] text-[#15803d] dark:bg-[rgba(22,128,74,0.22)] dark:text-[#4ade80]">
                                    <svg class="!w-3 !h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                            </div>
                            <div class="flex items-baseline justify-between gap-1 mt-0.5">
                                <p class="ks-num text-xl sm:text-2xl font-bold text-[#15803d] dark:text-emerald-400">{{ $stPresent }}</p>
                                <span class="badge ks-stat-change {{ $stRate > 0 ? 'ks-stat-change--positive' : 'ks-stat-change--neutral' }} !text-[10px] !px-1.5 !py-0.5">
                                    {{ $stRate }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Carte Employés --}}
                <div class="ks-sec overflow-hidden">
                    <div class="ks-sec-head">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="ks-stat-icon bg-[#ecfdf5] text-[#15803d] dark:bg-[rgba(22,128,74,0.22)] dark:text-[#4ade80]">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc] truncate">Employés</h3>
                                <p class="text-[11px] text-[#6e6862] dark:text-[#94a3b8] truncate">Suivi des présences du personnel</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if($empPerm > 0)
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                {{ $empPerm }} perm.
                            </span>
                            @endif
                            <span class="badge ks-stat-change {{ $empRate > 0 ? 'ks-stat-change--positive' : 'ks-stat-change--neutral' }}">
                                {{ $empRate }}%
                            </span>
                        </div>
                    </div>

                    {{-- Jauge d'assiduité --}}
                    <div class="px-3 pt-2.5 pb-1">
                        <div class="flex items-center justify-between text-[11px] text-[#6e6862] dark:text-[#94a3b8] mb-1">
                            <span class="font-medium">Taux d'assiduité du personnel</span>
                            <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ks-num">{{ $empPresent }} / {{ $empTotal }}</span>
                        </div>
                        <div class="ks-track h-1.5 w-full bg-[#ecebe3] dark:bg-[#262a30] rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $empRate > 0 ? 'bg-[#15803d] dark:bg-emerald-400' : 'bg-[#bfbab2] dark:bg-[#333948]' }}" style="width: {{ min(100, max(0, $empRate)) }}%"></div>
                        </div>
                    </div>

                    {{-- 2 Sous-Cartes KPI « Mon Stage » --}}
                    <div class="p-2.5 sm:p-3 grid grid-cols-2 gap-2 sm:gap-2.5">
                        {{-- Total effectif --}}
                        <div class="p-2.5 sm:p-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#15181f] flex flex-col justify-between transition-all hover:border-[#4338ca]/40">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="ks-label truncate">Total effectif</span>
                                <span class="ks-stat-icon !w-5 !h-5 !rounded bg-[#eef2ff] text-[#4338ca] dark:bg-[rgba(67,56,202,0.25)] dark:text-[#818cf8]">
                                    <svg class="!w-3 !h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </span>
                            </div>
                            <div class="flex items-baseline justify-between gap-1 mt-0.5">
                                <p class="ks-num text-xl sm:text-2xl font-bold text-[#241f1b] dark:text-[#f8fafc]">{{ $empTotal }}</p>
                                <span class="text-[10px] sm:text-[11px] font-medium text-[#6e6862] dark:text-[#94a3b8]">salariés</span>
                            </div>
                        </div>

                        {{-- Présents --}}
                        <div class="p-2.5 sm:p-3 rounded-lg border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#15181f] flex flex-col justify-between transition-all hover:border-emerald-500/40">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="ks-label text-[#15803d] dark:text-emerald-400 truncate">Présents</span>
                                <span class="ks-stat-icon !w-5 !h-5 !rounded bg-[#ecfdf5] text-[#15803d] dark:bg-[rgba(22,128,74,0.22)] dark:text-[#4ade80]">
                                    <svg class="!w-3 !h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                            </div>
                            <div class="flex items-baseline justify-between gap-1 mt-0.5">
                                <p class="ks-num text-xl sm:text-2xl font-bold text-[#15803d] dark:text-emerald-400">{{ $empPresent }}</p>
                                <span class="badge ks-stat-change {{ $empRate > 0 ? 'ks-stat-change--positive' : 'ks-stat-change--neutral' }} !text-[10px] !px-1.5 !py-0.5">
                                    {{ $empRate }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── TABLEAU STAGIAIRES ── --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon bg-[#eef1fb] text-[#1c3e96] dark:bg-[rgba(28,62,150,0.25)] dark:text-[#60a5fa] !w-6 !h-6 rounded">
                            <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Pointages du {{ $displayDate }} — Stagiaires</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-[#6e6862] dark:text-[#a89f93]">{{ count($attendanceStudents) }} enregistré{{ count($attendanceStudents) > 1 ? 's' : '' }}</span>
                </div>

                {{-- Mobile list --}}
                <div class="block sm:hidden divide-y divide-[#ecebe3] dark:divide-[#222630]">
                    @forelse($attendanceStudents as $day)
                    @php
                        $hour = $day->first_check_in_at?->hour ?? null;
                        $minute = $day->first_check_in_at?->minute ?? null;
                        $stClass = 'bg-[#f1f5f9] text-[#475569] dark:bg-[#262a30] dark:text-[#cbd5e1]';
                        $stText = 'Absent';
                        if ($hour !== null) {
                            if ($hour < 8 || ($hour == 7 && $minute <= 45)) {
                                $stClass = 'bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]';
                                $stText = "À l'heure";
                            } elseif ($hour == 7 && $minute > 45 && $minute < 60) {
                                $stClass = 'bg-[#fef3c7] text-[#b45309] dark:bg-amber-950/60 dark:text-[#fbbf24]';
                                $stText = 'Tard';
                            } else {
                                $stClass = 'bg-[#fee2e2] text-[#b91c1c] dark:bg-rose-950/60 dark:text-[#f87171]';
                                $stText = 'Retard';
                            }
                        }
                    @endphp
                    <div class="p-3 space-y-2 hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc] truncate">{{ $day->etudiant?->user?->name ?? 'N/A' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $stClass }}">{{ $stText }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Arrivée:</span> <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ml-1">{{ $day->first_check_in_at?->format('H:i') ?? '—' }}</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Départ:</span> <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ml-1">{{ $day->last_check_out_at?->format('H:i') ?? '—' }}</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Position:</span> <span class="ml-1 text-[#241f1b] dark:text-[#f8fafc]">{{ $day->stage?->site ? $day->stage->site->name : 'À distance' }}</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Retard:</span> <span class="ml-1 font-semibold {{ $day->late_minutes > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }}">{{ $day->late_minutes > 0 ? $day->late_minutes . ' min' : '0 min' }}</span></div>
                        </div>
                        @if($day->arrival_status === 'late' && $day->late_observation)
                        <div class="text-[11px] bg-[#fffbeb] dark:bg-amber-950/40 text-[#b45309] dark:text-[#fbbf24] p-1.5 rounded border border-amber-200 dark:border-amber-800/60">
                            {{ Str::limit($day->late_observation, 50) }}
                        </div>
                        @endif
                        <div class="pt-1 flex justify-end">
                            <a href="{{ route('attendance.tracking.user.historique', $day->etudiant->user) }}"
                               class="ks-btn-secondary ks-btn-xs">
                                Historique →
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucun pointage des stagiaires pour cette date</div>
                    @endforelse
                </div>

                {{-- Desktop Table --}}
                <div class="hidden sm:block overflow-x-auto custom-scrollbar">
                    <table class="tracking-table min-w-full divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        <thead>
                            <tr class="bg-[#faf9f6] dark:bg-[#181c24]">
                                <th class="px-4 py-2.5 text-left ks-label">Nom</th>
                                <th class="px-4 py-2.5 text-left ks-label">Arrivée</th>
                                <th class="px-4 py-2.5 text-left ks-label">Départ</th>
                                <th class="px-4 py-2.5 text-left ks-label">Position</th>
                                <th class="px-4 py-2.5 text-left ks-label">Statut</th>
                                <th class="px-4 py-2.5 text-left ks-label">Retard</th>
                                <th class="px-4 py-2.5 text-left ks-label">Observation</th>
                                <th class="px-4 py-2.5 text-right ks-label">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            @forelse($attendanceStudents as $day)
                            @php
                                $hour = $day->first_check_in_at?->hour ?? null;
                                $minute = $day->first_check_in_at?->minute ?? null;
                                $stClass = 'bg-[#f1f5f9] text-[#475569] dark:bg-[#262a30] dark:text-[#cbd5e1]';
                                $stText = 'Absent';
                                if ($hour !== null) {
                                    if ($hour < 8 || ($hour == 7 && $minute <= 45)) {
                                        $stClass = 'bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]';
                                $stText = "À l'heure";
                                    } elseif ($hour == 7 && $minute > 45 && $minute < 60) {
                                        $stClass = 'bg-[#fef3c7] text-[#b45309] dark:bg-amber-950/60 dark:text-[#fbbf24]';
                                        $stText = 'Tard';
                                    } else {
                                        $stClass = 'bg-[#fee2e2] text-[#b91c1c] dark:bg-rose-950/60 dark:text-[#f87171]';
                                        $stText = 'Retard';
                                    }
                                }
                                $resolvedSite = $day->stage?->site ?? $day->checkInEvent?->geofence?->site;
                            @endphp
                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $day->etudiant?->user?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-xs text-[#241f1b] dark:text-[#f8fafc]">
                                    {{ $day->first_check_in_at ? $day->first_check_in_at->format('H:i') : '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-xs text-[#241f1b] dark:text-[#f8fafc]">
                                    {{ $day->last_check_out_at ? $day->last_check_out_at->format('H:i') : '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-xs">
                                    @if($resolvedSite)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-[#eef1fb] text-[#1c3e96] dark:bg-blue-950/60 dark:text-blue-300">
                                        {{ $resolvedSite->name }}
                                    </span>
                                    @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-[#f1f5f9] text-[#6e6862] dark:bg-[#262a30] dark:text-[#a89f93]">
                                        À distance
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $stClass }}">{{ $stText }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-xs font-semibold">
                                    @if($day->late_minutes > 0)
                                    <span class="text-[#b91c1c] dark:text-[#f87171]">{{ $day->late_minutes }} min</span>
                                    @else
                                    <span class="text-[#15803d] dark:text-[#4ade80]">0 min</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs">
                                    @if($day->arrival_status === 'late' && $day->late_observation)
                                    <span class="px-2 py-0.5 rounded text-[11px] bg-[#fffbeb] text-[#b45309] dark:bg-amber-950/60 dark:text-[#fbbf24] border border-amber-200 dark:border-amber-800" title="{{ e($day->late_observation) }}">
                                        {{ Str::limit($day->late_observation, 30) }}
                                    </span>
                                    @else
                                    <span class="text-[#a89f93] dark:text-[#64748b]">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs text-right">
                                    <a href="{{ route('attendance.tracking.user.historique', $day->etudiant->user) }}"
                                       class="ks-btn-secondary ks-btn-xs">
                                        Historique →
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucun pointage des stagiaires pour cette date</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── TABLEAU EMPLOYÉS ── --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon bg-[#ecfdf5] text-[#15803d] dark:bg-[rgba(22,128,74,0.22)] dark:text-[#4ade80] !w-6 !h-6 rounded">
                            <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Pointages du {{ $displayDate }} — Employés</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-[#6e6862] dark:text-[#a89f93]">{{ count($attendanceEmployees) }} enregistré{{ count($attendanceEmployees) > 1 ? 's' : '' }}</span>
                </div>

                {{-- Mobile list --}}
                <div class="block sm:hidden divide-y divide-[#ecebe3] dark:divide-[#222630]">
                    @forelse($attendanceEmployees as $day)
                    @php
                        $hour = $day->first_check_in_at?->hour ?? null;
                        $minute = $day->first_check_in_at?->minute ?? null;
                        $stClass = 'bg-[#f1f5f9] text-[#475569] dark:bg-[#262a30] dark:text-[#cbd5e1]';
                        $stText = 'Absent';
                        if ($hour !== null) {
                            if ($hour < 8 || ($hour == 7 && $minute <= 45)) {
                                $stClass = 'bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]';
                                $stText = "À l'heure";
                            } elseif ($hour == 7 && $minute > 45 && $minute < 60) {
                                $stClass = 'bg-[#fef3c7] text-[#b45309] dark:bg-amber-950/60 dark:text-[#fbbf24]';
                                $stText = 'Tard';
                            } else {
                                $stClass = 'bg-[#fee2e2] text-[#b91c1c] dark:bg-rose-950/60 dark:text-[#f87171]';
                                $stText = 'Retard';
                            }
                        }
                    @endphp
                    <div class="p-3 space-y-2 hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc] truncate">{{ $day->user?->name ?? 'N/A' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $stClass }}">{{ $stText }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Arrivée:</span> <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ml-1">{{ $day->first_check_in_at?->format('H:i') ?? '—' }}</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Départ:</span> <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ml-1">{{ $day->last_check_out_at?->format('H:i') ?? '—' }}</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Position:</span> <span class="ml-1 text-[#241f1b] dark:text-[#f8fafc]">{{ $day->stage?->site ? $day->stage->site->name : 'À distance' }}</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Retard:</span> <span class="ml-1 font-semibold {{ $day->late_minutes > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }}">{{ $day->late_minutes > 0 ? $day->late_minutes . ' min' : '0 min' }}</span></div>
                        </div>
                        @if($day->arrival_status === 'late' && $day->late_observation)
                        <div class="text-[11px] bg-[#fffbeb] dark:bg-amber-950/40 text-[#b45309] dark:text-[#fbbf24] p-1.5 rounded border border-amber-200 dark:border-amber-800/60">
                            {{ Str::limit($day->late_observation, 50) }}
                        </div>
                        @endif
                        <div class="pt-1 flex justify-end">
                            <a href="{{ route('attendance.tracking.user.historique', $day->user) }}"
                               class="ks-btn-secondary ks-btn-xs">
                                Historique →
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucun pointage des employés pour cette date</div>
                    @endforelse
                </div>

                {{-- Desktop Table --}}
                <div class="hidden sm:block overflow-x-auto custom-scrollbar">
                    <table class="tracking-table min-w-full divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        <thead>
                            <tr class="bg-[#faf9f6] dark:bg-[#181c24]">
                                <th class="px-4 py-2.5 text-left ks-label">Nom</th>
                                <th class="px-4 py-2.5 text-left ks-label">Arrivée</th>
                                <th class="px-4 py-2.5 text-left ks-label">Départ</th>
                                <th class="px-4 py-2.5 text-left ks-label">Position</th>
                                <th class="px-4 py-2.5 text-left ks-label">Statut</th>
                                <th class="px-4 py-2.5 text-left ks-label">Retard</th>
                                <th class="px-4 py-2.5 text-left ks-label">Observation</th>
                                <th class="px-4 py-2.5 text-right ks-label">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            @forelse($attendanceEmployees as $day)
                            @php
                                $hour = $day->first_check_in_at?->hour ?? null;
                                $minute = $day->first_check_in_at?->minute ?? null;
                                $stClass = 'bg-[#f1f5f9] text-[#475569] dark:bg-[#262a30] dark:text-[#cbd5e1]';
                                $stText = 'Absent';
                                if ($hour !== null) {
                                    if ($hour < 8 || ($hour == 7 && $minute <= 45)) {
                                        $stClass = 'bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]';
                                $stText = "À l'heure";
                                    } elseif ($hour == 7 && $minute > 45 && $minute < 60) {
                                        $stClass = 'bg-[#fef3c7] text-[#b45309] dark:bg-amber-950/60 dark:text-[#fbbf24]';
                                        $stText = 'Tard';
                                    } else {
                                        $stClass = 'bg-[#fee2e2] text-[#b91c1c] dark:bg-rose-950/60 dark:text-[#f87171]';
                                        $stText = 'Retard';
                                    }
                                }
                                $resolvedSite = $day->stage?->site ?? $day->checkInEvent?->geofence?->site;
                            @endphp
                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $day->user?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-xs text-[#241f1b] dark:text-[#f8fafc]">
                                    {{ $day->first_check_in_at ? $day->first_check_in_at->format('H:i') : '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-xs text-[#241f1b] dark:text-[#f8fafc]">
                                    {{ $day->last_check_out_at ? $day->last_check_out_at->format('H:i') : '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-xs">
                                    @if($resolvedSite)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-[#ecfdf5] text-[#15803d] dark:bg-emerald-950/60 dark:text-emerald-300">
                                        {{ $resolvedSite->name }}
                                    </span>
                                    @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-[#f1f5f9] text-[#6e6862] dark:bg-[#262a30] dark:text-[#a89f93]">
                                        À distance
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $stClass }}">{{ $stText }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-xs font-semibold">
                                    @if($day->late_minutes > 0)
                                    <span class="text-[#b91c1c] dark:text-[#f87171]">{{ $day->late_minutes }} min</span>
                                    @else
                                    <span class="text-[#15803d] dark:text-[#4ade80]">0 min</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs">
                                    @if($day->arrival_status === 'late' && $day->late_observation)
                                    <span class="px-2 py-0.5 rounded text-[11px] bg-[#fffbeb] text-[#b45309] dark:bg-amber-950/60 dark:text-[#fbbf24] border border-amber-200 dark:border-amber-800" title="{{ e($day->late_observation) }}">
                                        {{ Str::limit($day->late_observation, 30) }}
                                    </span>
                                    @else
                                    <span class="text-[#a89f93] dark:text-[#64748b]">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs text-right">
                                    <a href="{{ route('attendance.tracking.user.historique', $day->user) }}"
                                       class="ks-btn-secondary ks-btn-xs">
                                        Historique →
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucun pointage des employés pour cette date</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- ════════════════════════════════════════════════════════
             VUE SEMAINE : SUIVI HEBDOMADAIRE
        ════════════════════════════════════════════════════════ --}}
        @if(request('period', 'day') === 'week')
        <div class="space-y-3 sm:space-y-3.5">
            {{-- Stagiaires Hebdo --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon bg-[#eef1fb] text-[#1c3e96] dark:bg-[rgba(28,62,150,0.25)] dark:text-[#60a5fa] !w-6 !h-6 rounded">
                            <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Suivi hebdomadaire — Stagiaires</h3>
                    </div>
                </div>

                <div class="block sm:hidden divide-y divide-[#ecebe3] dark:divide-[#222630]">
                    @forelse($studentWeekData as $data)
                    <div class="p-3 space-y-2 hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition">
                        <div class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc]">{{ $data['owner']?->user?->name ?? 'N/A' }}</div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-[#6e6862] dark:text-[#a89f93]">Jours présents:</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]">{{ $data['present_days'] }}/{{ $data['expected_days'] }} j</span>
                        </div>
                        @if(($data['permissioned_days'] ?? 0) > 0)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-[#6e6862] dark:text-[#a89f93]">Permissions:</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">{{ $data['permissioned_days'] }} j</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-[#6e6862] dark:text-[#a89f93]">Retard total:</span>
                            <span class="font-semibold {{ $data['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }}">{{ $data['total_late_minutes'] > 0 ? $data['total_late_minutes'] . ' min' : '0 min' }}</span>
                        </div>
                        <div class="pt-1 flex justify-end">
                            <a href="{{ route('attendance.tracking.user.historique', $data['owner']->user) }}" class="ks-btn-secondary ks-btn-xs">Historique →</a>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée stagiaire pour cette semaine.</div>
                    @endforelse
                </div>

                <div class="hidden sm:block overflow-x-auto custom-scrollbar">
                    <table class="tracking-table min-w-full divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        <thead>
                            <tr class="bg-[#faf9f6] dark:bg-[#181c24]">
                                <th class="px-4 py-2.5 text-left ks-label">Nom</th>
                                <th class="px-4 py-2.5 text-left ks-label">Jours présents</th>
                                <th class="px-4 py-2.5 text-left ks-label">Retard total</th>
                                <th class="px-4 py-2.5 text-right ks-label">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            @forelse($studentWeekData as $data)
                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $data['owner']?->user?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-xs">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]">{{ $data['present_days'] }}/{{ $data['expected_days'] }} j</span>
                                    @if(($data['permissioned_days'] ?? 0) > 0)
                                    <span class="inline-block ml-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">{{ $data['permissioned_days'] }} perm.</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs font-semibold {{ $data['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }}">
                                    {{ $data['total_late_minutes'] > 0 ? $data['total_late_minutes'] . ' min' : '0 min' }}
                                </td>
                                <td class="px-4 py-2.5 text-xs text-right">
                                    <a href="{{ route('attendance.tracking.user.historique', $data['owner']->user) }}" class="ks-btn-secondary ks-btn-xs">Historique →</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée stagiaire pour cette semaine.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Employés Hebdo --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon bg-[#ecfdf5] text-[#15803d] dark:bg-[rgba(22,128,74,0.22)] dark:text-[#4ade80] !w-6 !h-6 rounded">
                            <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Suivi hebdomadaire — Employés</h3>
                    </div>
                </div>

                <div class="block sm:hidden divide-y divide-[#ecebe3] dark:divide-[#222630]">
                    @forelse($employeeWeekData as $data)
                    <div class="p-3 space-y-2 hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition">
                        <div class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc]">{{ $data['owner']?->name ?? 'N/A' }}</div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-[#6e6862] dark:text-[#a89f93]">Jours présents:</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]">{{ $data['present_days'] }}/{{ $data['expected_days'] }} j</span>
                        </div>
                        @if(($data['permissioned_days'] ?? 0) > 0)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-[#6e6862] dark:text-[#a89f93]">Permissions:</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">{{ $data['permissioned_days'] }} j</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-[#6e6862] dark:text-[#a89f93]">Retard total:</span>
                            <span class="font-semibold {{ $data['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }}">{{ $data['total_late_minutes'] > 0 ? $data['total_late_minutes'] . ' min' : '0 min' }}</span>
                        </div>
                        <div class="pt-1 flex justify-end">
                            <a href="{{ route('attendance.tracking.user.historique', $data['owner']) }}" class="ks-btn-secondary ks-btn-xs">Historique →</a>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée employé pour cette semaine.</div>
                    @endforelse
                </div>

                <div class="hidden sm:block overflow-x-auto custom-scrollbar">
                    <table class="tracking-table min-w-full divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        <thead>
                            <tr class="bg-[#faf9f6] dark:bg-[#181c24]">
                                <th class="px-4 py-2.5 text-left ks-label">Nom</th>
                                <th class="px-4 py-2.5 text-left ks-label">Jours présents</th>
                                <th class="px-4 py-2.5 text-left ks-label">Retard total</th>
                                <th class="px-4 py-2.5 text-right ks-label">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            @forelse($employeeWeekData as $data)
                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $data['owner']?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-xs">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]">{{ $data['present_days'] }}/{{ $data['expected_days'] }} j</span>
                                    @if(($data['permissioned_days'] ?? 0) > 0)
                                    <span class="inline-block ml-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">{{ $data['permissioned_days'] }} perm.</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs font-semibold {{ $data['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }}">
                                    {{ $data['total_late_minutes'] > 0 ? $data['total_late_minutes'] . ' min' : '0 min' }}
                                </td>
                                <td class="px-4 py-2.5 text-xs text-right">
                                    <a href="{{ route('attendance.tracking.user.historique', $data['owner']) }}" class="ks-btn-secondary ks-btn-xs">Historique →</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée employé pour cette semaine.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- ════════════════════════════════════════════════════════
             VUE MOIS : SUIVI MENSUEL
        ════════════════════════════════════════════════════════ --}}
        @if(request('period', 'day') === 'month')
        <div class="space-y-3 sm:space-y-3.5">
            {{-- Stagiaires Mois --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon bg-[#eef1fb] text-[#1c3e96] dark:bg-[rgba(28,62,150,0.25)] dark:text-[#60a5fa] !w-6 !h-6 rounded">
                            <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Suivi mensuel — Stagiaires</h3>
                    </div>
                </div>

                <div class="block sm:hidden divide-y divide-[#ecebe3] dark:divide-[#222630]">
                    @forelse($studentMonthlySummary as $summary)
                    <div class="p-3 space-y-2 hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition">
                        <div class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['owner']?->user?->name ?? 'N/A' }}</div>
                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Jours présents:</span> <span class="font-semibold text-[#15803d] dark:text-[#4ade80] ml-1">{{ $summary['present_days'] }}/{{ $summary['expected_days'] }} j</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Heures travaillées:</span> <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ml-1">{{ $summary['total_worked_hours'] }}h</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Retard cumulé:</span> <span class="font-semibold {{ $summary['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }} ml-1">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</span></div>
                            @if(($summary['permissioned_days'] ?? 0) > 0)
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Permissions:</span> <span class="font-semibold text-sky-700 dark:text-sky-300 ml-1">{{ $summary['permissioned_days'] }} j</span></div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée stagiaire pour ce mois.</div>
                    @endforelse
                </div>

                <div class="hidden sm:block overflow-x-auto custom-scrollbar">
                    <table class="tracking-table min-w-full divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        <thead>
                            <tr class="bg-[#faf9f6] dark:bg-[#181c24]">
                                <th class="px-4 py-2.5 text-left ks-label">Nom</th>
                                <th class="px-4 py-2.5 text-left ks-label">Jours présents</th>
                                <th class="px-4 py-2.5 text-left ks-label">Heures travaillées</th>
                                <th class="px-4 py-2.5 text-left ks-label">Retard cumulé</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            @forelse($studentMonthlySummary as $summary)
                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['owner']?->user?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-xs">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]">{{ $summary['present_days'] }}/{{ $summary['expected_days'] }} j</span>
                                    @if(($summary['permissioned_days'] ?? 0) > 0)
                                    <span class="inline-block ml-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">{{ $summary['permissioned_days'] }} perm.</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-4 py-2.5 text-xs font-semibold {{ $summary['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }}">
                                    {{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée stagiaire pour ce mois.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Employés Mois --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon bg-[#ecfdf5] text-[#15803d] dark:bg-[rgba(22,128,74,0.22)] dark:text-[#4ade80] !w-6 !h-6 rounded">
                            <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Suivi mensuel — Employés</h3>
                    </div>
                </div>

                <div class="block sm:hidden divide-y divide-[#ecebe3] dark:divide-[#222630]">
                    @forelse($employeeMonthlySummary as $summary)
                    <div class="p-3 space-y-2 hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition">
                        <div class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['owner']?->name ?? 'N/A' }}</div>
                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Jours présents:</span> <span class="font-semibold text-[#15803d] dark:text-[#4ade80] ml-1">{{ $summary['present_days'] }}/{{ $summary['expected_days'] }} j</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Heures travaillées:</span> <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ml-1">{{ $summary['total_worked_hours'] }}h</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Retard cumulé:</span> <span class="font-semibold {{ $summary['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }} ml-1">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</span></div>
                            @if(($summary['permissioned_days'] ?? 0) > 0)
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Permissions:</span> <span class="font-semibold text-sky-700 dark:text-sky-300 ml-1">{{ $summary['permissioned_days'] }} j</span></div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée employé pour ce mois.</div>
                    @endforelse
                </div>

                <div class="hidden sm:block overflow-x-auto custom-scrollbar">
                    <table class="tracking-table min-w-full divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        <thead>
                            <tr class="bg-[#faf9f6] dark:bg-[#181c24]">
                                <th class="px-4 py-2.5 text-left ks-label">Nom</th>
                                <th class="px-4 py-2.5 text-left ks-label">Jours présents</th>
                                <th class="px-4 py-2.5 text-left ks-label">Heures travaillées</th>
                                <th class="px-4 py-2.5 text-left ks-label">Retard cumulé</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            @forelse($employeeMonthlySummary as $summary)
                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['owner']?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-xs">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]">{{ $summary['present_days'] }}/{{ $summary['expected_days'] }} j</span>
                                    @if(($summary['permissioned_days'] ?? 0) > 0)
                                    <span class="inline-block ml-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">{{ $summary['permissioned_days'] }} perm.</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-4 py-2.5 text-xs font-semibold {{ $summary['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }}">
                                    {{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée employé pour ce mois.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- ════════════════════════════════════════════════════════
             VUE ANNÉE : SUIVI ANNUEL
        ════════════════════════════════════════════════════════ --}}
        @if(request('period', 'day') === 'year')
        <div class="space-y-3 sm:space-y-3.5">
            {{-- Stagiaires Année --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon bg-[#eef1fb] text-[#1c3e96] dark:bg-[rgba(28,62,150,0.25)] dark:text-[#60a5fa] !w-6 !h-6 rounded">
                            <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Suivi annuel — Stagiaires</h3>
                    </div>
                </div>

                <div class="block sm:hidden divide-y divide-[#ecebe3] dark:divide-[#222630]">
                    @forelse($studentYearlySummary as $summary)
                    <div class="p-3 space-y-2 hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition">
                        <div class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['owner']?->user?->name ?? 'N/A' }}</div>
                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Jours présents:</span> <span class="font-semibold text-[#15803d] dark:text-[#4ade80] ml-1">{{ $summary['present_days'] }}/{{ $summary['expected_days'] }} j</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Heures travaillées:</span> <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ml-1">{{ $summary['total_worked_hours'] }}h</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Retard:</span> <span class="font-semibold {{ $summary['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }} ml-1">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Anomalies:</span> <span class="font-semibold {{ $summary['anomalies_count'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }} ml-1">{{ $summary['anomalies_count'] }}</span></div>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée stagiaire pour cette année.</div>
                    @endforelse
                </div>

                <div class="hidden sm:block overflow-x-auto custom-scrollbar">
                    <table class="tracking-table min-w-full divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        <thead>
                            <tr class="bg-[#faf9f6] dark:bg-[#181c24]">
                                <th class="px-4 py-2.5 text-left ks-label">Nom</th>
                                <th class="px-4 py-2.5 text-left ks-label">Jours présents</th>
                                <th class="px-4 py-2.5 text-left ks-label">Heures travaillées</th>
                                <th class="px-4 py-2.5 text-left ks-label">Retard cumulé</th>
                                <th class="px-4 py-2.5 text-right ks-label">Anomalies</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            @forelse($studentYearlySummary as $summary)
                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['owner']?->user?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-xs">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]">{{ $summary['present_days'] }}/{{ $summary['expected_days'] }} j</span>
                                    @if(($summary['permissioned_days'] ?? 0) > 0)
                                    <span class="inline-block ml-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">{{ $summary['permissioned_days'] }} perm.</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-4 py-2.5 text-xs font-semibold {{ $summary['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }}">
                                    {{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}
                                </td>
                                <td class="px-4 py-2.5 text-xs text-right font-semibold">
                                    <span class="px-2 py-0.5 rounded text-[11px] {{ $summary['anomalies_count'] > 0 ? 'bg-[#fee2e2] text-[#b91c1c] dark:bg-rose-950/60 dark:text-[#f87171]' : 'bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]' }}">
                                        {{ $summary['anomalies_count'] }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée stagiaire pour cette année.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Employés Année --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon bg-[#ecfdf5] text-[#15803d] dark:bg-[rgba(22,128,74,0.22)] dark:text-[#4ade80] !w-6 !h-6 rounded">
                            <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Suivi annuel — Employés</h3>
                    </div>
                </div>

                <div class="block sm:hidden divide-y divide-[#ecebe3] dark:divide-[#222630]">
                    @forelse($employeeYearlySummary as $summary)
                    <div class="p-3 space-y-2 hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition">
                        <div class="font-semibold text-xs text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['owner']?->name ?? 'N/A' }}</div>
                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Jours présents:</span> <span class="font-semibold text-[#15803d] dark:text-[#4ade80] ml-1">{{ $summary['present_days'] }}/{{ $summary['expected_days'] }} j</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Heures travaillées:</span> <span class="font-semibold text-[#241f1b] dark:text-[#f8fafc] ml-1">{{ $summary['total_worked_hours'] }}h</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Retard:</span> <span class="font-semibold {{ $summary['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }} ml-1">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</span></div>
                            <div><span class="text-[#6e6862] dark:text-[#a89f93]">Anomalies:</span> <span class="font-semibold {{ $summary['anomalies_count'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }} ml-1">{{ $summary['anomalies_count'] }}</span></div>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée employé pour cette année.</div>
                    @endforelse
                </div>

                <div class="hidden sm:block overflow-x-auto custom-scrollbar">
                    <table class="tracking-table min-w-full divide-y divide-[#ecebe3] dark:divide-[#222630]">
                        <thead>
                            <tr class="bg-[#faf9f6] dark:bg-[#181c24]">
                                <th class="px-4 py-2.5 text-left ks-label">Nom</th>
                                <th class="px-4 py-2.5 text-left ks-label">Jours présents</th>
                                <th class="px-4 py-2.5 text-left ks-label">Heures travaillées</th>
                                <th class="px-4 py-2.5 text-left ks-label">Retard cumulé</th>
                                <th class="px-4 py-2.5 text-right ks-label">Anomalies</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            @forelse($employeeYearlySummary as $summary)
                            <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['owner']?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-xs">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]">{{ $summary['present_days'] }}/{{ $summary['expected_days'] }} j</span>
                                    @if(($summary['permissioned_days'] ?? 0) > 0)
                                    <span class="inline-block ml-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">{{ $summary['permissioned_days'] }} perm.</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs font-semibold text-[#241f1b] dark:text-[#f8fafc]">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-4 py-2.5 text-xs font-semibold {{ $summary['total_late_minutes'] > 0 ? 'text-[#b91c1c] dark:text-[#f87171]' : 'text-[#15803d] dark:text-[#4ade80]' }}">
                                    {{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}
                                </td>
                                <td class="px-4 py-2.5 text-xs text-right font-semibold">
                                    <span class="px-2 py-0.5 rounded text-[11px] {{ $summary['anomalies_count'] > 0 ? 'bg-[#fee2e2] text-[#b91c1c] dark:bg-rose-950/60 dark:text-[#f87171]' : 'bg-[#dcfce7] text-[#15803d] dark:bg-emerald-950/60 dark:text-[#4ade80]' }}">
                                        {{ $summary['anomalies_count'] }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune donnée employé pour cette année.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- ════════════════════════════════════════════════════════
             GRAPHIQUE ÉVOLUTION STATISTIQUES (SI UTILISATEUR SÉLECTIONNÉ)
        ════════════════════════════════════════════════════════ --}}
        @if($userStats && $userStats['chart_data'])
        <div class="ks-sec overflow-hidden">
            <div class="ks-sec-head">
                <div class="flex items-center gap-2">
                    <span class="ks-stat-icon bg-[#eef1fb] text-[#1c3e96] dark:bg-[rgba(28,62,150,0.25)] dark:text-[#60a5fa] !w-6 !h-6 rounded">
                        <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">
                            Évolution des statistiques — {{ $allUsers->where('id', $selectedUserId)->first()['name'] ?? 'Utilisateur' }}
                        </h3>
                        <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">Période : {{ ["today"=>"Aujourd'hui","week"=>"Cette semaine","month"=>"Ce mois","year"=>"Cette année","day"=>"Jour"][$period] ?? ucfirst($period) }}</p>
                    </div>
                </div>
            </div>
            <div class="p-3 sm:p-4">
                <div class="relative h-60 sm:h-72 md:h-80">
                    <canvas id="presenceChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.getElementById('dateFilter').addEventListener('change', function() {
            const period = new URLSearchParams(window.location.search).get('period') || 'day';
            const userId = new URLSearchParams(window.location.search).get('user_id') || '';
            window.location.href = `?period=${period}&date=${this.value}${userId ? '&user_id=' + userId : ''}`;
        });

        document.getElementById('userFilter').addEventListener('change', function() {
            const period = new URLSearchParams(window.location.search).get('period') || 'day';
            const date = document.getElementById('dateFilter').value;
            window.location.href = `?period=${period}&date=${date}&user_id=${this.value}`;
        });

        @if($userStats && $userStats['chart_data'])
        var chartData = @json($userStats['chart_data'] ?? []);
        var labels = Array.isArray(chartData.labels) ? chartData.labels : [];
        var present = Array.isArray(chartData.present) ? chartData.present : [];
        var onTime = Array.isArray(chartData.on_time) ? chartData.on_time : [];
        var lateDays = Array.isArray(chartData.late_days) ? chartData.late_days : [];
        var absences = Array.isArray(chartData.absences) ? chartData.absences : [];
        var workedHoursRaw = Array.isArray(chartData.worked_hours) ? chartData.worked_hours : [];
        var workedHours = workedHoursRaw.map(function(v) { return Math.round(v * 60); });
        var lateMinutes = Array.isArray(chartData.late_minutes) ? chartData.late_minutes : [];

        var canvas = document.getElementById('presenceChart');
        if (canvas && labels.length > 0) {
            var isMobile = window.innerWidth < 640;
            var isDark = document.documentElement.classList.contains('dark');
            var ctx = canvas.getContext('2d');
            var grad = (c1, c2) => {
                var g = ctx.createLinearGradient(0, 0, 0, 320);
                g.addColorStop(0, c1);
                g.addColorStop(1, c2);
                return g;
            };

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Présence',
                            data: present,
                            borderColor: '#15803d',
                            backgroundColor: grad('rgba(21,128,61,0.2)', 'rgba(21,128,61,0.01)'),
                            fill: true,
                            stepped: 'before',
                            tension: 0,
                            borderWidth: 2,
                            pointRadius: present.map(v => v > 0 ? (isMobile ? 3 : 4) : 0),
                            pointHoverRadius: isMobile ? 5 : 6,
                            pointBackgroundColor: '#15803d',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1.5,
                            yAxisID: 'yBinary'
                        },
                        {
                            label: 'Minutes retard',
                            data: lateMinutes,
                            borderColor: '#b45309',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.35,
                            borderWidth: 2,
                            borderDash: [5, 4],
                            pointRadius: lateMinutes.map(v => v > 0 ? (isMobile ? 3 : 4) : 0),
                            pointHoverRadius: isMobile ? 5 : 6,
                            pointBackgroundColor: '#b45309',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1.5,
                            yAxisID: 'yMinutes'
                        },
                        {
                            label: 'Heures travaillées',
                            data: workedHours,
                            borderColor: '#1c3e96',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.3,
                            borderWidth: 2,
                            pointRadius: workedHours.map(v => v > 0 ? (isMobile ? 3 : 4) : 0),
                            pointHoverRadius: isMobile ? 4 : 5,
                            pointBackgroundColor: '#1c3e96',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1.5,
                            yAxisID: 'yMinutes'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 600 },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 8,
                                padding: isMobile ? 6 : 10,
                                font: { size: isMobile ? 10 : 11, weight: '600' },
                                color: isDark ? '#94a3b8' : '#6e6862',
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: '#131519',
                            borderColor: '#222630',
                            borderWidth: 1,
                            titleColor: '#f8fafc',
                            bodyColor: '#94a3b8',
                            padding: 8,
                            cornerRadius: 6,
                            callbacks: {
                                label: function(context) {
                                    var val = context.parsed.y;
                                    var label = context.dataset.label;
                                    if (label.includes('Minutes')) return label + ': ' + val + ' min';
                                    if (label.includes('Heures')) { var h = Math.floor(val / 60), m = val % 60; return label + ': ' + h + 'h' + (m ? ' ' + m + 'min' : ''); }
                                    if (val === 1) return label + ': Oui';
                                    if (val === 0) return label + ': Non';
                                    return label + ': ' + val;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                font: { size: isMobile ? 9 : 10 },
                                color: isDark ? '#94a3b8' : '#6e6862',
                                autoSkip: true,
                                maxTicksLimit: isMobile ? 6 : 12
                            },
                            grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' }
                        },
                        yBinary: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            min: 0,
                            max: 1.25,
                            title: { display: false },
                            ticks: {
                                stepSize: 1,
                                callback: function(v) { return v === 1 ? 'Présent' : (v === 0 ? 'Absent' : ''); },
                                font: { size: 9, weight: 'bold' },
                                color: isDark ? '#94a3b8' : '#6e6862'
                            },
                            grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' }
                        },
                        yMinutes: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: { display: false },
                            grid: { drawOnChartArea: false },
                            ticks: {
                                font: { size: 9 },
                                color: isDark ? '#94a3b8' : '#6e6862'
                            }
                        }
                    }
                }
            });
        }
        @endif
    </script>
</x-app-layout>
