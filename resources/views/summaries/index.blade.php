<x-app-layout title="Mes Résumés IA">

    <style>
        body { background: #f1f0ed !important; }
        .dark body { background: #1f232d !important; }

        :root {
            --ks-ink: #241f1b;
            --ks-blue: #1c3e96;
            --ks-blue-dark: #162f73;
            --ks-line: #bfbab2;
            --ks-muted: #6e6862;
            --ks-card: #fffefa;
            --ks-card-bd: #dcdad2;
        }

        .ks-num { font-variant-numeric: tabular-nums; }
        .ks-label {
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6e6862;
        }
        .dark .ks-label { color: #a89f93; }

        .ks-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #1c3e96;
            color: #fff;
            border: 1px solid #1c3e96;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 600;
            padding: .4rem .85rem;
            transition: all .15s ease;
        }
        .ks-btn-primary:hover { background: #162f73; border-color: #162f73; }
        .ks-btn-primary:active { transform: scale(.98); }

        .ks-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            background: #fff;
            color: #241f1b;
            border: 1px solid #bfbab2;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 500;
            padding: .4rem .85rem;
            transition: all .15s ease;
        }
        .dark .ks-btn-secondary { background: #131519; color: #fff; border-color: #222630; }
        .ks-btn-secondary:hover { border-color: #1c3e96; color: #1c3e96; }

        .ks-btn-xs {
            min-height: 28px !important;
            padding: .25rem .6rem !important;
            font-size: .75rem !important;
            font-weight: 600;
            border-radius: 6px !important;
            white-space: nowrap;
        }

        .ks-sec {
            background: #fffefa;
            border: 1px solid #dcdad2;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(36,31,27,.04);
            transition: border-color .15s ease;
        }
        .dark .ks-sec {
            background: #131519 !important;
            border-color: #222630 !important;
            box-shadow: none;
        }

        /* KPI cards compactes */
        .ks-stat-card {
            border: 1px solid #dcdad2;
            border-radius: 10px;
            background-color: #fffefa;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .dark .ks-stat-card {
            background: #131519 !important;
            border: 1px solid #222630 !important;
        }
        .ks-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(0,0,0,.05);
        }

        .ks-stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: none;
        }
        .ks-stat-icon svg { width: 16px; height: 16px; stroke-width: 1.8; }

        .ks-stat-card--blue  .ks-stat-icon { background-color: #eef1fb; color: #1c3e96; }
        .ks-stat-card--purple .ks-stat-icon { background-color: #f5f3ff; color: #7c3aed; }
        .ks-stat-card--green .ks-stat-icon { background-color: #ecfdf5; color: #15803d; }
        .ks-stat-card--amber .ks-stat-icon { background-color: #fffbeb; color: #b45309; }

        .dark .ks-stat-card--blue  .ks-stat-icon { background-color: rgba(28, 62, 150, 0.25) !important; color: #93c5fd !important; }
        .dark .ks-stat-card--purple .ks-stat-icon { background-color: rgba(124, 58, 237, 0.25) !important; color: #c4b5fd !important; }
        .dark .ks-stat-card--green .ks-stat-icon { background-color: rgba(22, 101, 52, 0.25) !important; color: #86efac !important; }
        .dark .ks-stat-card--amber .ks-stat-icon { background-color: rgba(180, 83, 9, 0.25) !important; color: #fcd34d !important; }

        @keyframes ks-spin {
            to { transform: rotate(360deg); }
        }
        .ks-animate-spin { animation: ks-spin 1s linear infinite; }
    </style>

    <div class="w-full space-y-3 pb-6" x-data="{ filterPeriod: 'all' }">

        {{-- ── NOTIFICATIONS FLASH ── --}}
        @if(session('success'))
            <div class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold ml-4">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-300 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-xs font-bold ml-4">✕</button>
            </div>
        @endif

        {{-- ── EN-TÊTE COMPACT ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-[#241f1b] dark:text-white flex items-center gap-2">
                    Résumés IA
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-[#eef1fb] dark:bg-blue-950/50 text-[#1c3e96] dark:text-blue-300 border border-[#d8e0f5] dark:border-blue-900/50">
                        {{ $summaries->count() }}
                    </span>
                </h1>
                <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-0.5">
                    Synthèse périodique intelligente générée à partir de vos rapports d'activité quotidienne.
                </p>
            </div>

            {{-- Boutons de génération directe --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-[11px] font-semibold text-[#6e6862] dark:text-slate-400 mr-1 hidden sm:inline">
                    Générer :
                </span>

                <form action="{{ route('summaries.generate') }}" method="POST" class="generate-form inline">
                    @csrf
                    <input type="hidden" name="period" value="weekly">
                    <button type="submit" class="generate-btn ks-btn-primary ks-btn-xs !text-[12px] !px-3 cursor-pointer" title="Générer pour la semaine en cours">
                        <span class="generate-text flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Semaine
                        </span>
                        <span class="generate-spinner hidden">
                            <svg class="ks-animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </span>
                    </button>
                </form>

                <form action="{{ route('summaries.generate') }}" method="POST" class="generate-form inline">
                    @csrf
                    <input type="hidden" name="period" value="monthly">
                    <button type="submit" class="generate-btn ks-btn-secondary ks-btn-xs !text-[12px] !px-3 cursor-pointer hover:!border-purple-500 hover:!text-purple-600" title="Générer pour le mois en cours">
                        <span class="generate-text flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Mois
                        </span>
                        <span class="generate-spinner hidden">
                            <svg class="ks-animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </span>
                    </button>
                </form>

                <form action="{{ route('summaries.generate') }}" method="POST" class="generate-form inline">
                    @csrf
                    <input type="hidden" name="period" value="yearly">
                    <button type="submit" class="generate-btn ks-btn-secondary ks-btn-xs !text-[12px] !px-3 cursor-pointer hover:!border-emerald-500 hover:!text-emerald-600" title="Générer pour l'année en cours">
                        <span class="generate-text flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Année
                        </span>
                        <span class="generate-spinner hidden">
                            <svg class="ks-animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </span>
                    </button>
                </form>
            </div>
        </div>

        {{-- ── 4 CARTES KPI COMPACTES ── --}}
        @php
            $countTotal = $summaries->count();
            $countWeekly = $summaries->where('period_type', 'weekly')->count();
            $countMonthly = $summaries->where('period_type', 'monthly')->count();
            $countYearly = $summaries->where('period_type', 'yearly')->count();
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            {{-- Total --}}
            <div class="ks-stat-card ks-stat-card--blue p-3">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="ks-label">Total résumés</span>
                    <div class="ks-stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-[#241f1b] dark:text-white ks-num">
                    {{ $countTotal }}
                </div>
                <p class="text-[11px] text-[#6e6862] dark:text-slate-400 mt-0.5">Synthèses archivées</p>
            </div>

            {{-- Hebdomadaire --}}
            <div class="ks-stat-card ks-stat-card--blue p-3">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="ks-label">Hebdomadaires</span>
                    <div class="ks-stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-[#1c3e96] dark:text-blue-300 ks-num">
                    {{ $countWeekly }}
                </div>
                <p class="text-[11px] text-[#6e6862] dark:text-slate-400 mt-0.5">Points de fin de semaine</p>
            </div>

            {{-- Mensuel --}}
            <div class="ks-stat-card ks-stat-card--purple p-3">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="ks-label">Mensuels</span>
                    <div class="ks-stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-purple-700 dark:text-purple-300 ks-num">
                    {{ $countMonthly }}
                </div>
                <p class="text-[11px] text-[#6e6862] dark:text-slate-400 mt-0.5">Bilan mensuel complet</p>
            </div>

            {{-- Annuel --}}
            <div class="ks-stat-card ks-stat-card--green p-3">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="ks-label">Annuels</span>
                    <div class="ks-stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-emerald-700 dark:text-emerald-300 ks-num">
                    {{ $countYearly }}
                </div>
                <p class="text-[11px] text-[#6e6862] dark:text-slate-400 mt-0.5">Rapports d'activité annuels</p>
            </div>
        </div>

        {{-- ── BARRE D'ONGLETS COMPACTE ── --}}
        <div class="ks-sec p-1.5 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-1">
                <button type="button" @click="filterPeriod = 'all'"
                    :class="filterPeriod === 'all' ? 'bg-[#1c3e96] text-white shadow-sm' : 'text-[#6e6862] dark:text-slate-300 hover:bg-[#ecebe3] dark:hover:bg-[#1f242e]'"
                    class="px-2.5 py-1 text-xs font-semibold rounded transition cursor-pointer">
                    Tous <span class="ml-1 opacity-75">({{ $countTotal }})</span>
                </button>
                <button type="button" @click="filterPeriod = 'weekly'"
                    :class="filterPeriod === 'weekly' ? 'bg-[#1c3e96] text-white shadow-sm' : 'text-[#6e6862] dark:text-slate-300 hover:bg-[#ecebe3] dark:hover:bg-[#1f242e]'"
                    class="px-2.5 py-1 text-xs font-semibold rounded transition cursor-pointer">
                    Hebdomadaires <span class="ml-1 opacity-75">({{ $countWeekly }})</span>
                </button>
                <button type="button" @click="filterPeriod = 'monthly'"
                    :class="filterPeriod === 'monthly' ? 'bg-[#1c3e96] text-white shadow-sm' : 'text-[#6e6862] dark:text-slate-300 hover:bg-[#ecebe3] dark:hover:bg-[#1f242e]'"
                    class="px-2.5 py-1 text-xs font-semibold rounded transition cursor-pointer">
                    Mensuels <span class="ml-1 opacity-75">({{ $countMonthly }})</span>
                </button>
                <button type="button" @click="filterPeriod = 'yearly'"
                    :class="filterPeriod === 'yearly' ? 'bg-[#1c3e96] text-white shadow-sm' : 'text-[#6e6862] dark:text-slate-300 hover:bg-[#ecebe3] dark:hover:bg-[#1f242e]'"
                    class="px-2.5 py-1 text-xs font-semibold rounded transition cursor-pointer">
                    Annuels <span class="ml-1 opacity-75">({{ $countYearly }})</span>
                </button>
            </div>

            <div class="text-[11px] text-[#6e6862] dark:text-slate-400 px-2 font-mono hidden sm:block">
                Modèle actif : gpt-4o-mini
            </div>
        </div>

        {{-- ── LISTE DES RÉSUMÉS ── --}}
        @if($summaries->isEmpty())
            {{-- État vide --}}
            <div class="ks-sec p-8 sm:p-12 text-center space-y-3">
                <div class="w-12 h-12 mx-auto rounded-full bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#241f1b] dark:text-white">Aucun résumé généré pour le moment</h3>
                    <p class="text-xs text-[#6e6862] dark:text-slate-400 mt-1 max-w-md mx-auto">
                        Générez un résumé IA automatique à partir de vos rapports journaliers pour obtenir une synthèse claire de votre avancement.
                    </p>
                </div>
                <div class="pt-2">
                    <form action="{{ route('summaries.generate') }}" method="POST" class="generate-form inline">
                        @csrf
                        <input type="hidden" name="period" value="weekly">
                        <button type="submit" class="generate-btn ks-btn-primary ks-btn-xs !text-[12px] !px-4 cursor-pointer">
                            <span class="generate-text">Générer le résumé de cette semaine</span>
                            <span class="generate-spinner hidden">
                                <svg class="ks-animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="space-y-2.5">
                @foreach($summaries as $summary)
                    @php
                        $badgeBg = match($summary->period_type) {
                            'weekly' => 'bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 border-[#d8e0f5] dark:border-blue-900/50',
                            'monthly' => 'bg-[#f5f3ff] dark:bg-purple-950/40 text-[#6d28d9] dark:text-purple-300 border-[#e9d5ff] dark:border-purple-900/50',
                            'yearly' => 'bg-[#f0fdf4] dark:bg-emerald-950/40 text-[#15803d] dark:text-emerald-300 border-[#bbf7d0] dark:border-emerald-900/50',
                            default => 'bg-[#eef1fb] dark:bg-blue-950/40 text-[#1c3e96] dark:text-blue-300 border-[#d8e0f5] dark:border-blue-900/50',
                        };

                        $reportCount = is_array($summary->raw_data) ? count($summary->raw_data) : 0;
                        $totalHours = is_array($summary->raw_data) ? collect($summary->raw_data)->sum('hours_declared') : 0;
                    @endphp

                    <div x-show="filterPeriod === 'all' || filterPeriod === '{{ $summary->period_type }}'"
                         x-transition
                         class="ks-sec p-3 sm:p-4 hover:border-[#1c3e96] dark:hover:border-blue-700/60 transition group">

                        {{-- En-tête de carte --}}
                        <div class="flex items-start justify-between gap-3 flex-wrap sm:flex-nowrap mb-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border uppercase tracking-wider {{ $badgeBg }}">
                                    {{ ucfirst(__($summary->period_type)) }}
                                </span>

                                <span class="text-xs font-semibold text-[#241f1b] dark:text-white flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#6e6862] dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $summary->period_start->format('d/m/Y') }} → {{ $summary->period_end->format('d/m/Y') }}
                                </span>

                                <span class="text-[11px] text-[#6e6862] dark:text-slate-400 font-mono">
                                    · Généré le {{ $summary->generated_at?->format('d/m/Y à H:i') }}
                                </span>

                                @if($summary->model_used)
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-mono bg-[#f1f0ed] dark:bg-[#1a1e27] text-[#6e6862] dark:text-slate-400 border border-[#dcdad2] dark:border-[#2b3240]">
                                        {{ $summary->model_used }}
                                    </span>
                                @endif
                            </div>

                            {{-- Actions rapides --}}
                            <div class="flex items-center gap-1.5 shrink-0 ml-auto">
                                <a href="{{ route('summaries.show', $summary) }}"
                                   class="ks-btn-secondary ks-btn-xs !h-7 !px-2.5 cursor-pointer group-hover:border-[#1c3e96]">
                                    <span>Consulter</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>

                                <form action="{{ route('summaries.destroy', $summary) }}" method="POST"
                                      onsubmit="return confirm('Confirmer la suppression de ce résumé ?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="ks-btn-secondary ks-btn-xs !h-7 !px-2 text-rose-600 dark:text-rose-400 hover:border-rose-400 cursor-pointer"
                                            title="Supprimer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Extrait du résumé --}}
                        <div class="text-xs text-[#4b5563] dark:text-slate-300 leading-relaxed line-clamp-2 pl-0.5">
                            {{ Str::limit(strip_tags($summary->summary), 240) }}
                        </div>

                        {{-- Bandeau bas avec badges de données --}}
                        @if($reportCount > 0 || $totalHours > 0)
                            <div class="mt-2.5 pt-2 border-t border-[#ecebe3] dark:border-[#222630] flex items-center gap-3 text-[11px] text-[#6e6862] dark:text-slate-400 font-mono">
                                <span>📄 {{ $reportCount }} rapport{{ $reportCount > 1 ? 's' : '' }} analysé{{ $reportCount > 1 ? 's' : '' }}</span>
                                @if($totalHours > 0)
                                    <span>⏱️ {{ $totalHours }}h de travail enregistrées</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <script>
        document.querySelectorAll('.generate-form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('.generate-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.querySelector('.generate-text')?.classList.add('hidden');
                    btn.querySelector('.generate-spinner')?.classList.remove('hidden');
                }
            });
        });
    </script>
</x-app-layout>