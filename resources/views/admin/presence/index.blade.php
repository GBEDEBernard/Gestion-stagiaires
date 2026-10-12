<x-app-layout title="Supervision de la Présence - Admin">

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

        .ks-stat-icon { width: 32px; height: 32px; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center; flex: none; }
        .ks-stat-icon svg { width: 16px; height: 16px; stroke-width: 1.8; }

        .ks-num { font-variant-numeric: tabular-nums; }
        .ks-stat-change { font-size: .68rem; font-weight: 600; padding: .15rem .45rem; border-radius: 4px; display: inline-flex; align-items: center; gap: .25rem; white-space: nowrap; }
        .ks-stat-change--positive { color: #15803d; background-color: #ecfdf5; }
        .dark .ks-stat-change--positive { color: #4ade80 !important; background-color: rgba(22,128,74,0.22) !important; }
        .ks-stat-change--neutral { color: #475569; background-color: #f1f5f9; }
        .dark .ks-stat-change--neutral { color: #cbd5e1 !important; background-color: #262a30 !important; }
        .ks-stat-change--blue { color: #1c3e96; background-color: #eef1fb; }
        .dark .ks-stat-change--blue { color: #93c5fd !important; background-color: rgba(28,62,150,0.25) !important; }
        .ks-stat-change--amber { color: #b45309; background-color: #fffbeb; }
        .dark .ks-stat-change--amber { color: #fbbf24 !important; background-color: rgba(180,83,9,0.22) !important; }
        .ks-stat-change--negative { color: #b91c1c; background-color: #fef2f2; }
        .dark .ks-stat-change--negative { color: #f87171 !important; background-color: rgba(185,28,28,0.22) !important; }

        .ap-pill { font-size: .72rem; font-weight: 500; padding: .2rem .55rem; border-radius: 999px; background: #faf9f6; color: #6e6862; border: 1px solid #dcdad2; display: inline-flex; align-items: center; gap: .25rem; }
        .dark .ap-pill { background: #181c24; color: #94a3b8; border-color: #222630; }
        .ap-pill b { font-weight: 700; color: #241f1b; }
        .dark .ap-pill b { color: #f8fafc; }

        /* Dark mode overrides complets */
        .dark .text-slate-900, .dark .text-slate-800, .dark .text-gray-900 { color: #f8fafc !important; }
        .dark .text-slate-600, .dark .text-slate-500, .dark .text-gray-600 { color: #94a3b8 !important; }
        .dark .border-slate-200, .dark .border-slate-300, .dark .border-gray-200 { border-color: #222630 !important; }
        .dark select, .dark input[type="date"], .dark input[type="text"] {
            background-color: #181c24 !important;
            border-color: #222630 !important;
            color: #f8fafc !important;
        }

        /* Modale Absences */
        .pres-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
        }
        .pres-modal-backdrop.active { display: flex; }
        .pres-modal {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 12px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: modalIn .2s ease-out;
        }
        .dark .pres-modal {
            background: #131519 !important;
            border-color: #222630 !important;
            color: #f8fafc !important;
        }
        .pres-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #dcdad2;
            background: #faf9f6;
        }
        .dark .pres-modal-header {
            border-color: #222630 !important;
            background: #181c24 !important;
        }
        .pres-modal-title { font-size: 1rem; font-weight: 700; margin: 0; }
        .pres-modal-meta { font-size: .75rem; color: #6e6862; margin-top: .15rem; }
        .dark .pres-modal-meta { color: #94a3b8; }
        .pres-modal-close {
            background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #6e6862; padding: .25rem; line-height: 1;
        }
        .dark .pres-modal-close { color: #94a3b8; }
        .pres-modal-body { padding: 1rem 1.25rem; max-height: 60vh; overflow-y: auto; }
        .pres-modal-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: .5rem; }
        .pres-modal-item {
            display: flex; align-items: center; gap: .75rem; padding: .6rem .8rem;
            border-radius: 8px; background: #faf9f6; border: 1px solid #dcdad2;
        }
        .dark .pres-modal-item { background: #181c24; border-color: #222630; }
        .pres-modal-item-marker { color: #e11d48; font-weight: bold; font-size: .8rem; }
        .pres-modal-item-title { font-size: .8rem; font-weight: 600; color: #241f1b; }
        .dark .pres-modal-item-title { color: #f8fafc; }
        .pres-modal-item-sub { font-size: .72rem; color: #6e6862; }
        .dark .pres-modal-item-sub { color: #94a3b8; }
        .pres-modal-empty { text-align: center; color: #6e6862; font-size: .85rem; padding: 1.5rem 0; }
        .dark .pres-modal-empty { color: #94a3b8; }

        /* Tables & Tags */
        .pres-table { width: 100%; border-collapse: collapse; font-size: .8rem; }
        .pres-table th {
            text-align: left; padding: .55rem .75rem; font-size: .68rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .05em; color: #6e6862;
            background: #faf9f6; border-bottom: 1px solid #dcdad2;
        }
        .dark .pres-table th { background: #181c24; border-color: #222630; color: #94a3b8; }
        .pres-table td {
            padding: .6rem .75rem; border-bottom: 1px solid #ecebe3; color: #241f1b;
        }
        .dark .pres-table td { border-color: #222630; color: #f8fafc; }
        .pres-table tr:hover td { background: #faf9f6; }
        .dark .pres-table tr:hover td { background: #181c24; }

        .pres-tag {
            display: inline-flex; align-items: center; gap: .25rem; font-size: .72rem;
            font-weight: 600; padding: .15rem .45rem; border-radius: 4px; border: none; cursor: pointer;
        }
        .tag-amber { background: #fffbeb; color: #b45309; }
        .dark .tag-amber { background: rgba(180,83,9,0.22); color: #fbbf24; }
        .tag-rose { background: #fef2f2; color: #b91c1c; }
        .dark .tag-rose { background: rgba(185,28,28,0.22); color: #f87171; }
        .tag-emerald { background: #ecfdf5; color: #15803d; }
        .dark .tag-emerald { background: rgba(22,128,74,0.22); color: #4ade80; }

        .pres-rank {
            display: inline-flex; align-items: center; justify-content: center;
            width: 20px; height: 20px; border-radius: 4px; font-size: .7rem; font-weight: 700;
            background: #ecebe3; color: #6e6862;
        }
        .dark .pres-rank { background: #222630; color: #94a3b8; }
        .pres-rank-1 { background: #fef3c7; color: #b45309; }
        .dark .pres-rank-1 { background: rgba(180,83,9,0.3); color: #fbbf24; }
        .pres-rank-2 { background: #f1f5f9; color: #475569; }
        .dark .pres-rank-2 { background: #262a30; color: #cbd5e1; }
        .pres-rank-3 { background: #ffedd5; color: #c2410c; }
        .dark .pres-rank-3 { background: rgba(194,65,12,0.3); color: #fb923c; }

        /* Chart Legends */
        .chart-legend { display: flex; flex-wrap: wrap; gap: .75rem 1rem; font-size: .75rem; color: #6e6862; margin-bottom: .75rem; }
        .dark .chart-legend { color: #94a3b8; }
        .legend-item { display: inline-flex; align-items: center; gap: .35rem; font-weight: 500; }
        .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

        @media (max-width: 639px) {
            .pres-grid-2col { grid-template-columns: 1fr !important; }
        }
    </style>

    <div class="space-y-3 sm:space-y-3.5 overflow-x-hidden">

        {{-- ════════════════════════════════════════════════════════
             1. BANNIÈRE SUPERVISION PRÉSENCES (STYLE MON STAGE COMPACT)
        ════════════════════════════════════════════════════════ --}}
        <div class="relative overflow-hidden rounded-[10px] border border-[#dcdad2] dark:border-[#222630] bg-gradient-to-r from-[#1c3e96] via-[#1a3889] to-[#162f73] dark:from-[#131519] dark:via-[#161a22] dark:to-[#131519] shadow-sm">
            <div class="absolute inset-0 opacity-15 pointer-events-none"
                style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;)">
            </div>
            <div class="relative px-3.5 sm:px-4 py-2.5 sm:py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="ks-stat-icon !w-6 !h-6 rounded bg-blue-500/20 text-blue-200 dark:bg-blue-500/30 dark:text-blue-300">
                                <svg class="!w-3.5 !h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </span>
                            <h1 class="text-base sm:text-lg font-bold text-white tracking-tight leading-tight">Supervision des Présences</h1>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                Live
                            </span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-blue-100/80 dark:text-gray-400 mt-0.5">Analyses temporelles • Écarts de pointage • Bilan d'assiduité du personnel & stagiaires</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 self-start sm:self-auto">
                        <a href="{{ route('admin.presence.export') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 dark:bg-white/5 hover:bg-white/15 border border-white/15 dark:border-[#222630] text-xs font-semibold text-white transition-all whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-blue-200 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export CSV</span>
                        </a>
                        <a href="{{ route('admin.presence.pointage-suivi') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 dark:bg-white/5 hover:bg-white/15 border border-white/15 dark:border-[#222630] text-xs font-semibold text-white transition-all whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-blue-200 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <span>Suivi Pointage</span>
                        </a>
                        <a href="{{ route('admin.presence.corrections') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 dark:bg-white/5 hover:bg-white/15 border border-white/15 dark:border-[#222630] text-xs font-semibold text-white transition-all whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-blue-200 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Corrections</span>
                        </a>
                        <a href="{{ route('admin.presence.anomalies') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 border border-rose-400/30 text-xs font-semibold text-rose-200 transition-all whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Anomalies</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             2. ONGLETS PÉRIODE & FILTRE PLAGE DE DATE
        ════════════════════════════════════════════════════════ --}}
        <div class="ks-sec p-3 sm:p-3.5 space-y-2.5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                {{-- Onglets segmentés Mon Stage --}}
                <div class="inline-flex items-center p-0.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] self-start">
                    @foreach(['today'=>"Aujourd'hui",'week'=>'Semaine','month'=>'Mois','year'=>'Année'] as $k=>$lbl)
                    <a href="{{ route('admin.presence.index', array_filter(['period'=>$k, 'date_from'=>$rangeStart->format('Y-m-d')])) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-md transition-all {{ $period===$k ? 'bg-[#1c3e96] text-white shadow-sm' : 'text-[#6e6862] dark:text-[#a89f93] hover:text-[#241f1b] dark:hover:text-white' }}">
                        {{ $lbl }}
                    </a>
                    @endforeach
                </div>

                <div class="text-xs text-[#6e6862] dark:text-[#a89f93] flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#1c3e96] dark:bg-blue-400"></span>
                    <span>Période appliquée : <b>{{ $rangeStart->locale('fr')->isoFormat('D MMM YYYY') }}</b> → <b>{{ $rangeEnd->locale('fr')->isoFormat('D MMM YYYY') }}</b></span>
                </div>
            </div>

            {{-- Formulaire Date Personnalisée --}}
            <form method="GET" action="{{ route('admin.presence.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 items-end pt-2 border-t border-[#ecebe3] dark:border-[#222630]">
                <input type="hidden" name="period" value="custom" />
                <input type="hidden" name="group" value="{{ $group ?? '' }}" />

                <div>
                    <label class="ks-label block mb-1">Du</label>
                    <input type="date" name="date_from" value="{{ $rangeStart->format('Y-m-d') }}"
                           class="w-full px-2.5 py-1.5 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] transition">
                </div>

                <div>
                    <label class="ks-label block mb-1">Au</label>
                    <input type="date" name="date_to" value="{{ $rangeEnd->format('Y-m-d') }}"
                           class="w-full px-2.5 py-1.5 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] transition">
                </div>

                <div class="flex items-center gap-1.5">
                    <button type="submit" class="ks-btn-primary ks-btn-xs flex-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Afficher</span>
                    </button>
                    <a href="{{ route('admin.presence.index') }}" class="ks-btn-secondary ks-btn-xs px-2.5" title="Réinitialiser">
                        <svg class="w-3.5 h-3.5 text-[#6e6862] dark:text-[#a89f93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        {{-- ════════════════════════════════════════════════════════
             3. CARTES KPIS DE SUPERVISION (STYLE MON STAGE COMPACT)
        ════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
            {{-- KPI 1: Taux de présence --}}
            <div class="ks-sec p-3 flex items-center justify-between">
                <div>
                    <span class="ks-label">Taux de Présence</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="ks-num text-xl sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            {{ $globalStats['taux_presence'] ?? 0 }}%
                        </span>
                    </div>
                    <span class="text-[11px] font-medium text-[#6e6862] dark:text-[#a89f93] mt-0.5 block">
                        {{ $globalStats['present_days'] ?? 0 }}/{{ $globalStats['total_days'] ?? 0 }} jours présents
                    </span>
                </div>
                <span class="ks-stat-icon bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>

            {{-- KPI 2: Retards Cumulés --}}
            <div class="ks-sec p-3 flex items-center justify-between">
                <div>
                    <span class="ks-label">Retards Cumulés</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="ks-num text-xl sm:text-2xl font-bold text-amber-600 dark:text-amber-400">
                            {{ round(($globalStats['total_late_minutes'] ?? 0) / 60, 1) }}h
                        </span>
                    </div>
                    <span class="text-[11px] font-medium text-[#6e6862] dark:text-[#a89f93] mt-0.5 block">
                        {{ ($globalStats['total_late_minutes'] ?? 0) ? formatMinutes($globalStats['total_late_minutes']) : '0min' }} ({{ $globalStats['total_late_days'] ?? 0 }} j)
                    </span>
                </div>
                <span class="ks-stat-icon bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>

            {{-- KPI 3: Heures Travaillées --}}
            <div class="ks-sec p-3 flex items-center justify-between">
                <div>
                    <span class="ks-label">Heures Travaillées</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="ks-num text-xl sm:text-2xl font-bold text-[#1c3e96] dark:text-blue-400">
                            {{ $globalStats['total_worked_hours'] ?? 0 }}h
                        </span>
                    </div>
                    <span class="text-[11px] font-medium text-[#6e6862] dark:text-[#a89f93] mt-0.5 block">
                        {{ number_format($globalStats['total_days'] ?? 0) }} jours pointés
                    </span>
                </div>
                <span class="ks-stat-icon bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400 border border-blue-200 dark:border-blue-800/40">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
            </div>

            {{-- KPI 4: Anomalies Ouvertes --}}
            <div class="ks-sec p-3 flex items-center justify-between">
                <div>
                    <span class="ks-label">Anomalies Ouvertes</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="ks-num text-xl sm:text-2xl font-bold {{ ($globalStats['total_anomalies'] ?? 0) > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-[#241f1b] dark:text-[#f8fafc]' }}">
                            {{ $globalStats['total_anomalies'] ?? 0 }}
                        </span>
                    </div>
                    <span class="text-[11px] font-medium text-[#6e6862] dark:text-[#a89f93] mt-0.5 block">
                        {{ \App\Models\AttendanceAnomaly::where('status','resolved')->whereDate('reviewed_at',today())->count() }} résolue(s) auj.
                    </span>
                </div>
                <span class="ks-stat-icon {{ ($globalStats['total_anomalies'] ?? 0) > 0 ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800/40' : 'bg-slate-50 text-slate-600 dark:bg-slate-900 dark:text-slate-400 border border-slate-200 dark:border-slate-800' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             4. ACTIONS RAPIDES & RACCOURCIS
        ════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <a href="{{ route('admin.presence.anomalies') }}"
               class="ks-sec p-3 flex items-center justify-between hover:border-rose-500/40 transition group">
                <div class="flex items-center gap-2.5">
                    <span class="ks-stat-icon bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800/40 group-hover:scale-105 transition-transform">
                        🚨
                    </span>
                    <div>
                        <h3 class="text-xs font-bold text-[#241f1b] dark:text-[#f8fafc]">Anomalies Détectées</h3>
                        <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">Traiter les incohérences</p>
                    </div>
                </div>
                <span class="ks-num text-sm font-bold text-rose-600 dark:text-rose-400">{{ $globalStats['total_anomalies'] ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.presence.pointage-suivi') }}"
               class="ks-sec p-3 flex items-center justify-between hover:border-emerald-500/40 transition group">
                <div class="flex items-center gap-2.5">
                    <span class="ks-stat-icon bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40 group-hover:scale-105 transition-transform">
                        📍
                    </span>
                    <div>
                        <h3 class="text-xs font-bold text-[#241f1b] dark:text-[#f8fafc]">Suivi Pointage Temps Réel</h3>
                        <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">Historique & géolocalisation</p>
                    </div>
                </div>
                <span class="text-xs text-[#1c3e96] dark:text-blue-400 font-semibold">Accéder ›</span>
            </a>

            <a href="{{ route('tasks.index') }}"
               class="ks-sec p-3 flex items-center justify-between hover:border-blue-500/40 transition group">
                <div class="flex items-center gap-2.5">
                    <span class="ks-stat-icon bg-blue-50 text-[#1c3e96] dark:bg-blue-950/40 dark:text-blue-400 border border-blue-200 dark:border-blue-800/40 group-hover:scale-105 transition-transform">
                        📋
                    </span>
                    <div>
                        <h3 class="text-xs font-bold text-[#241f1b] dark:text-[#f8fafc]">Tâches & Activités</h3>
                        <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">Suivi des livrables</p>
                    </div>
                </div>
                <span class="text-xs text-[#1c3e96] dark:text-blue-400 font-semibold">Voir ›</span>
            </a>
        </div>

        {{-- ════════════════════════════════════════════════════════
             5. GRAPHIQUES (ÉVOLUTION & HEURES / RETARDS)
        ════════════════════════════════════════════════════════ --}}
        @php $hasChartData = !empty($globalStats['chart_data']['labels']); @endphp
        @if($hasChartData)
        <div class="ks-sec p-3.5 sm:p-4 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                <div>
                    <h2 class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc]">Évolution · Présence & Ponctualité</h2>
                    <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">💡 Cliquez sur un point d'une courbe pour afficher le détail du jour</p>
                </div>
                <div class="chart-legend mb-0 pt-1 sm:pt-0">
                    <div class="legend-item"><span class="legend-dot" style="background:#10b981"></span>Présents</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#3b82f6"></span>À l'heure</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#f59e0b"></span>En retard</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#f43f5e"></span>Absents</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#f97316;border-radius:2px;"></span>Retard (min) →</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#8b5cf6;border-radius:2px;"></span>Heures travaillées →</div>
                    <div class="legend-item"><span class="legend-dot" style="background:rgba(139,92,246,0.3);"></span>Jour férié</div>
                </div>
            </div>
            <div style="position:relative; height:300px; width:100%;">
                <canvas id="chartGlobal"></canvas>
            </div>
        </div>

        <div class="ks-sec p-3.5 sm:p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc]">Vue d'Ensemble · Heures & Retards</h2>
            </div>
            <div style="position:relative; height:300px; width:100%;">
                <canvas id="chartOverview"></canvas>
            </div>
        </div>
        @endif

        {{-- ════════════════════════════════════════════════════════
             6. STATS PAR GROUPE (STAGIAIRES & EMPLOYÉS)
        ════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            {{-- Stagiaires --}}
            <div class="ks-sec p-3 sm:p-3.5 space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-[#241f1b] dark:text-[#f8fafc] flex items-center gap-1.5">
                            <span>👥</span> Stagiaires Actifs
                        </div>
                        <div class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">
                            Moy. {{ $groupStats['etudiants']['avg_worked_hours'] ?? 0 }}h / jour
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="ks-num text-sm font-bold text-[#241f1b] dark:text-[#f8fafc]">{{ $groupStats['etudiants']['present'] ?? 0 }}</span>
                        <span class="text-xs text-[#a89f93]">/ {{ $groupStats['etudiants']['count'] ?? 0 }}</span>
                    </div>
                </div>
                @php
                    $pctEtudiant = ($groupStats['etudiants']['count'] ?? 0) > 0 ? round(($groupStats['etudiants']['present'] ?? 0) / ($groupStats['etudiants']['count'] ?? 1) * 100) : 0;
                @endphp
                <div class="w-full bg-[#faf9f6] dark:bg-[#181c24] h-2 rounded-full overflow-hidden border border-[#dcdad2] dark:border-[#222630]">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $pctEtudiant }}%;"></div>
                </div>
                <div class="flex justify-end text-[10px] font-bold text-[#6e6862] dark:text-[#a89f93]">
                    {{ $pctEtudiant }}% d'assiduité
                </div>
            </div>

            {{-- Employés --}}
            <div class="ks-sec p-3 sm:p-3.5 space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-[#241f1b] dark:text-[#f8fafc] flex items-center gap-1.5">
                            <span>👔</span> Personnel Actif
                        </div>
                        <div class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">
                            Moy. {{ $groupStats['employes']['avg_worked_hours'] ?? 0 }}h / jour
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="ks-num text-sm font-bold text-[#241f1b] dark:text-[#f8fafc]">{{ $groupStats['employes']['present'] ?? 0 }}</span>
                        <span class="text-xs text-[#a89f93]">/ {{ $groupStats['employes']['count'] ?? 0 }}</span>
                    </div>
                </div>
                @php
                    $pctEmploye = ($groupStats['employes']['count'] ?? 0) > 0 ? round(($groupStats['employes']['present'] ?? 0) / ($groupStats['employes']['count'] ?? 1) * 100) : 0;
                @endphp
                <div class="w-full bg-[#faf9f6] dark:bg-[#181c24] h-2 rounded-full overflow-hidden border border-[#dcdad2] dark:border-[#222630]">
                    <div class="h-full bg-blue-600 rounded-full transition-all duration-500" style="width: {{ $pctEmploye }}%;"></div>
                </div>
                <div class="flex justify-end text-[10px] font-bold text-[#6e6862] dark:text-[#a89f93]">
                    {{ $pctEmploye }}% d'assiduité
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             7. SUIVI DES RAPPORTS JOURNALIERS
        ════════════════════════════════════════════════════════ --}}
        <div class="ks-sec p-3.5 sm:p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc] flex items-center gap-1.5">
                    <span>📑</span> Suivi des Rapports Journaliers
                </h2>
                <a href="{{ route('tasks.index') }}" class="text-xs font-semibold text-[#1c3e96] dark:text-blue-400 hover:underline">
                    Gérer les tâches ›
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630]">
                    <span class="ks-label block text-[10px]">Brouillons</span>
                    <span class="ks-num text-lg font-bold text-[#241f1b] dark:text-[#f8fafc]">{{ $reportStats['drafts'] }}</span>
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93] block mt-0.5">À compléter auj.</span>
                </div>
                <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630]">
                    <span class="ks-label block text-[10px]">En Attente</span>
                    <span class="ks-num text-lg font-bold text-amber-600 dark:text-amber-400">{{ $reportStats['pending'] }}</span>
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93] block mt-0.5">À valider</span>
                </div>
                <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630]">
                    <span class="ks-label block text-[10px]">Approuvés</span>
                    <span class="ks-num text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $reportStats['approved'] }}</span>
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93] block mt-0.5">Cette semaine</span>
                </div>
                <div class="p-2.5 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630]">
                    <span class="ks-label block text-[10px]">Taux Validation</span>
                    <span class="ks-num text-lg font-bold text-[#1c3e96] dark:text-blue-400">{{ $reportStats['validation_rate'] }}%</span>
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93] block mt-0.5">Obj. &gt; 90%</span>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             8. TOP 10 RETARDS & ABSENCES
        ════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
            {{-- Top 10 Retards --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <span class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc] flex items-center gap-1.5">
                        <span>🏆</span> Top Retards
                    </span>
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">
                        {{ $period === 'custom' ? ($rangeStart->format('d/m') . ' → ' . $rangeEnd->format('d/m')) : ucfirst($period ?? 'mois') }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="pres-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Utilisateur</th>
                                <th>Jours</th>
                                <th>Cumul</th>
                                <th>Moy/j</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topLate ?? [] as $i => $user)
                            <tr>
                                <td><span class="pres-rank {{ $i===0?'pres-rank-1':($i===1?'pres-rank-2':($i===2?'pres-rank-3':'')) }}">{{ $i+1 }}</span></td>
                                <td class="font-medium text-xs">{{ $user->user_name ?? $user->name }}</td>
                                <td>
                                    <span class="pres-tag tag-amber">{{ $user->days_count }} j</span>
                                </td>
                                <td>
                                    <span class="pres-tag tag-amber font-mono">{{ formatMinutes($user->total_late ?? 0) }}</span>
                                </td>
                                <td class="font-mono text-xs text-amber-600 dark:text-amber-400">
                                    {{ formatMinutes($user->avg_late ?? 0) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-xs text-[#6e6862] dark:text-[#a89f93]">
                                    ✅ Aucun retard détecté sur la période.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Absences --}}
            <div class="ks-sec overflow-hidden">
                <div class="ks-sec-head">
                    <span class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc] flex items-center gap-1.5">
                        <span>⭕</span> Bilan Absences
                    </span>
                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">
                        {{ $period === 'custom' ? ($rangeStart->format('d/m') . ' → ' . $rangeEnd->format('d/m')) : ucfirst($period ?? 'mois') }}
                    </span>
                </div>
                @if(empty($absences))
                <div class="text-center py-8 text-xs text-[#6e6862] dark:text-[#a89f93]">
                    🎉 Aucune absence enregistrée sur cette période.
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="pres-table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th class="text-right">Jours d'absence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absenceItems as $item)
                            <tr>
                                <td class="font-medium text-xs">{{ $item['user'] }}</td>
                                <td class="text-right">
                                    <button type="button" class="pres-tag tag-rose pres-absence-count-button" data-index="{{ $loop->index }}" title="Voir les dates d'absence">
                                        {{ $item['count'] }} jour(s) ›
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             9. DÉPARTS NON POINTÉS À RÉGLER (RÉSUMÉ)
        ════════════════════════════════════════════════════════ --}}
        <div class="ks-sec overflow-hidden">
            <div class="ks-sec-head flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="ks-stat-icon !w-5 !h-5 bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">
                        <svg class="!w-3 !h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <h2 class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc]">Départs à Régulariser</h2>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.presence.corrections') }}" class="text-xs font-semibold text-[#1c3e96] dark:text-blue-400 hover:underline">
                        Gérer toutes les corrections ›
                    </a>
                </div>
            </div>

            @if($pendingCorrections->isEmpty())
                <div class="text-center py-6 text-xs text-[#6e6862] dark:text-[#a89f93]">
                    ✅ Aucun départ oublié en attente de correction.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="pres-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Collaborateur</th>
                                <th>Arrivée</th>
                                <th>Clôturé à</th>
                                <th>Statut</th>
                                <th>Déclaration</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingCorrections as $pc)
                                @php
                                    $pcUser    = $pc->user ?: $pc->etudiant?->user;
                                    $pcName    = $pcUser?->name ?? ($pc->etudiant?->full_name ?? 'Utilisateur');
                                    $pcEtat    = $pc->departure_status === 'claimed' ? 'Déclaré' : 'Clôturé';
                                    $pcEjEtat  = $pc->departure_status === 'claimed' ? 'tag-amber' : 'tag-rose';
                                @endphp
                                <tr>
                                    <td class="whitespace-nowrap text-xs">{{ $pc->attendance_date->locale('fr')->isoFormat('ddd D MMM YYYY') }}</td>
                                    <td class="font-medium text-xs">{{ $pcName }}</td>
                                    <td class="font-mono text-xs">{{ $pc->first_check_in_at?->format('H:i') ?? '—' }}</td>
                                    <td class="font-mono text-xs">{{ $pc->last_check_out_at?->format('H:i') ?? '—' }}</td>
                                    <td>
                                        <span class="pres-tag {{ $pcEjEtat }}">
                                            {{ $pcEtat }}
                                            @if($pc->departure_status === 'claimed')
                                                <span title="{{ $pc->claimed_check_out_reason ?? '' }}">· {{ $pc->claimed_check_out_at?->format('H:i') }}</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-xs text-[#6e6862] dark:text-[#a89f93] max-w-[200px] truncate">
                                        {{ $pc->claimed_check_out_reason ?? '—' }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.presence.corrections') }}" class="ks-btn-primary ks-btn-xs text-[11px] no-underline">
                                            Rétablir
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════════
         MODALES : ABSENCES & DÉTAIL DU JOUR
    ════════════════════════════════════════════════════════ --}}
    <div class="pres-modal-backdrop" id="absenceModalBackdrop" aria-hidden="true">
        <div class="pres-modal" role="dialog" aria-modal="true" aria-labelledby="absenceModalTitle">
            <div class="pres-modal-header">
                <div>
                    <h2 class="pres-modal-title" id="absenceModalTitle">Jours d'absence</h2>
                    <p class="pres-modal-meta" id="absenceModalMeta">Sélectionnez un utilisateur pour voir les dates.</p>
                </div>
                <button type="button" class="pres-modal-close" id="absenceModalClose" aria-label="Fermer la modale">×</button>
            </div>
            <div class="pres-modal-body" id="absenceModalBody">
                <div class="pres-modal-empty">Cliquez sur un total d'absences pour afficher les jours.</div>
            </div>
        </div>
    </div>

    {{-- Modale détail d'un jour cliqué sur les graphiques --}}
    <div id="presence-day-modal" x-data="presenceDayApp()" x-show="open" x-cloak
         class="fixed inset-0 z-[10000] flex items-start justify-center p-3 sm:p-6 overflow-y-auto">
        <div x-show="open" x-transition.opacity @click="open=false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="open" x-transition.duration.200ms
             class="relative bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] rounded-2xl shadow-2xl w-full max-w-5xl z-10 my-4 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between gap-3 px-4 sm:px-6 py-3.5 border-b border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24]">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="ks-stat-icon bg-blue-50 text-[#1c3e96] dark:bg-blue-950/40 dark:text-blue-300">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc] truncate">
                            Détail du jour — <span x-text="prefixLabel"></span>
                        </h3>
                        <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">
                            <span x-text="fullDateLabel"></span> (<span x-text="summary.total || 0"></span> personne(s) attendue(s))
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button @click="exportCsv()" :disabled="csvLoading"
                        class="ks-btn-secondary ks-btn-xs text-xs">
                        <svg x-show="!csvLoading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Export CSV</span>
                    </button>
                    <button @click="open=false" class="p-1 rounded text-[#6e6862] dark:text-[#a89f93] hover:text-[#241f1b] dark:hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Résumé --}}
            <div x-show="!loading && !error && allRows.length > 0" class="px-4 sm:px-6 pt-3 flex flex-wrap gap-1.5">
                <span class="ks-stat-change ks-stat-change--positive">Pr&eacute;sents <span x-text="summary.present || 0"></span></span>
                <span class="ks-stat-change ks-stat-change--blue">&Agrave; l'heure <span x-text="summary.on_time || 0"></span></span>
                <span class="ks-stat-change ks-stat-change--amber">En retard <span x-text="summary.late || 0"></span></span>
                <span class="ks-stat-change ks-stat-change--negative">Absents <span x-text="summary.absent || 0"></span></span>
                <span class="ks-stat-change ks-stat-change--neutral" x-show="summary.corrected">Corrig&eacute;s <span x-text="summary.corrected || 0"></span></span>
                <span class="ks-stat-change ks-stat-change--neutral">Heures <span x-text="fmtHours(summary.worked_minutes)"></span></span>
                <span class="ks-stat-change ks-stat-change--amber">Retard <span x-text="fmtMin(summary.late_minutes)"></span></span>
            </div>

            {{-- Body --}}
            <div class="px-4 sm:px-6 py-3.5" @keydown.escape.window="open=false">
                <div x-show="loading" class="py-12 text-center">
                    <p class="text-xs text-[#6e6862] dark:text-[#a89f93]">Chargement des détails du jour…</p>
                </div>

                <div x-show="!loading && error" class="py-12 text-center text-xs text-rose-600">
                    Impossible de charger les données du jour.
                </div>

                <div x-show="!loading && !error && flags.future" class="py-12 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">
                    Ce jour n'est pas encore arrivé.
                </div>
                <div x-show="!loading && !error && flags.weekend" class="py-12 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">
                    Jour de week-end — aucune présence attendue.
                </div>
                <div x-show="!loading && !error && flags.holiday" class="py-12 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">
                    Jour férié — aucune présence attendue.
                </div>
                <div x-show="!loading && !error && !flags.future && !flags.weekend && !flags.holiday && allRows.length === 0" class="py-12 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">
                    Aucune donnée de présence pour ce jour.
                </div>

                <div x-show="!loading && !error && allRows.length > 0" class="overflow-x-auto max-h-[50vh] overflow-y-auto rounded-lg border border-[#dcdad2] dark:border-[#222630]">
                    <table class="w-full text-xs">
                        <thead class="bg-[#faf9f6] dark:bg-[#181c24] sticky top-0 border-b border-[#dcdad2] dark:border-[#222630] text-[#6e6862] dark:text-[#a89f93]">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Collaborateur</th>
                                <th class="px-3 py-2 text-left font-semibold">Type</th>
                                <th class="px-3 py-2 text-left font-semibold">Site</th>
                                <th class="px-3 py-2 text-left font-semibold">Statut</th>
                                <th class="px-3 py-2 text-left font-semibold">Entrée</th>
                                <th class="px-3 py-2 text-right font-semibold">Sortie</th>
                                <th class="px-3 py-2 text-right font-semibold">Heures</th>
                                <th class="px-3 py-2 text-right font-semibold">Retard</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            <template x-for="r in rows" :key="r.id + '-' + r.group">
                                <tr class="hover:bg-[#faf9f6] dark:hover:bg-[#181c24] transition-colors">
                                    <td class="px-3 py-2 font-medium text-[#241f1b] dark:text-[#f8fafc]" x-text="r.name"></td>
                                    <td class="px-3 py-2 text-[#6e6862] dark:text-[#a89f93]" x-text="groupLabel(r.group)"></td>
                                    <td class="px-3 py-2 text-[#6e6862] dark:text-[#a89f93]" x-text="r.site_name || '—'"></td>
                                    <td class="px-3 py-2">
                                        <span class="ks-stat-change" :class="rowBadge(r).cls" x-text="rowBadge(r).label"></span>
                                    </td>
                                    <td class="px-3 py-2 font-mono" x-text="r.arrival || '—'"></td>
                                    <td class="px-3 py-2 font-mono text-right" x-text="r.departure || '—'"></td>
                                    <td class="px-3 py-2 font-mono text-right" x-text="fmtHours(r.worked_minutes)"></td>
                                    <td class="px-3 py-2 font-mono text-right" x-text="fmtMin(r.late_minutes)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination modal --}}
                <div x-show="!loading && !error && allRows.length > 0" class="mt-3 flex items-center justify-between text-xs text-[#6e6862] dark:text-[#a89f93]">
                    <span x-text="`Page ${page} / ${lastPage} (${allRows.length} total)`"></span>
                    <div class="flex gap-1.5">
                        <button @click="prevPage()" :disabled="page <= 1" class="ks-btn-secondary ks-btn-xs" :class="{ 'opacity-50 cursor-not-allowed': page <= 1 }">‹ Précédent</button>
                        <button @click="nextPage()" :disabled="page >= lastPage" class="ks-btn-secondary ks-btn-xs" :class="{ 'opacity-50 cursor-not-allowed': page >= lastPage }">Suivant ›</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function presenceDayApp() {
            return {
                open: false,
                loading: false,
                error: false,
                date: null,
                fullDateLabel: '',
                flags: { weekend: false, future: false, holiday: false, before_system: false },
                summary: { total: 0, present: 0, on_time: 0, late: 0, absent: 0, corrected: 0, worked_minutes: 0, late_minutes: 0 },
                allRows: [],
                rows: [],
                page: 1,
                perPage: 10,
                csvLoading: false,
                prefixLabel: '',

                async openDetail(date, label) {
                    this.date = date;
                    this.prefixLabel = label || '';
                    this.fullDateLabel = '';
                    this.page = 1;
                    this.allRows = [];
                    this.rows = [];
                    this.error = false;
                    this.open = true;
                    await this.loadDay();
                },

                async loadDay() {
                    if (!this.date) return;
                    this.loading = true;
                    this.error = false;
                    try {
                        const url = `/admin/presence/chart-detail?date=${encodeURIComponent(this.date)}`;
                        const resp = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        if (!resp.ok) throw new Error('HTTP ' + resp.status);
                        const data = await resp.json();
                        this.flags = {
                            weekend: !!data.weekend,
                            future: !!data.future,
                            holiday: !!data.holiday,
                            before_system: !!data.before_system,
                        };
                        this.summary = Object.assign({}, this.summary, data.summary || {});
                        this.allRows = data.rows || [];
                        this.fullDateLabel = data.label || '';
                        this.page = 1;
                        this.applyPage();
                    } catch (e) {
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                },

                applyPage() {
                    const start = (this.page - 1) * this.perPage;
                    this.rows = this.allRows.slice(start, start + this.perPage);
                },

                prevPage() { if (this.page > 1) { this.page--; this.applyPage(); } },
                nextPage() { if (this.page < this.lastPage) { this.page++; this.applyPage(); } },
                get lastPage() { return Math.max(1, Math.ceil(this.allRows.length / this.perPage)); },

                statusBadge(status) {
                    const map = {
                        on_time:   { label: "À l'heure", cls: 'ks-stat-change--blue' },
                        late:      { label: 'En retard', cls: 'ks-stat-change--amber' },
                        present:   { label: 'Présent', cls: 'ks-stat-change--positive' },
                        absent:    { label: 'Absent', cls: 'ks-stat-change--negative' },
                        corrected: { label: 'Corrigé', cls: 'ks-stat-change--neutral' },
                    };
                    return map[status] || { label: status || '—', cls: 'ks-stat-change--neutral' };
                },

                rowBadge(r) {
                    if (r.status === 'corrected' && r.is_permission) {
                        return { label: 'Permission', cls: 'ks-stat-change--blue' };
                    }
                    return this.statusBadge(r.status);
                },

                groupLabel(group) {
                    return group === 'employe' ? 'Employé' : 'Stagiaire';
                },

                fmtMin(m) {
                    if (!m || m <= 0) return '0min';
                    const h = Math.floor(m / 60), mins = m % 60;
                    if (h === 0) return mins + 'min';
                    if (mins === 0) return h + 'h';
                    return h + 'h ' + mins + 'min';
                },

                fmtHours(m) {
                    const h = (m || 0) / 60;
                    return h.toLocaleString('fr-FR', { maximumFractionDigits: 1 }) + 'h';
                },

                async exportCsv() {
                    if (this.csvLoading || !this.allRows.length) return;
                    this.csvLoading = true;
                    try {
                        const header = ['Utilisateur', 'Type', 'Site', 'Statut', 'Entrée', 'Sortie', 'Heures', 'Retard'];
                        const esc = (v) => '"' + String(v ?? '').replace(/"/g, '""') + '"';
                        const csv = [
                            header.join(';'),
                            ...this.allRows.map(r => [
                                r.name,
                                this.groupLabel(r.group),
                                r.site_name || '',
                                this.rowBadge(r).label,
                                r.arrival || '',
                                r.departure || '',
                                this.fmtHours(r.worked_minutes),
                                this.fmtMin(r.late_minutes),
                            ].map(esc).join(';')),
                        ].join('\n');
                        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = `presence_${this.date || 'detail'}.csv`;
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                    } catch (e) {
                        this.error = true;
                    } finally {
                        this.csvLoading = false;
                    }
                },
            };
        }

        window.openPresenceDayDetails = function (date, label) {
            const el = document.getElementById('presence-day-modal');
            if (!el) return;
            let data = null;
            if (window.Alpine && typeof window.Alpine.$data === 'function') {
                try { data = window.Alpine.$data(el); } catch (e) { data = null; }
            }
            if (!data && el.__x && el.__x.$data) {
                data = el.__x.$data;
            }
            if (!data || typeof data.openDetail !== 'function') {
                console.warn('[Presence] Alpine composant modale non initialisé.');
                return;
            }
            data.openDetail(date, label);
        };
    </script>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#f8fafc' : '#241f1b';
            const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
            const mutedColor = isDark ? '#94a3b8' : '#6e6862';

            Chart.defaults.font.family = "'Figtree', 'DM Sans', sans-serif";
            Chart.defaults.color = textColor;

            const fmtMin = (m) => {
                if (!m || m <= 0) return '0min';
                const h = Math.floor(m / 60), mins = m % 60;
                if (h === 0) return mins + 'min';
                if (mins === 0) return h + 'h';
                return h + 'h ' + mins + 'min';
            };

            const labels = @json($globalStats['chart_data']['labels'] ?? []);
            const chartDates = @json($globalStats['chart_data']['dates'] ?? []);
            const present = @json($globalStats['chart_data']['present'] ?? []);
            const lateMinutes = @json($globalStats['chart_data']['late_minutes'] ?? []);
            const lateDays = @json($globalStats['chart_data']['late_days'] ?? []);
            const absences = @json($globalStats['chart_data']['absent'] ?? []);
            const workedHours = @json($globalStats['chart_data']['worked_hours'] ?? []);
            const workedMinutes = workedHours.map(v => Math.round(v * 60));
            const holidays = @json($globalStats['chart_data']['holidays'] ?? []);
            const absenceItems = @json($absenceItems ?? []);

            const onTime = present.map((v, i) => v - (lateDays[i] ?? 0));

            const holidayPlugin = {
                id: 'holidayBands',
                beforeDraw(chart) {
                    const { ctx, chartArea: { left, right, top, bottom }, scales: { x } } = chart;
                    if (!x || !holidays.length) return;
                    const gap = labels.length > 1 ? x.getPixelForValue(1) - x.getPixelForValue(0) : 0;
                    holidays.forEach((isHoliday, i) => {
                        if (isHoliday) {
                            const xPos = x.getPixelForValue(i) - gap / 2;
                            ctx.save();
                            ctx.fillStyle = 'rgba(139,92,246,0.08)';
                            ctx.fillRect(xPos, top, gap, bottom - top);
                            ctx.restore();
                        }
                    });
                }
            };

            const openDayFromChart = (evt, items) => {
                let idx = (items && items.length) ? items[0].index : null;
                const chart = evt?.chart;
                if (idx === null && chart?.scales?.x) {
                    const estimated = Math.round(chart.scales.x.getValueForPixel(evt.x));
                    if (Number.isFinite(estimated)) idx = estimated;
                }
                if (idx === null || idx < 0 || idx >= chartDates.length) return;
                const date = chartDates[idx];
                if (!date) return;
                if (typeof window.openPresenceDayDetails === 'function') {
                    window.openPresenceDayDetails(date, labels[idx]);
                }
            };
            const chartCursor = (evt, item) => {
                evt.native.target.style.cursor = item[0] ? 'pointer' : 'default';
            };

            const absenceModalBackdrop = document.getElementById('absenceModalBackdrop');
            const absenceModalClose = document.getElementById('absenceModalClose');
            const absenceModalTitle = document.getElementById('absenceModalTitle');
            const absenceModalMeta = document.getElementById('absenceModalMeta');
            const absenceModalBody = document.getElementById('absenceModalBody');
            const absenceButtons = document.querySelectorAll('.pres-absence-count-button');

            const renderAbsenceDays = (days) => {
                if (!days || days.length === 0) {
                    return '<div class="pres-modal-empty">Aucune date d\'absence disponible pour cet utilisateur.</div>';
                }
                return `<ul class="pres-modal-list">${days.map((day, index) => `
                    <li class="pres-modal-item">
                        <span class="pres-modal-item-marker">✖</span>
                        <div>
                            <div class="pres-modal-item-title">${day.label}</div>
                            <div class="pres-modal-item-sub">${day.date}</div>
                        </div>
                    </li>
                `).join('')}</ul>`;
            };

            const openAbsenceModal = (index) => {
                const item = absenceItems[index] ?? null;
                if (!item) return;
                absenceModalTitle.textContent = `Absences de ${item.user}`;
                absenceModalMeta.textContent = `${item.count} jour${item.count > 1 ? 's' : ''} d'absence`;
                absenceModalBody.innerHTML = renderAbsenceDays(item.details);
                absenceModalBackdrop?.classList.add('active');
                absenceModalBackdrop?.setAttribute('aria-hidden', 'false');
            };

            absenceButtons.forEach(button => {
                button.addEventListener('click', () => {
                    openAbsenceModal(button.dataset.index);
                });
            });

            if (absenceModalClose && absenceModalBackdrop) {
                absenceModalClose.addEventListener('click', () => {
                    absenceModalBackdrop.classList.remove('active');
                    absenceModalBackdrop.setAttribute('aria-hidden', 'true');
                });
            }

            if (absenceModalBackdrop) {
                absenceModalBackdrop.addEventListener('click', (event) => {
                    if (event.target === absenceModalBackdrop) {
                        absenceModalBackdrop.classList.remove('active');
                        absenceModalBackdrop.setAttribute('aria-hidden', 'true');
                    }
                });
            }

            /* Chart 1: Global Line */
            const ctx = document.getElementById('chartGlobal');
            if (ctx && labels.length > 0) {
                const g = ctx.getContext('2d');
                const mkGrad = (top, bottom) => {
                    const grad = g.createLinearGradient(0, 0, 0, 300);
                    grad.addColorStop(0, top);
                    grad.addColorStop(1, bottom);
                    return grad;
                };
                const tooltipStyle = {
                    backgroundColor: isDark ? '#131519' : '#fffefa',
                    borderColor: isDark ? '#222630' : '#dcdad2',
                    borderWidth: 1,
                    titleColor: isDark ? '#f8fafc' : '#241f1b',
                    bodyColor: isDark ? '#94a3b8' : '#6e6862',
                    padding: 10,
                    cornerRadius: 8,
                };

                new Chart(ctx, {
                    type: 'line',
                    plugins: [holidayPlugin],
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Présents',
                                data: present,
                                borderColor: '#10b981',
                                backgroundColor: mkGrad('rgba(16,185,129,0.2)', 'rgba(16,185,129,0.01)'),
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointRadius: present.map(v => v ? 4 : 0),
                                pointHoverRadius: 7,
                                pointBackgroundColor: '#10b981',
                                yAxisID: 'yCount'
                            },
                            {
                                label: "À l'heure",
                                data: onTime,
                                borderColor: '#1c3e96',
                                backgroundColor: mkGrad('rgba(28,62,150,0.18)', 'rgba(28,62,150,0.01)'),
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: onTime.map(v => v ? 3.5 : 0),
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#1c3e96',
                                yAxisID: 'yCount'
                            },
                            {
                                label: 'En retard',
                                data: lateDays,
                                borderColor: '#f59e0b',
                                backgroundColor: mkGrad('rgba(245,158,11,0.15)', 'rgba(245,158,11,0.01)'),
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: lateDays.map(v => v ? 3.5 : 0),
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#f59e0b',
                                yAxisID: 'yCount'
                            },
                            {
                                label: 'Absents',
                                data: absences,
                                borderColor: '#f43f5e',
                                backgroundColor: mkGrad('rgba(244,63,94,0.15)', 'rgba(244,63,94,0.01)'),
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: absences.map(v => v ? 3.5 : 0),
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#f43f5e',
                                yAxisID: 'yCount'
                            },
                            {
                                label: 'Retard (min)',
                                data: lateMinutes,
                                borderColor: '#f97316',
                                backgroundColor: 'transparent',
                                fill: false,
                                tension: 0.35,
                                borderWidth: 2,
                                borderDash: [5, 4],
                                pointRadius: lateMinutes.map(v => v > 0 ? 3.5 : 0),
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#f97316',
                                yAxisID: 'yMinutes'
                            },
                            {
                                label: 'Heures travaillées',
                                data: workedMinutes,
                                borderColor: '#8b5cf6',
                                backgroundColor: 'transparent',
                                fill: false,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: workedMinutes.map(v => v > 0 ? 3.5 : 0),
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#8b5cf6',
                                yAxisID: 'yMinutes'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 700, easing: 'easeOutQuart' },
                        interaction: { mode: 'index', intersect: false },
                        onClick: openDayFromChart,
                        onHover: chartCursor,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                ...tooltipStyle,
                                callbacks: {
                                    label(ctx) {
                                        const v = ctx.parsed.y;
                                        const lbl = ctx.dataset.label;
                                        if (lbl === 'Retard (min)') return `Retard: ${fmtMin(v)}`;
                                        if (lbl === 'Heures travaillées') return `${lbl}: ${fmtMin(v)}`;
                                        return `${lbl}: ${v}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: gridColor },
                                ticks: { color: mutedColor, font: { size: 10 }, maxRotation: 45, maxTicksLimit: 20 }
                            },
                            yCount: {
                                type: 'linear', position: 'left', beginAtZero: true,
                                title: { display: true, text: 'Personnes', color: mutedColor, font: { size: 10 } },
                                ticks: { color: mutedColor, font: { size: 10 } },
                                grid: { color: gridColor }
                            },
                            yMinutes: {
                                type: 'linear', position: 'right',
                                grid: { drawOnChartArea: false },
                                title: { display: true, text: 'Temps', color: '#f97316', font: { size: 10 } },
                                ticks: { callback: (v) => fmtMin(v), color: '#f97316', font: { size: 10 } }
                            }
                        }
                    }
                });
            }

            /* Chart 2: Bars */
            const ctxOv = document.getElementById('chartOverview');
            if (ctxOv && labels.length > 0) {
                new Chart(ctxOv, {
                    type: 'bar',
                    plugins: [holidayPlugin],
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Heures travaillées',
                                data: workedHours,
                                backgroundColor: '#1c3e96',
                                borderColor: '#1c3e96',
                                borderWidth: 1,
                                borderRadius: 4,
                                barPercentage: 0.6,
                                categoryPercentage: 0.75,
                                yAxisID: 'yHours'
                            },
                            {
                                label: 'Retard (min)',
                                data: lateMinutes,
                                backgroundColor: 'rgba(245,158,11,0.85)',
                                borderColor: '#f59e0b',
                                borderWidth: 1,
                                borderRadius: 4,
                                barPercentage: 0.6,
                                categoryPercentage: 0.75,
                                yAxisID: 'yMin'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 600, easing: 'easeOutQuart' },
                        interaction: { mode: 'index', intersect: false },
                        onClick: openDayFromChart,
                        onHover: chartCursor,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { color: textColor, usePointStyle: true, padding: 12, font: { size: 11 } }
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#131519' : '#fffefa',
                                borderColor: isDark ? '#222630' : '#dcdad2',
                                borderWidth: 1,
                                titleColor: isDark ? '#f8fafc' : '#241f1b',
                                bodyColor: isDark ? '#94a3b8' : '#6e6862',
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label(ctx) {
                                        const v = ctx.parsed.y;
                                        return ctx.dataset.label === 'Heures travaillées'
                                            ? `Heures: ${v}h`
                                            : `Retard: ${fmtMin(v)}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: mutedColor, font: { size: 10 }, maxRotation: 45, maxTicksLimit: 20 }
                            },
                            yHours: {
                                type: 'linear', position: 'left', min: 0,
                                title: { display: true, text: 'Heures', color: '#1c3e96', font: { size: 10 } },
                                ticks: { callback: v => v + 'h', color: '#1c3e96', font: { size: 10 } },
                                grid: { color: gridColor }
                            },
                            yMin: {
                                type: 'linear', position: 'right', min: 0,
                                title: { display: true, text: 'Retard (min)', color: '#f59e0b', font: { size: 10 } },
                                ticks: { callback: v => v + ' min', color: '#f59e0b', font: { size: 10 } },
                                grid: { drawOnChartArea: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>