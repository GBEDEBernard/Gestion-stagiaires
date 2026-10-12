<x-app-layout>

    @php
        $missingDepartureDay = $missingDepartureDay ?? null;
    @endphp

    @include('presence.partials.missing-departure-banner', [
        'missingDeparture' => (bool) ($missingDeparture ?? false),
        'missingDepartureDay' => $missingDepartureDay,
    ])

    {{-- ════════════════════════════════════════════════════════
         STYLES IDENTITÉ « MON STAGE » (KAISSS / HOPE UI COMPACT)
    ════════════════════════════════════════════════════════ --}}
    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }
        header { background-color: transparent !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }
        .dark header { background-color: #131519 !important; border-bottom: none !important; border-color: transparent !important; box-shadow: none !important; }
        .dark header button.rounded-lg, .dark header button.rounded-xl { background-color: #1f232d !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
            --ks-green: #15803d;
            --ks-green-bg: #dcfce7;
            --ks-red: #b91c1c;
            --ks-red-bg: #fee2e2;
            --ks-yellow: #b45309;
            --ks-yellow-bg: #fef3c7;
        }

        .ks-label { font-size: .68rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: #6e6862; }
        .dark .ks-label { color: #a89f93 !important; }

        .ks-btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; background: #1c3e96; color: #fff; border: 1px solid #1c3e96; border-radius: 6px; font-size: .85rem; font-weight: 600; padding: .4rem .9rem; min-height: 34px; transition: background-color .15s ease, transform .1s ease; }
        .ks-btn-primary:hover { background: #162f73; border-color: #162f73; }
        .ks-btn-primary:active { transform: scale(.98); }
        .ks-btn-primary:focus-visible, .ks-btn-secondary:focus-visible, a:focus-visible, button:focus-visible { outline: 2px solid #1c3e96; outline-offset: 2px; }

        .ks-btn-secondary { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; background: #fff; color: #241f1b; border: 1px solid #bfbab2; border-radius: 6px; font-size: .85rem; font-weight: 500; padding: .4rem .9rem; min-height: 34px; transition: all .15s ease; }
        .dark .ks-btn-secondary { background: #131519; color: #fff; border-color: #222630; }
        .ks-btn-secondary:hover { border-color: #1c3e96; color: #1c3e96; }

        .ks-btn-xs { min-height: 28px !important; padding: .25rem .65rem !important; font-size: .74rem !important; font-weight: 600; border-radius: 5px !important; flex: none; white-space: nowrap; }

        .ks-stat-card { border: 1px solid #dcdad2; border-radius: 10px; background-color: #fffefa; transition: transform .2s ease, box-shadow .2s ease; }
        .dark .ks-stat-card { background: #131519 !important; border: 1px solid #222630 !important; border-color: #222630 !important; }
        .ks-stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.05); }

        .ks-stat-icon { width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; flex: none; background-color: var(--ks-stat-icon-bg, #eef1fb); color: var(--ks-stat-icon-color, #1c3e96); }
        .ks-stat-icon svg { width: 15px; height: 15px; stroke-width: 1.75; }

        .ks-stat-card--green { --ks-stat-accent: #16804a; --ks-stat-icon-bg: #ecfdf5; --ks-stat-icon-color: #15803d; }
        .ks-stat-card--blue { --ks-stat-accent: #1c3e96; --ks-stat-icon-bg: #eef1fb; --ks-stat-icon-color: #1c3e96; }
        .ks-stat-card--amber { --ks-stat-accent: #b45309; --ks-stat-icon-bg: #fffbeb; --ks-stat-icon-color: #b45309; }
        .ks-stat-card--red { --ks-stat-accent: #b91c1c; --ks-stat-icon-bg: #fff1f2; --ks-stat-icon-color: #b91c1c; }
        .ks-stat-card--indigo { --ks-stat-accent: #4338ca; --ks-stat-icon-bg: #eef2ff; --ks-stat-icon-color: #4338ca; }
        .ks-stat-card--violet { --ks-stat-accent: #7e22ce; --ks-stat-icon-bg: #faf5ff; --ks-stat-icon-color: #7e22ce; }
        .ks-stat-card--cyan { --ks-stat-accent: #0e7490; --ks-stat-icon-bg: #ecfeff; --ks-stat-icon-color: #0891b2; }

        .dark .ks-stat-card--green { --ks-stat-icon-bg: rgba(22,128,74,0.22); --ks-stat-icon-color: #4ade80; }
        .dark .ks-stat-card--blue { --ks-stat-icon-bg: rgba(28,62,150,0.25); --ks-stat-icon-color: #60a5fa; }
        .dark .ks-stat-card--amber { --ks-stat-icon-bg: rgba(180,83,9,0.22); --ks-stat-icon-color: #fbbf24; }
        .dark .ks-stat-card--red { --ks-stat-icon-bg: rgba(185,28,28,0.22); --ks-stat-icon-color: #f87171; }
        .dark .ks-stat-card--indigo { --ks-stat-icon-bg: rgba(67,56,202,0.25); --ks-stat-icon-color: #818cf8; }
        .dark .ks-stat-card--violet { --ks-stat-icon-bg: rgba(126,34,206,0.25); --ks-stat-icon-color: #c084fc; }
        .dark .ks-stat-card--cyan { --ks-stat-icon-bg: rgba(14,116,144,0.25); --ks-stat-icon-color: #22d3ee; }

        .ks-stat-footer { border-top: 0 !important; }
        .ks-stat-change { border-radius: 4px; font-size: .7rem; font-weight: 600; display: inline-flex; align-items: center; gap: .25rem; padding: .12rem .4rem; }
        .ks-stat-change--positive { color: #166534; background-color: #dcfce7; }
        .ks-stat-change--negative { color: #b91c1c; background-color: #fee2e2; }
        .ks-stat-change--neutral { color: #475569; background-color: #f1f5f9; }

        .dark .ks-stat-change--positive { color: #4ade80 !important; background-color: rgba(22,128,74,0.25) !important; }
        .dark .ks-stat-change--negative { color: #f87171 !important; background-color: rgba(185,28,28,0.25) !important; }
        .dark .ks-stat-change--neutral { color: #cbd5e1 !important; background-color: #262a30 !important; }

        .ks-num { font-variant-numeric: tabular-nums; }
        .ks-sec { background: #fffefa; border: 1px solid #dcdad2; border-radius: 10px; box-shadow: 0 1px 2px rgba(36,31,27,.04); transition: border-color .15s ease, box-shadow .18s ease-out; }
        .dark .ks-sec { background: #131519 !important; border: 1px solid #222630 !important; border-color: #222630 !important; box-shadow: none; }
        .ks-sec-head { padding: .65rem .9rem; border-bottom: 1px solid #dcdad2; display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
        .dark .ks-sec-head { border-bottom: 1px solid #222630 !important; border-color: #222630 !important; }

        .ks-track { background: #ecebe3; border-radius: 999px; overflow: hidden; }
        .dark .ks-track { background: #262a30; }

        /* Dark Hope UI / Mon Stage : lisibilité totale */
        .dark .text-gray-400, .dark .text-gray-500, .dark .text-slate-400 { color: #9aa0a6 !important; }
        .dark h1, .dark h2, .dark h3 { color: #f8fafc !important; }
        .dark .text-dark { color: #f8fafc !important; }
        .dark .text-secondary { color: #94a3b8 !important; }
        .dark .text-muted { color: #94a3b8 !important; }
        .dark .border, .dark [class*="border-[#dcdad2]"], .dark [class*="border-[#ecebe3]"], .dark [class*="border-[#bfbab2]"], .dark [class*="border-[#d8d4cc]"] { border-color: #222630 !important; }
    </style>

    {{-- ════════════════════════════════════════════════════════
         BANNER ADMIN COMPACT (STYLE MON STAGE)
    ════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-gradient-to-r from-[#1c3e96] via-[#1a3889] to-[#162f73] dark:from-[#131519] dark:via-[#161a22] dark:to-[#131519] mb-3 shadow-sm">
        <div class="absolute inset-0 opacity-15 pointer-events-none"
            style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;)">
        </div>
        <div class="relative px-3.5 sm:px-4 py-2.5 sm:py-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
                <div class="min-w-0">
                    <h1 class="text-base sm:text-lg font-bold text-white tracking-tight leading-tight">Tableau de Bord</h1>
                    <p class="text-[11px] sm:text-xs text-blue-200 dark:text-gray-400 mt-0.5">Bienvenue ! Voici l'état de votre plateforme</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                    @role('admin')
                    <a href="{{ route('admin.notifications.urgent.index') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-semibold text-xs rounded-lg shadow-sm border border-white/20 whitespace-nowrap transition-all">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                        </span>
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span>Alertes urgentes</span>
                    </a>
                    @endrole
                    @role('admin|superviseur')
                    @if(($pendingCorrectionsCount ?? 0) > 0)
                    <a href="{{ route('admin.presence.corrections') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold text-xs rounded-lg shadow-sm border border-white/20 whitespace-nowrap transition-all">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                        </span>
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                        </svg>
                        <span>Correction de pointage</span>
                        <span class="inline-flex items-center justify-center min-w-[1.2rem] h-4 px-1 rounded-full bg-white/25 text-[10px] font-bold tabular-nums">{{ $pendingCorrectionsCount }}</span>
                    </a>
                    @endif
                    @endrole
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/10 dark:bg-white/5 border border-white/15 dark:border-[#222630] text-xs font-semibold text-white whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-blue-200 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ now()->locale('fr')->isoFormat('DD MMMM YYYY') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-3 sm:space-y-3.5 overflow-x-hidden">
        {{-- ── KPI ROW 1 (STYLE KS-STAT-CARD MON STAGE) ────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            {{-- Total Stages --}}
            <a href="{{ route('stages.index', ['annee_academique' => 'all']) }}" class="ks-stat-card ks-stat-card--blue border shadow-none block">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="ks-label truncate">Total Stages</span>
                        <span class="ks-stat-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                    </div>
                    <div class="my-0.5 flex items-baseline gap-1">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-xl sm:text-2xl leading-none">{{ $totalStages }}</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-1.5 mt-1.5 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--{{ $evolutionStages30j >= 0 ? 'positive' : 'negative' }}">{{ $evolutionStages30j >= 0 ? '+' : '' }}{{ $evolutionStages30j }}%</span>
                        <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">sur 30j</span>
                    </div>
                </div>
            </a>

            {{-- Stages en cours --}}
            <a href="{{ route('stages.index', ['statut' => 'En cours', 'annee_academique' => 'all']) }}" class="ks-stat-card ks-stat-card--green border shadow-none block">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="ks-label truncate">Stages en cours</span>
                        <span class="ks-stat-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <div class="my-0.5 flex items-baseline gap-1">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-xl sm:text-2xl leading-none">{{ $enCoursGlobal }}</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-1.5 mt-1.5 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--positive">Actifs</span>
                        <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">en ce moment</span>
                    </div>
                </div>
            </a>

            {{-- Stagiaires inscrits --}}
            <a href="{{ route('etudiants.index') }}" class="ks-stat-card ks-stat-card--violet border shadow-none block">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="ks-label truncate">Stagiaires inscrits</span>
                        <span class="ks-stat-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </span>
                    </div>
                    <div class="my-0.5 flex items-baseline gap-1">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-xl sm:text-2xl leading-none">{{ $totalEtudiants }}</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-1.5 mt-1.5 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--{{ $evolutionEtudiants30j >= 0 ? 'positive' : 'negative' }}">{{ $evolutionEtudiants30j >= 0 ? '+' : '' }}{{ $evolutionEtudiants30j }}%</span>
                        <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">sur 30j</span>
                    </div>
                </div>
            </a>

            {{-- Attestations --}}
            <a href="{{ route('stages.index') }}" class="ks-stat-card ks-stat-card--amber border shadow-none block">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="ks-label truncate">Attestations</span>
                        <span class="ks-stat-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/></svg>
                        </span>
                    </div>
                    <div class="my-0.5 flex items-baseline gap-1">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-xl sm:text-2xl leading-none">{{ $totalAttestations }}</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-1.5 mt-1.5 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--neutral">Délivrées</span>
                        <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">au total</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- ── KPI ROW 2 (STYLE KS-STAT-CARD MON STAGE) ────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            {{-- Stages terminés --}}
            <a href="{{ route('stages.index',['statut'=>'Termine','annee_academique'=>'all']) }}" class="ks-stat-card ks-stat-card--violet border shadow-none block">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="ks-label truncate">Stages terminés</span>
                        <span class="ks-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                    </div>
                    <div class="my-0.5 flex items-baseline gap-1">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-xl sm:text-2xl leading-none">{{ $terminesGlobal }}</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-1.5 mt-1.5 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--positive">Clôturés</span>
                        <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">avec succès</span>
                    </div>
                </div>
            </a>

            {{-- Stages à venir --}}
            <a href="{{ route('stages.index',['statut'=>'A venir','annee_academique'=>'all']) }}" class="ks-stat-card ks-stat-card--amber border shadow-none block">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="ks-label truncate">Stages à venir</span>
                        <span class="ks-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                    </div>
                    <div class="my-0.5 flex items-baseline gap-1">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-xl sm:text-2xl leading-none">{{ $inscritsGlobal }}</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-1.5 mt-1.5 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--neutral">Inscrits</span>
                        <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">en attente</span>
                    </div>
                </div>
            </a>

            {{-- Durée moyenne --}}
            <div class="ks-stat-card ks-stat-card--cyan border shadow-none">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="ks-label truncate">Durée moyenne</span>
                        <span class="ks-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                    </div>
                    <div class="my-0.5 flex items-baseline gap-1">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-xl sm:text-2xl leading-none">{{ $dureeMoyenne }}</span>
                        <span class="text-xs font-semibold text-[#6e6862] dark:text-[#a89f93]">jours</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-1.5 mt-1.5 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--neutral">Moyenne</span>
                        <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">par stage</span>
                    </div>
                </div>
            </div>

            {{-- Sans stage --}}
            <a href="{{ route('etudiants.index',['stage_status'=>'none']) }}" class="ks-stat-card ks-stat-card--red border shadow-none block">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="ks-label truncate">Sans stage</span>
                        <span class="ks-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></span>
                    </div>
                    <div class="my-0.5 flex items-baseline gap-1">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-xl sm:text-2xl leading-none">{{ $etudiantsSansStage }}</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-1.5 mt-1.5 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--negative">À affecter</span>
                        <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">non placés</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- ── SUIVI DES POINTAGES (STYLE MON STAGE) ──────────── --}}
        @can('presence.view')
        <div class="ks-sec">
            <div class="ks-sec-head">
                <div class="flex items-center gap-2">
                    <span class="ks-stat-icon !w-7 !h-7 bg-[#ecfdf5] text-[#15803d] dark:bg-emerald-950/60 dark:text-emerald-400">
                        <svg class="!w-4 !h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </span>
                    <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Suivi des Pointages</h3>
                </div>
                <a href="{{ route('attendance.tracking.index') }}" class="ks-btn-primary ks-btn-xs">
                    Voir le détail →
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-2.5 p-3 sm:p-3.5">
                @foreach([
                    [$todayAttendance, "Aujourd'hui", 'pointages', 'text-[#15803d] dark:text-emerald-400'],
                    [$todayPresent, 'Présents', "aujourd'hui", 'text-[#1c3e96] dark:text-blue-400'],
                    [$todayLate, 'En retard', "aujourd'hui", 'text-[#b45309] dark:text-amber-400'],
                    [$weekLateMinutes, 'Retard cumulé', 'min cette semaine', 'text-[#b91c1c] dark:text-rose-400'],
                ] as [$data, $title, $subtitle, $textColor])
                <div class="rounded-md border border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] p-2.5 sm:p-3 flex flex-col justify-between">
                    <p class="ks-label !text-[10px]">{{ $title }}</p>
                    <p class="text-lg sm:text-xl font-bold {{ $textColor }} ks-num my-1">{{ $data }}</p>
                    <p class="text-[10px] sm:text-[11px] text-[#6e6862] dark:text-[#a89f93]">{{ $subtitle }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endcan

        {{-- ── DEMANDES DE PERMISSION (STYLE MON STAGE) ────────── --}}
        @role('admin|superviseur')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
            @foreach([
                ['Demandes en attente', $permissionsPending, 'amber', 'Urgent à traiter', route('admin.permissions.index',['status'=>'pending']), 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Approuvées', $permissionsApproved, 'green', 'Validées', route('admin.permissions.index',['status'=>'approved']), 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Refusées', $permissionsRejected, 'red', 'Rejetées', route('admin.permissions.index',['status'=>'rejected']), 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ] as [$lbl, $val, $col, $sub, $link, $icon])
            <a href="{{ $link }}" class="ks-stat-card ks-stat-card--{{ $col }} border shadow-none block group">
                <div class="p-3 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-1">
                        <span class="ks-label truncate">{{ $lbl }}</span>
                        <span class="ks-stat-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                        </span>
                    </div>
                    <div class="my-0.5 flex items-baseline gap-1">
                        <span class="font-bold text-[#241f1b] dark:text-white ks-num text-xl sm:text-2xl leading-none">{{ $val }}</span>
                    </div>
                    <div class="ks-stat-footer flex items-center justify-between gap-1.5 mt-1.5 pt-1">
                        <span class="badge ks-stat-change ks-stat-change--{{ $col === 'green' ? 'positive' : ($col === 'red' ? 'negative' : 'neutral') }}">{{ $sub }}</span>
                        <span class="text-xs font-semibold text-[#1c3e96] dark:text-blue-400 group-hover:translate-x-0.5 transition-transform inline-flex items-center gap-0.5">Voir →</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endrole

        {{-- ── SUIVI DES TÂCHES (STYLE MON STAGE) ─────────────── --}}
        @role('admin|superviseur')
        <div class="ks-sec">
            <div class="ks-sec-head">
                <div class="flex items-center gap-2">
                    <span class="ks-stat-icon !w-7 !h-7 bg-[#faf5ff] text-[#7e22ce] dark:bg-purple-950/60 dark:text-purple-400">
                        <svg class="!w-4 !h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </span>
                    <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Suivi des Tâches</h3>
                </div>
                <a href="{{ route('tasks.index') }}" class="ks-btn-primary ks-btn-xs">
                    Voir les tâches →
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-2.5 p-3 sm:p-3.5">
                @foreach([
                    ['Créées', $tasksCreated, 'bg-[#faf5ff] dark:bg-purple-950/40 text-[#7e22ce] dark:text-purple-300'],
                    ['En cours', $tasksInProgress, 'bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300'],
                    ['Terminées', $tasksCompleted, 'bg-[#ecfdf5] dark:bg-emerald-950/40 text-[#15803d] dark:text-emerald-300'],
                    ['En attente val.', $tasksAwaiting, 'bg-[#fffbeb] dark:bg-amber-950/40 text-[#b45309] dark:text-amber-300'],
                ] as [$lbl, $val, $badgeCol])
                <div class="rounded-md border border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] p-2.5 sm:p-3 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-1">
                        <span class="ks-label !text-[10px]">{{ $lbl }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $badgeCol }}">{{ $lbl }}</span>
                    </div>
                    <p class="text-lg sm:text-xl font-bold text-[#241f1b] dark:text-white ks-num mt-1">{{ $val }}</p>
                </div>
                @endforeach
            </div>
            <div class="px-3 sm:px-4 pb-3" style="position:relative;height:185px">
                <canvas id="chart-tasks"></canvas>
            </div>
            <div class="px-4 pb-2.5 text-center">
                <p class="inline-flex items-center gap-1.5 text-[10.5px] text-[#6e6862] dark:text-[#a89f93]">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                    </svg>
                    Cliquez sur un point de la courbe pour voir le détail des tâches du mois.
                </p>
            </div>
        </div>
        @endrole

        {{-- ── CERCLES STATISTIQUES UTILISATEURS (STYLE MON STAGE) ────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5 sm:gap-3">
            {{-- Cercle 1 : Répartition des Utilisateurs --}}
            <a href="{{ url('/admin/stages?per_page=10') }}" class="ks-sec block group hover:shadow-md transition">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon !w-7 !h-7 bg-[#eef1fb] text-[#1c3e96] dark:bg-indigo-950/60 dark:text-indigo-400">
                            <svg class="!w-4 !h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Répartition des Utilisateurs</h3>
                    </div>
                    <span class="ks-btn-secondary ks-btn-xs group-hover:border-[#1c3e96] group-hover:text-[#1c3e96]">
                        Voir les stages →
                    </span>
                </div>
                <div class="p-3 sm:p-3.5" style="position:relative;height:185px">
                    <canvas id="chart-users-roles"></canvas>
                </div>
            </a>

            {{-- Cercle 2 : Statut des Comptes --}}
            <div class="ks-sec">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon !w-7 !h-7 bg-[#ecfdf5] text-[#15803d] dark:bg-emerald-950/60 dark:text-emerald-400">
                            <svg class="!w-4 !h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Statut des Comptes</h3>
                    </div>
                </div>
                <div class="p-3 sm:p-3.5" style="position:relative;height:185px">
                    <canvas id="chart-users-status"></canvas>
                </div>
            </div>
        </div>

        {{-- ── CHARTS ROW 1 (STYLE MON STAGE) ─────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-2.5 sm:gap-3">
            {{-- Évolution inscriptions --}}
            <div class="lg:col-span-2 ks-sec">
                <div class="ks-sec-head flex-col xs:flex-row xs:items-center">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon !w-7 !h-7 bg-[#eef1fb] text-[#1c3e96] dark:bg-indigo-950/60 dark:text-indigo-400">
                            <svg class="!w-4 !h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Évolution des inscriptions</h3>
                    </div>
                    <div class="flex gap-1 bg-[#faf9f6] dark:bg-[#181c24] p-0.5 rounded-md border border-[#ecebe3] dark:border-[#222630] self-start xs:self-auto">
                        <button id="btn-jour" onclick="switchPeriod('jour')"
                            class="px-2 sm:px-2.5 py-1 text-[11px] font-semibold rounded-[4px] bg-[#1c3e96] text-white transition-all">Jour</button>
                        <button id="btn-semaine" onclick="switchPeriod('semaine')"
                            class="px-2 sm:px-2.5 py-1 text-[11px] font-semibold rounded-[4px] text-[#6e6862] dark:text-[#a89f93] hover:text-[#241f1b] dark:hover:text-white transition-all">Sem.</button>
                        <button id="btn-mois" onclick="switchPeriod('mois')"
                            class="px-2 sm:px-2.5 py-1 text-[11px] font-semibold rounded-[4px] text-[#6e6862] dark:text-[#a89f93] hover:text-[#241f1b] dark:hover:text-white transition-all">Mois</button>
                    </div>
                </div>
                <div class="p-3 sm:p-3.5" style="position:relative;height:185px">
                    <canvas id="chart-inscriptions"></canvas>
                </div>
                <div class="px-4 pb-2 text-center">
                    <p class="inline-flex items-center gap-1.5 text-[10.5px] text-[#6e6862] dark:text-[#a89f93]">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                        </svg>
                        Cliquez sur un point de la courbe pour voir le détail des inscriptions de la période.
                    </p>
                </div>
            </div>

            {{-- Par type --}}
            <div class="ks-sec">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon !w-7 !h-7 bg-[#faf5ff] text-[#7e22ce] dark:bg-purple-950/60 dark:text-purple-400">
                            <svg class="!w-4 !h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Par type de stage</h3>
                    </div>
                </div>
                <div class="p-3 sm:p-3.5" style="position:relative;height:185px">
                    <canvas id="chart-types"></canvas>
                </div>
            </div>
        </div>

        {{-- ── RÉPARTITION PAR DOMAINE (STYLE MON STAGE) ───────── --}}
        @php
            $repartitionDomaines = $domainesStats->sortByDesc('total')->values();
            $nbDomainesRep       = $repartitionDomaines->count();
            $totalStagesRep      = (int) $repartitionDomaines->sum('total');
            $sEnCoursRep         = (int) $repartitionDomaines->sum('enCours');
            $sTerminesRep        = (int) $repartitionDomaines->sum('termines');
            $sInscritsRep        = (int) $repartitionDomaines->sum('inscrits');
            $paletteRep          = ['#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#6366f1', '#14b8a6', '#f97316', '#ef4444', '#a855f7', '#22d3ee'];
        @endphp
        <div class="ks-sec">
            <div class="ks-sec-head">
                <div class="flex items-center gap-2">
                    <span class="ks-stat-icon !w-7 !h-7 bg-[#ecfeff] text-[#0891b2] dark:bg-cyan-950/60 dark:text-cyan-400">
                        <svg class="!w-4 !h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </span>
                    <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Répartition par domaine</h3>
                </div>
                <span class="badge ks-stat-change ks-stat-change--neutral">
                    {{ $nbDomainesRep }} domaine{{ $nbDomainesRep > 1 ? 's' : '' }}
                </span>
            </div>
            <div class="p-3 sm:p-3.5 grid grid-cols-1 lg:grid-cols-5 gap-3.5 sm:gap-4">
                {{-- Donut total --}}
                <div class="lg:col-span-3 min-w-0">
                    <div class="flex items-center justify-between gap-2.5 mb-2.5">
                        <p class="text-xs font-semibold text-[#241f1b] dark:text-white min-w-0 truncate">Nombre total de stages par domaine</p>
                        @if($nbDomainesRep)
                        <span class="badge ks-stat-change ks-stat-change--neutral">{{ $totalStagesRep }} stage{{ $totalStagesRep > 1 ? 's' : '' }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-3.5 sm:gap-4">
                        <div class="relative w-full max-w-[10rem] aspect-square shrink-0">
                            <canvas id="chart-domaines"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-xl sm:text-2xl font-bold text-[#241f1b] dark:text-white ks-num">{{ $totalStagesRep }}</span>
                                <span class="text-[9px] font-semibold uppercase tracking-wide text-[#6e6862] dark:text-[#a89f93]">stages</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 w-full space-y-1.5">
                            @forelse($repartitionDomaines as $idxPie => $domPie)
                            @php
                                $colorPie = $paletteRep[$idxPie % count($paletteRep)];
                                $pctPie   = $totalStagesRep ? round($domPie['total'] / $totalStagesRep * 100, 1) : 0;
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $colorPie }}"></span>
                                <span class="flex-1 min-w-0 text-xs font-medium text-[#241f1b] dark:text-gray-200 truncate" title="{{ $domPie['domaine'] }}">{{ $domPie['domaine'] }}</span>
                                <span class="text-[11px] font-bold text-[#6e6862] dark:text-[#a89f93] flex-shrink-0 tabular-nums">{{ $pctPie }}%</span>
                                <span class="w-5 text-right text-xs font-bold text-[#241f1b] dark:text-gray-200 flex-shrink-0 tabular-nums">{{ $domPie['total'] }}</span>
                            </div>
                            @empty
                            <p class="text-center text-[#6e6862] text-xs py-4">Aucun domaine enregistré</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Détail par domaine --}}
                <div class="lg:col-span-2 min-w-0">
                    <p class="ks-label !text-[10px] mb-2">Détail par domaine</p>
                    <div class="space-y-2">
                        @forelse($repartitionDomaines as $idxRep => $domRep)
                        @php
                            $totRep = $domRep['total'] ?: 1;
                            $rings  = [
                                ['En cours', $domRep['enCours'],  round($domRep['enCours']  / $totRep * 100), 'stroke-emerald-500', 'text-emerald-600 dark:text-emerald-400'],
                                ['Terminés', $domRep['termines'], round($domRep['termines'] / $totRep * 100), 'stroke-violet-500',  'text-violet-600 dark:text-violet-400'],
                                ['À venir',  $domRep['inscrits'], round($domRep['inscrits'] / $totRep * 100), 'stroke-orange-500',  'text-orange-600 dark:text-orange-400'],
                            ];
                        @endphp
                        <div class="rounded-md border border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] px-2.5 py-2 transition hover:border-[#1c3e96]">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded flex items-center justify-center text-[10.5px] font-bold flex-shrink-0 bg-[#eef1fb] dark:bg-indigo-950/60 text-[#1c3e96] dark:text-indigo-400">
                                    {{ $idxRep + 1 }}
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block text-xs font-semibold text-[#241f1b] dark:text-white truncate" title="{{ $domRep['domaine'] }}">{{ $domRep['domaine'] }}</span>
                                </span>
                                <span class="text-xs font-bold text-[#241f1b] dark:text-white flex-shrink-0 tabular-nums">{{ $domRep['total'] }}</span>
                            </div>

                            <div class="mt-2 ml-7 flex items-start justify-between gap-1.5">
                                @foreach($rings as [$ringLbl, $ringCnt, $ringPct, $ringBar, $ringTxt])
                                <div class="flex flex-col items-center gap-0.5 min-w-0">
                                    <div class="relative w-6 h-6 flex-shrink-0">
                                        <svg viewBox="0 0 36 36" class="w-6 h-6 -rotate-90">
                                            <circle cx="18" cy="18" r="15.915" fill="none" class="stroke-[#ecebe3] dark:stroke-[#222630]" stroke-width="3.5"></circle>
                                            <circle cx="18" cy="18" r="15.915" fill="none" class="{{ $ringBar }}" stroke-width="3.5"
                                                stroke-linecap="round" stroke-dasharray="{{ $ringPct }} 100"></circle>
                                        </svg>
                                        <span class="absolute inset-0 flex items-center justify-center text-[8px] font-bold {{ $ringTxt }}">{{ $ringPct }}%</span>
                                    </div>
                                    <span class="text-[8px] font-medium text-[#6e6862] dark:text-[#a89f93] leading-none">{{ $ringLbl }}</span>
                                    <span class="text-[8.5px] font-bold text-[#241f1b] dark:text-gray-200 leading-none -mt-0.5 tabular-nums">{{ $ringCnt }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-[#6e6862] text-xs py-4">Aucun domaine enregistré</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($nbDomainesRep)
            <div class="px-3.5 sm:px-4 py-2 border-t border-[#dcdad2] dark:border-[#222630] flex flex-wrap items-center gap-x-3.5 gap-y-1 text-[11px] text-[#6e6862] dark:text-[#a89f93]">
                <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $sEnCoursRep }} en cours</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span>{{ $sTerminesRep }} terminés</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>{{ $sInscritsRep }} à venir</span>
                <span class="ml-auto inline-flex items-center gap-1.5 font-semibold text-[#241f1b] dark:text-white"><span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>{{ $totalStagesRep }} au total</span>
            </div>
            @endif
        </div>

        {{-- ── ACTIVITÉS + INDICATEURS (STYLE MON STAGE) ───────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5 sm:gap-3">
            {{-- Activités récentes --}}
            <div class="ks-sec">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon !w-7 !h-7 bg-[#fffbeb] text-[#b45309] dark:bg-amber-950/60 dark:text-amber-400">
                            <svg class="!w-4 !h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Activités récentes</h3>
                    </div>
                </div>
                <div class="divide-y divide-[#ecebe3] dark:divide-[#222630] max-h-56 sm:max-h-60 overflow-y-auto">
                    @forelse($dernieresActivites as $activity)
                    <div class="px-3 sm:px-3.5 py-2 hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition">
                        <div class="flex items-start gap-2.5">
                            <div class="h-6 w-6 rounded-md bg-[#eef1fb] dark:bg-indigo-950/60 text-[#1c3e96] dark:text-indigo-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[#241f1b] dark:text-white line-clamp-1">{{ $activity->action }}</p>
                                <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93] mt-0.5 line-clamp-1">{{ $activity->description }}</p>
                            </div>
                            <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93] whitespace-nowrap flex-shrink-0 ml-1">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="px-4 py-6 text-center text-[#6e6862] text-xs">Aucune activité récente</div>
                    @endforelse
                </div>
            </div>

            {{-- Indicateurs clés --}}
            <div class="ks-sec">
                <div class="ks-sec-head">
                    <div class="flex items-center gap-2">
                        <span class="ks-stat-icon !w-7 !h-7 bg-[#fff1f2] text-[#b91c1c] dark:bg-rose-950/60 dark:text-rose-400">
                            <svg class="!w-4 !h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </span>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Indicateurs clés</h3>
                    </div>
                </div>
                <div class="p-3 sm:p-3.5 space-y-2.5">
                    @foreach([
                        ['Taux de réussite', $tauxReussite, 'bg-[#15803d]', 'text-[#15803d] dark:text-emerald-400'],
                        ['Stagiaires actifs', $tauxEtudiantsActifs, 'bg-[#1c3e96]', 'text-[#1c3e96] dark:text-blue-400'],
                        ['Conversion', $tauxConversion, 'bg-[#7e22ce]', 'text-[#7e22ce] dark:text-purple-400']
                    ] as [$lbl, $val, $barBg, $cls])
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-xs font-semibold text-[#241f1b] dark:text-white">{{ $lbl }}</span>
                            <span class="text-xs font-bold {{ $cls }} tabular-nums">{{ $val }}%</span>
                        </div>
                        <div class="ks-track h-1.5 w-full">
                            <div class="{{ $barBg }} h-1.5 rounded-full transition-all duration-700" style="width:{{ $val }}%"></div>
                        </div>
                    </div>
                    @endforeach
                    <div class="pt-2 border-t border-[#ecebe3] dark:border-[#222630] grid grid-cols-2 gap-2 text-center">
                        <div class="rounded-md border border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] p-2">
                            <p class="text-base sm:text-lg font-bold text-[#15803d] dark:text-emerald-400 tabular-nums">{{ $evolutionInscriptionsMois >= 0 ? '+' : '' }}{{ $evolutionInscriptionsMois }}%</p>
                            <p class="text-[10px] text-[#6e6862] dark:text-[#a89f93] mt-0.5">vs mois dernier</p>
                        </div>
                        <div class="rounded-md border border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] p-2">
                            <p class="text-base sm:text-lg font-bold text-[#1c3e96] dark:text-blue-400 tabular-nums">{{ $evolutionStages >= 0 ? '+' : '' }}{{ $evolutionStages }}%</p>
                            <p class="text-[10px] text-[#6e6862] dark:text-[#a89f93] mt-0.5">stages actifs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CORBEILLE (STYLE MON STAGE) ────────────────────── --}}
        <div class="ks-sec">
            <div class="ks-sec-head">
                <div class="flex items-center gap-2">
                    <span class="ks-stat-icon !w-7 !h-7 bg-[#fff1f2] text-[#b91c1c] dark:bg-rose-950/60 dark:text-rose-400">
                        <svg class="!w-4 !h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </span>
                    <h3 class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-white">Corbeille</h3>
                </div>
                <span class="badge ks-stat-change ks-stat-change--negative">{{ $totalTrash }} éléments</span>
            </div>
            <div class="p-3 sm:p-3.5">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-2.5">
                    @foreach([
                        ['Stages', $stagesTrash->count(), 'text-[#7e22ce] dark:text-purple-400', route('stages.trash')],
                        ['Stagiaires', $etudiantsTrash->count(), 'text-[#1c3e96] dark:text-blue-400', route('etudiants.trash')],
                        ['Badges', $badgesTrash->count(), 'text-[#b45309] dark:text-amber-400', route('stages.trash')],
                        ['Total', $totalTrash, 'text-[#b91c1c] dark:text-rose-400', route('stages.trash')],
                    ] as [$lbl, $cnt, $cls, $link])
                    <a href="{{ $link }}" class="rounded-md border border-[#ecebe3] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] p-2 sm:p-2.5 text-center hover:border-[#1c3e96] transition block">
                        <p class="text-base sm:text-lg font-bold {{ $cls }} ks-num">{{ $cnt }}</p>
                        <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93] mt-0.5">{{ $lbl }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

    </div>{{-- /space-y-3.5 --}}

    {{-- ════════ DONNÉES PHP → JS ════════ --}}
    <script>
        window.__DASHBOARD__ = {
            labelsJour:      {!! Js::from($labelsJour) !!},
            evolutionJour:   {!! Js::from($evolutionJour) !!},
            labelsSemaine:   {!! Js::from($labelsSemaine) !!},
            evolutionSemaine:{!! Js::from($evolutionSemaine) !!},
            labelsMois:      {!! Js::from($labelsMois) !!},
            evolutionMois:   {!! Js::from($evolutionMois) !!},
            rangesJour:      {!! Js::from($rangesJour) !!},
            rangesSemaine:   {!! Js::from($rangesSemaine) !!},
            rangesMois:      {!! Js::from($rangesMois) !!},
            typesLabels:     {!! Js::from($typesLabels) !!},
            typesData:       {!! Js::from($typesData) !!},
            stagesMoisLabels:{!! Js::from($labelsMoisAnnee) !!},
            stagesMoisData:  {!! Js::from($stagesParMois) !!},
            domLabels:       {!! Js::from($repartitionDomaines->pluck('domaine')->values()) !!},
            domTotaux:       {!! Js::from($repartitionDomaines->pluck('total')->values()) !!},
            domPalette:      {!! Js::from($paletteRep) !!},
            tasksMoisLabels: {!! Js::from($tasksMoisLabels) !!},
            tasksCreated:    {!! Js::from($tasksCreatedByMonth) !!},
            tasksInProgress: {!! Js::from($tasksInProgressByMonth) !!},
            tasksCompleted:  {!! Js::from($tasksCompletedByMonth) !!},
            tasksRanges:     {!! Js::from($rangesMois) !!},
        };
    </script>

    {{-- ════════ MODALE : DÉTAIL DES INSCRIPTIONS (courbe cliquable) ════════ --}}
    <div id="registrations-modal" x-data="registrationsApp()" x-show="open" x-cloak
        class="fixed inset-0 z-[10000] flex items-start justify-center p-3 sm:p-6 overflow-y-auto">
        <div x-show="open" x-transition.opacity @click="open=false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div x-show="open" x-transition.duration.200ms class="relative bg-[#fffefa] dark:bg-[#131519] rounded-xl shadow-2xl w-full max-w-6xl z-10 my-4 border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between gap-3 px-5 sm:px-7 py-4 border-b border-[#dcdad2] dark:border-[#222630]">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white truncate">
                            Inscriptions — <span x-text="periodLabel"></span>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <span x-text="total"></span> stagiaire(s) inscrit(s) du
                            <span x-text="fromLabel"></span> au <span x-text="toLabel"></span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button @click="exportCsv()" :disabled="csvLoading"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition disabled:opacity-50">
                        <svg x-show="!csvLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <svg x-show="csvLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Export CSV
                    </button>
                    <button @click="open=false" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-5 sm:px-7 py-4" @keydown.escape.window="open=false">
                <div x-show="loading" class="py-16 text-center">
                    <div class="w-10 h-10 mx-auto rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 font-medium">Chargement des inscriptions…</p>
                </div>

                <div x-show="!loading && error" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Impossible de charger les données.</p>
                    <button @click="loadPage(page)" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
                        Réessayer
                    </button>
                </div>

                <div x-show="!loading && !error && rows.length === 0" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Aucune inscription sur cette période.</p>
                </div>

                <div x-show="!loading && !error && rows.length > 0" class="overflow-x-auto max-h-[55vh] overflow-y-auto rounded-xl border border-[#dcdad2] dark:border-[#222630]">
                    <table class="w-full text-sm min-w-[860px]">
                        <thead class="bg-[#faf9f6] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630] sticky top-0">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Identité</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">École</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Compte</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Inscription</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stages & Domaines</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            <template x-for="r in rows" :key="r.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-5 py-3">
                                        <div class="font-semibold text-gray-900 dark:text-white" x-text="r.full_name"></div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            <span x-show="r.genre" x-text="r.genre" class="inline-block px-1.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300 uppercase"></span>
                                            <span x-show="r.niveau" x-text="'Niv : ' + r.niveau" class="ml-1 text-gray-500 dark:text-gray-400"></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="text-gray-900 dark:text-white" x-text="r.email || '—'"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="r.telephone || '—'"></div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-700 dark:text-gray-300" x-text="r.ecole || '—'"></td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                                            :class="accountBadgeClass(r)">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="accountDotClass(r)"></span>
                                            <span x-text="r.account?.label || '—'"></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300" x-text="r.created_at || '—'"></td>
                                    <td class="px-5 py-3">
                                        <template x-if="r.stages && r.stages.length">
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="(s, si) in r.stages" :key="s.theme + si">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300"
                                                        :title="`${s.theme} — ${s.domaine || 'Sans domaine'} (${s.statut || ''})`">
                                                        <span x-text="s.theme"></span>
                                                        <span x-show="s.domaine" class="text-violet-400" x-text="'· ' + s.domaine"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                        <span x-show="!r.stages || !r.stages.length" class="text-xs text-gray-400 italic">Aucun stage</span>
                                    </td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">
                                        <a :href="routeShow(r)" class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Voir la fiche">
                                            <svg class="w-4.5 h-4.5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div x-show="!loading && !error && rows.length > 0" class="mt-4 flex items-center justify-between gap-3 flex-wrap">
                    <p class="text-xs text-gray-500 dark:text-gray-400"
                        x-text="`Page ${page} / ${lastPage} — ${total} stagiaire(s) au total`"></p>
                    <div class="flex gap-2">
                        <button @click="loadPage(page - 1)" :disabled="page <= 1"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            ‹ Précédent
                        </button>
                        <button @click="loadPage(page + 1)" :disabled="page >= lastPage"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Suivant ›
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════ MODALE : DÉTAIL DES TÂCHES (courbe cliquable) ════════ --}}
    <div id="tasks-modal" x-data="tasksApp()" x-show="open" x-cloak
        class="fixed inset-0 z-[10000] flex items-start justify-center p-3 sm:p-6 overflow-y-auto">
        <div x-show="open" x-transition.opacity @click="open=false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div x-show="open" x-transition.duration.200ms class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-7xl z-10 my-4 border border-[#dcdad2] dark:border-[#222630] overflow-hidden">
            {{-- Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-[#1c3e96] via-[#1a3889] to-[#162f73] dark:from-[#131519] dark:via-[#161a22] dark:to-[#131519] border-b border-[#dcdad2] dark:border-[#222630]">
                <div class="absolute inset-0 opacity-20"
                    style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;)">
                </div>
                <div class="relative px-5 sm:px-7 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm flex items-center justify-center text-white flex-shrink-0 border border-white/20 shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-white truncate flex items-center gap-2">
                                Tâches <span x-text="typeLabel()"></span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white border border-white/20" x-text="periodLabel"></span>
                            </h3>
                            <p class="text-xs text-violet-200 mt-0.5">
                                <span x-text="total"></span> tâche(s) · du
                                <span x-text="fromLabel"></span> au <span x-text="toLabel"></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0 self-start sm:self-auto">
                        <a :href="trackingUrl()" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/20 backdrop-blur-sm transition whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Suivi des tâches
                        </a>
                        <button @click="open=false" class="w-10 h-10 rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/20 backdrop-blur-sm transition flex items-center justify-center" title="Fermer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-4 sm:px-6 py-4" @keydown.escape.window="open=false">
                <div x-show="loading" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-violet-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 font-medium">Chargement des tâches…</p>
                </div>

                <div x-show="!loading && error" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Impossible de charger les données.</p>
                    <button @click="loadPage(page)" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-violet-600 text-white hover:bg-violet-700 transition">
                        Réessayer
                    </button>
                </div>

                <div x-show="!loading && !error && rows.length === 0" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Aucune tâche sur cette période.</p>
                    <p class="mt-1 text-xs text-gray-400">Essayez une autre période en cliquant sur la courbe.</p>
                </div>

                <template x-if="!loading && !error && rows.length > 0">
                    <div>
                        {{-- Search --}}
                        <div class="mb-4 relative">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m1.7-4.3a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                            </svg>
                            <input type="text" x-model="q" placeholder="Rechercher une tâche, un propriétaire, un stage…"
                                class="w-full h-10 pl-10 pr-9 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500 transition">
                            <button x-show="q.length > 0" @click="q = ''"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div x-show="filteredRows().length === 0" class="py-12 text-center">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center">
                                <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m1.7-4.3a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Aucun résultat</p>
                            <p class="mt-1 text-xs text-gray-400">Aucune tâche ne correspond à « <span class="font-medium" x-text="q"></span> ».</p>
                        </div>

                        {{-- Cards grid --}}
                        <div x-show="filteredRows().length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5 max-h-[55vh] overflow-y-auto pr-1">
                            <template x-for="r in filteredRows()" :key="r.id">
                                <a :href="r.url"
                                    class="group relative block bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-0.5 hover:border-violet-300 dark:hover:border-violet-600 transition-all duration-300">
                                    {{-- Status color bar --}}
                                    <div class="absolute left-0 top-0 bottom-0 w-1" :class="statusBarClass(r)"></div>

                                    <div class="p-4 pl-5">
                                        {{-- Top row : priority dot + status + arrow --}}
                                        <div class="flex items-center justify-between gap-2 mb-2.5">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-2 h-2 rounded-full flex-shrink-0" :class="priorityDotClass(r)"></span>
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium flex items-center gap-1.5"
                                                    :class="statusBadgeClass(r)">
                                                    <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(r)"></span>
                                                    <span x-text="r.status_label"></span>
                                                </span>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-violet-500 group-hover:translate-x-0.5 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>

                                        {{-- Title + owner --}}
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 leading-snug mb-2.5 group-hover:text-violet-700 dark:group-hover:text-violet-300 transition">
                                            <span x-text="r.title"></span>
                                        </p>
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0" :style="`background:${avatarColor(r.owner || '—')}`" x-text="initials(r.owner)"></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="r.owner || '—'"></span>
                                        </div>

                                        {{-- Meta : stage + date --}}
                                        <div class="flex flex-wrap items-center gap-2 mb-3">
                                            <span x-show="r.stage_theme" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300 truncate max-w-[60%]"
                                                :title="`${r.stage_theme}${r.etudiant ? ' — ' + r.etudiant : ''}`">
                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                <span x-text="r.stage_theme" class="truncate"></span>
                                            </span>
                                            <span x-show="!r.stage_theme" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                                Sans stage
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-[11px] text-gray-400 ml-auto">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span x-text="r.created_at || '—'"></span>
                                            </span>
                                        </div>

                                        {{-- Progress --}}
                                        <div class="flex items-center gap-2 mb-3">
                                            <div class="flex-1 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-500" :class="progressBarClass(r)" :style="`width:${Math.min(100,(r.progress || 0))}%`"></div>
                                            </div>
                                            <span class="text-[11px] font-bold text-gray-600 dark:text-gray-300 font-mono" x-text="(r.progress || 0) + '%'"></span>
                                        </div>

                                        {{-- Assignees --}}
                                        <template x-if="r.assignees && r.assignees.length">
                                            <div class="flex items-center pt-3 border-t border-gray-100 dark:border-gray-700/60">
                                                <div class="flex -space-x-2">
                                                    <template x-for="(a, ai) in r.assignees.slice(0, 3)" :key="a + ai">
                                                        <span class="w-6 h-6 rounded-full ring-2 ring-white dark:ring-gray-800 flex items-center justify-center text-[9px] font-bold text-white flex-shrink-0"
                                                            :style="`background:${avatarColor(a)}`" :title="a" x-text="initials(a)"></span>
                                                    </template>
                                                    <span x-show="r.assignees.length > 3"
                                                        class="w-6 h-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-200 flex items-center justify-center text-[9px] font-bold flex-shrink-0"
                                                        x-text="'+' + (r.assignees.length - 3)"></span>
                                                </div>
                                                <div class="ml-2 flex flex-wrap gap-1 items-center w-full">
                                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 truncate" x-text="r.assignees.slice(0, 3).map((n, i) => (i === 0 ? n : ' · ' + n)).join('') + (r.assignees.length > 3 ? '…' : '')"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </a>
                            </template>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4 flex items-center justify-between gap-3 flex-wrap">
                            <p class="text-xs text-gray-500 dark:text-gray-400"
                                x-text="`Page ${page} / ${lastPage} — ${total} tâche(s) au total`"></p>
                            <div class="flex gap-2">
                                <button @click="loadPage(page - 1)" :disabled="page <= 1"
                                    class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    Précédent
                                </button>
                                <button @click="loadPage(page + 1)" :disabled="page >= lastPage"
                                    class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    Suivant
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function registrationsApp() {
            return {
                open: false,
                loading: false,
                error: false,
                rows: [],
                total: 0,
                from: null,
                to: null,
                fromLabel: '',
                toLabel: '',
                periodLabel: '',
                page: 1,
                lastPage: 1,
                perPage: 10,
                csvLoading: false,

                init() {},

                async openDetail(from, to, label) {
                    this.from = from;
                    this.to = to;
                    this.periodLabel = label || '';
                    this.page = 1;
                    this.rows = [];
                    this.total = 0;
                    this.lastPage = 1;
                    this.error = false;
                    this.open = true;
                    await this.loadPage(1);
                },

                async loadPage(page) {
                    if (page < 1) return;
                    this.loading = true;
                    this.error = false;
                    try {
                        const url = `/admin/dashboard/inscriptions/details?from=${encodeURIComponent(this.from)}&to=${encodeURIComponent(this.to)}&page=${page}&per_page=${this.perPage}`;
                        const resp = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        if (!resp.ok) throw new Error('HTTP ' + resp.status);
                        const data = await resp.json();
                        this.rows = data.data || [];
                        this.total = data.total || 0;
                        this.page = data.pagination?.current_page || page;
                        this.lastPage = data.pagination?.last_page || 1;
                        this.setLabels(data.from, data.to);
                    } catch (e) {
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                },

                setLabels(from, to) {
                    const fmt = (iso) => {
                        if (!iso) return '';
                        const [y, m, d] = iso.split('-');
                        return `${d}/${m}/${y}`;
                    };
                    this.fromLabel = fmt(from);
                    this.toLabel = fmt(to);
                },

                accountBadgeClass(r) {
                    const map = {
                        active: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                        pending: 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                        none: 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                    };
                    return map[r.account?.status] || map.none;
                },

                accountDotClass(r) {
                    const map = { active: 'bg-emerald-500', pending: 'bg-amber-500', none: 'bg-gray-400' };
                    return map[r.account?.status] || map.none;
                },

                routeShow(r) {
                    return `/admin/etudiants/${r.id}`;
                },

                async exportCsv() {
                    if (this.csvLoading || !this.from || !this.to) return;
                    this.csvLoading = true;
                    try {
                        const url = `/admin/dashboard/inscriptions/details?from=${encodeURIComponent(this.from)}&to=${encodeURIComponent(this.to)}&page=1&per_page=1000`;
                        const resp = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        if (!resp.ok) throw new Error('HTTP ' + resp.status);
                        const data = await resp.json();
                        const rows = data.data || [];
                        const header = ['Identité', 'Email', 'Téléphone', 'Genre', 'École', 'Niveau', 'Inscrit le', 'Compte', 'Stages'];
                        const esc = (v) => '"' + String(v ?? '').replace(/"/g, '""') + '"';
                        const csv = [
                            header.join(';'),
                            ...rows.map(r => [
                                r.full_name,
                                r.email,
                                r.telephone,
                                r.genre,
                                r.ecole,
                                r.niveau,
                                r.created_at,
                                r.account?.label,
                                (r.stages || []).map(s => s.theme + (s.domaine ? ' — ' + s.domaine : '') + (s.statut ? ' (' + s.statut + ')' : '')).join(' | '),
                            ].map(esc).join(';')),
                        ].join('\n');
                        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = `inscriptions_${this.from}_au_${this.to}.csv`;
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        URL.revokeObjectURL(link.href);
                    } catch (e) {
                        this.error = true;
                    } finally {
                        this.csvLoading = false;
                    }
                },
            };
        }

        window.openRegistrationsDetails = function (from, to, label) {
            const el = document.getElementById('registrations-modal');
            if (!el) return;
            let data = null;

            if (window.Alpine && typeof window.Alpine.$data === 'function') {
                try { data = window.Alpine.$data(el); } catch (e) { data = null; }
            }
            if (!data && el.__x && el.__x.$data) {
                data = el.__x.$data;
            }
            if (!data) {
                console.warn('[Registrations] Alpine composant modale non initialisé.');
                return;
            }
            if (typeof data.openDetail === 'function') {
                data.openDetail(from, to, label);
            }
        };
    </script>

    <script>
        function tasksApp() {
            return {
                open: false,
                loading: false,
                error: false,
                rows: [],
                total: 0,
                from: null,
                to: null,
                fromLabel: '',
                toLabel: '',
                periodLabel: '',
                type: 'created',
                page: 1,
                lastPage: 1,
                perPage: 10,
                q: '',

                typeLabel() {
                    const map = {
                        created: 'créées',
                        in_progress: 'en cours',
                        completed: 'terminées',
                    };
                    return map[this.type] || this.type;
                },

                async openDetail(from, to, label, type) {
                    this.from = from;
                    this.to = to;
                    this.periodLabel = label || '';
                    this.type = type || 'created';
                    this.page = 1;
                    this.rows = [];
                    this.total = 0;
                    this.lastPage = 1;
                    this.error = false;
                    this.q = '';
                    this.open = true;
                    await this.loadPage(1);
                },

                async loadPage(page) {
                    if (page < 1) return;
                    this.loading = true;
                    this.error = false;
                    try {
                        const url = `/admin/dashboard/tasks-details?from=${encodeURIComponent(this.from)}&to=${encodeURIComponent(this.to)}&type=${encodeURIComponent(this.type)}&page=${page}&per_page=${this.perPage}`;
                        const resp = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        if (!resp.ok) throw new Error('HTTP ' + resp.status);
                        const data = await resp.json();
                        this.rows = data.data || [];
                        this.total = data.total || 0;
                        this.page = data.pagination?.current_page || page;
                        this.lastPage = data.pagination?.last_page || 1;
                        this.setLabels(data.from, data.to);
                    } catch (e) {
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                },

                setLabels(from, to) {
                    const fmt = (iso) => {
                        if (!iso) return '';
                        const [y, m, d] = iso.split('-');
                        return `${d}/${m}/${y}`;
                    };
                    this.fromLabel = fmt(from);
                    this.toLabel = fmt(to);
                },

                filteredRows() {
                    const q = (this.q || '').trim().toLowerCase();
                    if (!q) return this.rows || [];
                    return (this.rows || []).filter((r) => {
                        const hay = [
                            r.title,
                            r.owner,
                            r.stage_theme,
                            r.etudiant,
                            r.status_label,
                            ...(r.assignees || []),
                        ].filter(Boolean).join(' ').toLowerCase();
                        return hay.includes(q);
                    });
                },

                initials(name) {
                    name = (name || '').trim();
                    if (!name) return '?';
                    const parts = name.split(/\s+/).filter(Boolean);
                    if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
                    return name.slice(0, 2).toUpperCase();
                },

                avatarColor(n) {
                    n = n || '?';
                    let h = 0;
                    for (let i = 0; i < n.length; i++) h = (h * 31 + n.charCodeAt(i)) % 360;
                    return 'hsl(' + h + ' 55% 48%)';
                },

                priorityDotClass(r) {
                    const map = {
                        urgent: 'bg-red-500',
                        high: 'bg-orange-500',
                        normal: 'bg-blue-500',
                        low: 'bg-gray-400',
                    };
                    return map[r.priority] || map.normal;
                },

                statusBarClass(r) {
                    const map = {
                        slate: 'bg-slate-400',
                        blue: 'bg-blue-500',
                        red: 'bg-red-500',
                        amber: 'bg-amber-500',
                        violet: 'bg-violet-500',
                        emerald: 'bg-emerald-500',
                    };
                    return map[r.status_color] || map.slate;
                },

                statusBadgeClass(r) {
                    const map = {
                        slate: 'bg-slate-100 dark:bg-slate-700/40 text-slate-600 dark:text-slate-300',
                        blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                        red: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                        amber: 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                        violet: 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300',
                        emerald: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                    };
                    return map[r.status_color] || map.slate;
                },

                statusDotClass(r) {
                    const map = {
                        slate: 'bg-slate-400',
                        blue: 'bg-blue-500',
                        red: 'bg-red-500',
                        amber: 'bg-amber-500',
                        violet: 'bg-violet-500',
                        emerald: 'bg-emerald-500',
                    };
                    return map[r.status_color] || map.slate;
                },

                progressBarClass(r) {
                    const p = r.progress || 0;
                    if (p >= 100) return 'bg-emerald-500';
                    if (p >= 50) return 'bg-blue-500';
                    return 'bg-amber-500';
                },

                trackingUrl() {
                    return `/admin/tasks-tracking?status=${encodeURIComponent(this.statusForTracking())}`;
                },

                statusForTracking() {
                    const map = {
                        created: '',
                        in_progress: 'in_progress',
                        completed: 'completed',
                    };
                    return map[this.type] || '';
                },
            };
        }

        window.openTasksDetails = function (from, to, label, type) {
            const el = document.getElementById('tasks-modal');
            if (!el) return;
            let data = null;

            if (window.Alpine && typeof window.Alpine.$data === 'function') {
                try { data = window.Alpine.$data(el); } catch (e) { data = null; }
            }
            if (!data && el.__x && el.__x.$data) {
                data = el.__x.$data;
            }
            if (!data) {
                console.warn('[Tasks] Alpine composant modale non initialisé.');
                return;
            }
            if (typeof data.openDetail === 'function') {
                data.openDetail(from, to, label, type);
            }
        };
    </script>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const D   = window.__DASHBOARD__ || {};
        const safe = (arr, fb=[]) => Array.isArray(arr) && arr.length ? arr : fb;

        const isDark  = document.documentElement.classList.contains('dark');
        const TXT     = isDark ? '#e5e7eb' : '#374151';
        const GRID    = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';

        Chart.defaults.font.family = "'Figtree', sans-serif";
        Chart.defaults.font.size   = 11;

        function xyScales() {
            return {
                x: { ticks:{ color:TXT, maxRotation:30 }, grid:{ display:false } },
                y: { beginAtZero:true, ticks:{ color:TXT }, grid:{ color:GRID } }
            };
        }
        function tip() {
            return { backgroundColor:'rgba(15,23,42,.9)', titleColor:'#fff', bodyColor:'#ddd', padding:8, cornerRadius:6 };
        }

        /* ── 1. INSCRIPTIONS ── */
        let inscChart;
        let currentPeriod = 'jour';

        function buildInscriptions(labels, data, ranges) {
            const canvas = document.getElementById('chart-inscriptions');
            if (!canvas) return;
            if (inscChart) inscChart.destroy();

            const clickPoint = (evt, items) => {
                let idx = (items && items.length) ? items[0].index : null;

                if (idx === null && inscChart) {
                    const xScale = inscChart.scales.x;
                    const estimated = Math.round(xScale.getValueForPixel(evt.x));
                    if (Number.isFinite(estimated)) idx = estimated;
                }

                if (idx === null || idx < 0) return;
                const range = ranges && ranges[idx];
                if (!range) return;
                if (typeof window.openRegistrationsDetails === 'function') {
                    window.openRegistrationsDetails(range[0], range[1], labels[idx]);
                }
            };

            inscChart = new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: safe(labels, ['—']),
                    datasets: [{
                        label: 'Inscriptions',
                        data: safe(data, [0]),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHitRadius: 15,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: true, mode: 'nearest' },
                    plugins: {
                        legend: { display: false },
                        tooltip: tip()
                    },
                    scales: xyScales(),
                    onClick: clickPoint,
                    onHover: (evt, item) => {
                        evt.native.target.style.cursor = item[0] ? 'pointer' : 'default';
                    }
                }
            });
        }

        window.switchPeriod = function(period) {
            const map = {
                jour:    [D.labelsJour,    D.evolutionJour],
                semaine: [D.labelsSemaine, D.evolutionSemaine],
                mois:    [D.labelsMois,    D.evolutionMois],
            };
            const ranges = {
                jour:    D.rangesJour,
                semaine: D.rangesSemaine,
                mois:    D.rangesMois,
            };
            document.querySelectorAll('[id^="btn-"]').forEach(b => {
                b.classList.remove('bg-[#1c3e96]','text-white');
                b.classList.add('text-[#6e6862]');
            });
            const btn = document.getElementById('btn-'+period);
            if (btn) { btn.classList.add('bg-[#1c3e96]','text-white'); btn.classList.remove('text-[#6e6862]'); }
            currentPeriod = period;
            buildInscriptions(map[period][0], map[period][1], ranges[period]);
        };
        buildInscriptions(D.labelsJour, D.evolutionJour, D.rangesJour);

        /* ── 2. TYPES ── */
        new Chart(document.getElementById('chart-types')?.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: safe(D.typesLabels,['Vide']),
                datasets: [{ data:safe(D.typesData,[1]), backgroundColor:['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'] }]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom', labels:{color:TXT, boxWidth:10, padding:8} }, tooltip:tip() } }
        });

        /* ── 3. DOMAINES (donut par domaine) ── */
        new Chart(document.getElementById('chart-domaines')?.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: safe(D.domLabels, ['Vide']),
                datasets: [{
                    data: safe(D.domTotaux, [1]),
                    backgroundColor: (D.domPalette && D.domPalette.length) ? D.domPalette : ['#06b6d4'],
                    borderColor: isDark ? '#1f2937' : '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: Object.assign(tip(), {
                        callbacks: {
                            label: (ctx) => {
                                let total = 0;
                                (ctx.dataset.data || []).forEach(v => total += v);
                                const share = total ? Math.round(ctx.raw / total * 100) : 0;
                                return ' ' + ctx.label + ' : ' + ctx.raw + ' (' + share + '%)';
                            }
                        }
                    })
                }
            }
        });

        /* ── 4. TÂCHES (courbes 12 mois) ── */
        (function() {
            const el = document.getElementById('chart-tasks');
            if (!el) return;

            const typesByDs = ['created', 'in_progress', 'completed'];
            let tasksChart;

            const clickPoint = (evt, items) => {
                let idx = (items && items.length) ? items[0].index : null;
                let ds  = (items && items.length) ? items[0].datasetIndex : null;

                if (idx === null && tasksChart) {
                    const xScale = tasksChart.scales.x;
                    const estimated = Math.round(xScale.getValueForPixel(evt.x));
                    if (Number.isFinite(estimated)) idx = estimated;
                }
                if (ds === null && items && items.length) ds = items[0].datasetIndex;

                if (idx === null || idx < 0) return;
                const range = D.tasksRanges && D.tasksRanges[idx];
                if (!range) return;
                const type = typesByDs[ds] || 'created';
                const label = (D.tasksMoisLabels && D.tasksMoisLabels[idx]) || '';
                if (typeof window.openTasksDetails === 'function') {
                    window.openTasksDetails(range[0], range[1], label, type);
                }
            };

            tasksChart = new Chart(el.getContext('2d'), {
                type: 'line',
                data: {
                    labels: safe(D.tasksMoisLabels, []),
                    datasets: [
                        { label: 'Créées',  data: safe(D.tasksCreated, [0]),    borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.08)', fill: true, tension: 0.4, pointRadius: 4, pointHitRadius: 15, pointHoverRadius: 7, pointBackgroundColor: '#8b5cf6', pointBorderColor: '#ffffff', pointBorderWidth: 1.5 },
                        { label: 'En cours', data: safe(D.tasksInProgress, [0]), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.06)',  fill: false, tension: 0.4, pointRadius: 4, pointHitRadius: 15, pointHoverRadius: 7, pointBackgroundColor: '#3b82f6', pointBorderColor: '#ffffff', pointBorderWidth: 1.5 },
                        { label: 'Terminées', data: safe(D.tasksCompleted, [0]), borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.06)',  fill: false, tension: 0.4, pointRadius: 4, pointHitRadius: 15, pointHoverRadius: 7, pointBackgroundColor: '#10b981', pointBorderColor: '#ffffff', pointBorderWidth: 1.5 },
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, interaction: { intersect: true, mode: 'nearest' }, onClick: clickPoint, onHover: (evt, item) => { evt.native.target.style.cursor = item[0] ? 'pointer' : 'default'; }, plugins: { legend: { position: 'bottom', labels: { color: TXT, boxWidth: 10, padding: 8 } }, tooltip: tip() }, scales: xyScales() }
            });
        })();

        /* ── CERCLE 1 : RÉPARTITION UTILISATEURS ── */
        (function() {
            const el = document.getElementById('chart-users-roles');
            if (!el) return;
            const totalUsers = {{ $totalUsers }};
            const dataRoles = [{{ $usersStagiaires }}, {{ $usersEmployes }}, {{ $usersAdmins }} @if($usersAutres > 0), {{ $usersAutres }} @endif];
            const labelsRoles = ['Stagiaires', 'Employés', 'Admins / Superviseurs' @if($usersAutres > 0), 'Autres' @endif];
            const colorsRoles = ['#06b6d4', '#8b5cf6', '#f59e0b', '#9ca3af'];

            const drawCenterText = (ctx, cx, cy, mainTxt, subTxt) => {
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = 'bold 26px sans-serif';
                ctx.fillStyle = isDark ? '#f9fafb' : '#111827';
                ctx.fillText(mainTxt, cx, cy - 8);
                ctx.font = '11px sans-serif';
                ctx.fillStyle = isDark ? '#9ca3af' : '#6b7280';
                ctx.fillText(subTxt, cx, cy + 14);
                ctx.restore();
            };

            const drawSlicePct = (ctx, chart, dsData) => {
                const total = dsData.reduce((a, b) => a + (b || 0), 0);
                if (!total) return;
                chart.getDatasetMeta(0).data.forEach((arc, i) => {
                    const val = dsData[i];
                    if (!val) return;
                    const pct = val / total * 100;
                    if (pct < 5) return;
                    const pos = arc.tooltipPosition();
                    ctx.save();
                    ctx.font = 'bold 11px sans-serif';
                    ctx.fillStyle = '#fff';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.shadowColor = 'rgba(0,0,0,0.5)';
                    ctx.shadowBlur = 3;
                    ctx.fillText(pct.toFixed(1) + '%', pos.x, pos.y);
                    ctx.restore();
                });
            };

            const pluginRoles = {
                id: 'pluginRoles',
                afterDraw(chart) {
                    const { ctx, chartArea } = chart;
                    const cx = (chartArea.left + chartArea.right) / 2;
                    const cy = (chartArea.top + chartArea.bottom) / 2;
                    drawCenterText(ctx, cx, cy, totalUsers, 'Utilisateurs');
                    drawSlicePct(ctx, chart, dataRoles);
                }
            };

            new Chart(el.getContext('2d'), {
                type: 'doughnut',
                plugins: [pluginRoles],
                data: {
                    labels: labelsRoles,
                    datasets: [{ data: dataRoles, backgroundColor: colorsRoles, borderWidth: 2, borderColor: isDark ? '#1f2937' : '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { color: TXT, boxWidth: 10, padding: 8 } }, tooltip: tip() } }
            });
        })();

        /* ── CERCLE 2 : STATUT COMPTES ── */
        (function() {
            const el = document.getElementById('chart-users-status');
            if (!el) return;
            const totalUsers = {{ $totalUsers }};
            const dataStatus = [{{ $usersActifs }}, {{ $usersInactifs }}];
            const labelsStatus = ['Actifs', 'Inactifs'];
            const colorsStatus = ['#10b981', '#ef4444'];

            const pluginStatus = {
                id: 'pluginStatus',
                afterDraw(chart) {
                    const { ctx, chartArea } = chart;
                    const cx = (chartArea.left + chartArea.right) / 2;
                    const cy = (chartArea.top + chartArea.bottom) / 2;
                    // Centre
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = 'bold 26px sans-serif';
                    ctx.fillStyle = isDark ? '#f9fafb' : '#111827';
                    ctx.fillText(totalUsers, cx, cy - 8);
                    ctx.font = '11px sans-serif';
                    ctx.fillStyle = isDark ? '#9ca3af' : '#6b7280';
                    ctx.fillText('Comptes', cx, cy + 14);
                    ctx.restore();
                    // Pourcentages sur tranches
                    const total = dataStatus.reduce((a, b) => a + (b || 0), 0);
                    if (!total) return;
                    chart.getDatasetMeta(0).data.forEach((arc, i) => {
                        const val = dataStatus[i];
                        if (!val) return;
                        const pct = val / total * 100;
                        if (pct < 5) return;
                        const pos = arc.tooltipPosition();
                        ctx.save();
                        ctx.font = 'bold 11px sans-serif';
                        ctx.fillStyle = '#fff';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.shadowColor = 'rgba(0,0,0,0.5)';
                        ctx.shadowBlur = 3;
                        ctx.fillText(pct.toFixed(1) + '%', pos.x, pos.y);
                        ctx.restore();
                    });
                }
            };

            new Chart(el.getContext('2d'), {
                type: 'doughnut',
                plugins: [pluginStatus],
                data: {
                    labels: labelsStatus,
                    datasets: [{ data: dataStatus, backgroundColor: colorsStatus, borderWidth: 2, borderColor: isDark ? '#1f2937' : '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { color: TXT, boxWidth: 10, padding: 8 } }, tooltip: tip() } }
            });
        })();

    });
    </script>
    @endpush

</x-app-layout>