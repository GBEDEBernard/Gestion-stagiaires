<x-app-layout title="Suivi des Pointages - Supervision">

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

        .ps-pill { font-size: .72rem; font-weight: 500; padding: .2rem .55rem; border-radius: 999px; background: #faf9f6; color: #6e6862; border: 1px solid #dcdad2; display: inline-flex; align-items: center; gap: .25rem; }
        .dark .ps-pill { background: #181c24; color: #94a3b8; border-color: #222630; }
        .ps-pill b { font-weight: 700; color: #241f1b; }
        .dark .ps-pill b { color: #f8fafc; }

        @media (max-width: 639px) {
            .ps-desktop-table { display: none !important; }
            .ps-mobile-list { display: block !important; }
        }
        @media (min-width: 640px) {
            .ps-desktop-table { display: block !important; }
            .ps-mobile-list { display: none !important; }
        }

        /* Dark mode overrides complets */
        .dark .text-slate-900, .dark .text-slate-800, .dark .text-gray-900 { color: #f8fafc !important; }
        .dark .text-slate-600, .dark .text-slate-500, .dark .text-gray-600 { color: #94a3b8 !important; }
        .dark .border-slate-200, .dark .border-slate-300, .dark .border-gray-200 { border-color: #222630 !important; }
        .dark select, .dark input[type="date"], .dark input[type="text"] {
            background-color: #181c24 !important;
            border-color: #222630 !important;
            color: #f8fafc !important;
        }

        @media print {
            body * { visibility: hidden; }
            #print-table, #print-table * { visibility: visible; }
            #print-table {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            nav, header, footer, button, .no-print { display: none !important; }
            #print-table table {
                font-size: 11px;
                border-collapse: collapse;
                width: 100%;
            }
            #print-table th, #print-table td {
                border: 1px solid black;
                padding: 6px 8px;
                color: black !important;
                background: white !important;
            }
            #print-table th {
                background: #f0f0f0 !important;
                font-weight: bold;
            }
        }
    </style>

    <div class="space-y-3 sm:space-y-3.5 overflow-x-hidden">

        {{-- ════════════════════════════════════════════════════════
             1. BANNIÈRE SUIVI DES POINTAGES (STYLE MON STAGE COMPACT)
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <h1 class="text-base sm:text-lg font-bold text-white tracking-tight leading-tight">Suivi des Pointages</h1>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                Temps réel
                            </span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-blue-100/80 dark:text-gray-400 mt-0.5">Émargements géolocalisés, assiduité et écarts d'horaires par collaborateur</p>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <button type="button" onclick="printTable()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 dark:bg-white/5 hover:bg-white/15 border border-white/15 dark:border-[#222630] text-xs font-semibold text-white transition-all whitespace-nowrap"
                                title="Imprimer le rapport détaillé">
                            <svg class="w-3.5 h-3.5 text-blue-200 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Imprimer</span>
                        </button>
                        <a href="{{ route('admin.presence.index') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 dark:bg-white/5 hover:bg-white/15 border border-white/15 dark:border-[#222630] text-xs font-semibold text-white transition-all whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-blue-200 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Supervision</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             2. BARRE DE FILTRES COMPACTE (STYLE MON STAGE)
        ════════════════════════════════════════════════════════ --}}
        <div class="ks-sec p-3 sm:p-3.5">
            <form method="GET" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 xl:grid-cols-8 gap-2.5 sm:gap-3">

                {{-- DATE (cachée si personnalisé) --}}
                <div id="single-date-group" class="col-span-2 sm:col-span-1">
                    <label class="ks-label flex items-center gap-1 mb-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Date
                    </label>
                    <input type="date" name="date" value="{{ request('date', today()->format('Y-m-d')) }}"
                           class="w-full px-2.5 py-1.5 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] focus:border-[#1c3e96] transition">
                </div>

                {{-- DATE FROM --}}
                <div id="date-from-group" class="hidden col-span-1">
                    <label class="ks-label flex items-center gap-1 mb-1">Du</label>
                    <input type="date" name="date_from" value="{{ request('date_from', $dateFrom ?? '') }}"
                           class="w-full px-2.5 py-1.5 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] transition">
                </div>

                {{-- DATE TO --}}
                <div id="date-to-group" class="hidden col-span-1">
                    <label class="ks-label flex items-center gap-1 mb-1">Au</label>
                    <input type="date" name="date_to" value="{{ request('date_to', $dateTo ?? '') }}"
                           class="w-full px-2.5 py-1.5 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] transition">
                </div>

                {{-- PÉRIODE --}}
                <div>
                    <label class="ks-label flex items-center gap-1 mb-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Période
                    </label>
                    <select name="period" id="period-select"
                            class="w-full px-2.5 py-1.5 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] transition">
                        <option value="day" {{ request('period') == 'day' ? 'selected' : '' }}>Jour</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Semaine</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Mois</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                    </select>
                </div>

                {{-- UTILISATEUR --}}
                <div>
                    <label class="ks-label flex items-center gap-1 mb-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Collaborateur
                    </label>
                    <select name="user_id"
                            class="w-full px-2.5 py-1.5 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] transition">
                        <option value="">Tous</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- SITE --}}
                <div>
                    <label class="ks-label flex items-center gap-1 mb-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        Site
                    </label>
                    <select name="site_id"
                            class="w-full px-2.5 py-1.5 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] transition">
                        <option value="">Tous les sites</option>
                        @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                            {{ $site->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- ÉCOLE --}}
                <div>
                    <label class="ks-label flex items-center gap-1 mb-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        </svg>
                        Entité / École
                    </label>
                    <select name="school"
                            class="w-full px-2.5 py-1.5 text-xs rounded-md border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] text-[#241f1b] dark:text-[#f8fafc] focus:ring-1 focus:ring-[#1c3e96] transition">
                        <option value="">Toutes</option>
                        <option value="__employes__" {{ request('school') == '__employes__' ? 'selected' : '' }}>
                            👷 Employés
                        </option>
                        @foreach($schools as $ecole)
                        @if($ecole === '__employes__')
                        @continue
                        @endif
                        <option value="{{ $ecole }}" {{ request('school') == $ecole ? 'selected' : '' }}>
                            {{ $ecole }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- BOUTONS D'ACTION --}}
                <div class="col-span-2 sm:col-span-3 lg:col-span-1 xl:col-span-2 flex items-end gap-1.5 pt-1 sm:pt-0">
                    <button type="submit" class="ks-btn-primary ks-btn-xs flex-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filtrer</span>
                    </button>

                    <a href="{{ route('admin.presence.pointage-suivi') }}"
                       class="ks-btn-secondary ks-btn-xs px-2.5"
                       title="Réinitialiser les filtres">
                        <svg class="w-3.5 h-3.5 text-[#6e6862] dark:text-[#a89f93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        {{-- ════════════════════════════════════════════════════════
             3. CARTES STATS KPIS (STYLE MON STAGE COMPACT)
        ════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
            {{-- KPI 1: Pointages Aujourd'hui --}}
            <div class="ks-sec p-3 flex items-center justify-between">
                <div>
                    <span class="ks-label">Pointages Aujourd'hui</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="ks-num text-xl sm:text-2xl font-bold text-[#241f1b] dark:text-[#f8fafc]">{{ $todayCount ?? 0 }}</span>
                    </div>
                    <span class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 mt-0.5 block">
                        {{ $checkinsToday ?? 0 }} arrivées • {{ $checkoutsToday ?? 0 }} départs
                    </span>
                </div>
                <span class="ks-stat-icon bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>

            {{-- KPI 2: Anomalies Récentes --}}
            <div class="ks-sec p-3 flex items-center justify-between">
                <div>
                    <span class="ks-label">Anomalies Actives</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="ks-num text-xl sm:text-2xl font-bold {{ ($recentAnomalies ?? 0) > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-[#241f1b] dark:text-[#f8fafc]' }}">
                            {{ $recentAnomalies ?? 0 }}
                        </span>
                    </div>
                    <span class="text-[11px] font-medium text-[#6e6862] dark:text-[#a89f93] mt-0.5 block">
                        Sur {{ $periodDays ?? 7 }} jours analysés
                    </span>
                </div>
                <span class="ks-stat-icon {{ ($recentAnomalies ?? 0) > 0 ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800/40' : 'bg-slate-50 text-slate-600 dark:bg-slate-900 dark:text-slate-400 border border-slate-200 dark:border-slate-800' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
            </div>

            {{-- KPI 3: Précision Moyenne GPS --}}
            <div class="ks-sec p-3 flex items-center justify-between">
                <div>
                    <span class="ks-label">Précision Moyenne</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="ks-num text-xl sm:text-2xl font-bold text-[#1c3e96] dark:text-blue-400">
                            {{ number_format($avgAccuracy ?? 0, 0) }}m
                        </span>
                    </div>
                    <span class="text-[11px] font-medium text-[#6e6862] dark:text-[#a89f93] mt-0.5 block">
                        Géolocalisation des pointages
                    </span>
                </div>
                <span class="ks-stat-icon bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400 border border-blue-200 dark:border-blue-800/40">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════
             4. RAPPORT DÉTAILLÉ PAR COLLABORATEUR
        ════════════════════════════════════════════════════════ --}}
        <div class="ks-sec overflow-hidden">
            <div class="ks-sec-head flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="ks-stat-icon !w-5 !h-5 bg-blue-50 text-[#1c3e96] dark:bg-blue-950/40 dark:text-blue-400">
                        <svg class="!w-3 !h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <h2 class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc]">Rapport Détaillé par Collaborateur</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="ps-pill">
                        <b>{{ $detail instanceof \Illuminate\Pagination\LengthAwarePaginator ? $detail->total() : $detail->count() }}</b> collaborateur(s)
                    </span>
                </div>
            </div>

            <div id="print-table" class="p-2.5 sm:p-3.5 space-y-3">
                @forelse($detail as $block)
                @php
                    $u = $block['user'];
                    $t = $block['totals'];
                    $workedHours = round($t['worked_minutes'] / 60, 1);
                @endphp
                <div class="rounded-[8px] border border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] overflow-hidden">
                    {{-- En-tête collaborateur + KPIs individuels --}}
                    <div class="p-2.5 sm:p-3 bg-[#faf9f6] dark:bg-[#181c24] border-b border-[#dcdad2] dark:border-[#222630] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <a href="{{ route('attendance.tracking.user.historique', $u) }}"
                               class="text-xs sm:text-sm font-bold text-[#241f1b] dark:text-[#f8fafc] hover:text-[#1c3e96] dark:hover:text-blue-400 hover:underline transition-colors">
                                {{ $u->name }}
                            </a>
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $block['group'] === 'etudiant' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' }}">
                                {{ $block['group'] === 'etudiant' ? 'Stagiaire' : 'Employé' }}
                            </span>
                            @if($block['school'])
                            <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">🎓 {{ $block['school'] }}</span>
                            @endif
                            @if($block['site_name'])
                            <span class="text-[11px] text-[#6e6862] dark:text-[#a89f93]">📍 {{ $block['site_name'] }}</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <span class="ks-stat-change ks-stat-change--positive">✓ {{ $t['present'] }} présent(s)</span>
                            <span class="ks-stat-change {{ $t['absent'] > 0 ? 'ks-stat-change--negative' : 'ks-stat-change--neutral' }}">✗ {{ $t['absent'] }} absence(s)</span>
                            @if($t['corrected'] > 0)
                            <span class="ks-stat-change ks-stat-change--neutral">◌ {{ $t['corrected'] }} corrigé(s)</span>
                            @endif
                            @if($t['late_minutes'] > 0)
                            <span class="ks-stat-change ks-stat-change--amber">⏱ Retard : {{ formatMinutes($t['late_minutes']) }}</span>
                            @endif
                            <span class="ks-stat-change ks-stat-change--blue">🕒 {{ $workedHours }}h pointées</span>
                        </div>
                    </div>

                    {{-- ── VUE DESKTOP (Tableau épuré) ── --}}
                    <div class="ps-desktop-table overflow-x-auto">
                        <table class="w-full text-left text-xs divide-y divide-[#ecebe3] dark:divide-[#222630]">
                            <thead class="bg-[#faf9f6] dark:bg-[#181c24] text-[#6e6862] dark:text-[#a89f93]">
                                <tr>
                                    <th class="px-3 py-2 font-semibold uppercase tracking-wider text-[10px]">Journée</th>
                                    <th class="px-3 py-2 font-semibold uppercase tracking-wider text-[10px]">Arrivée</th>
                                    <th class="px-3 py-2 font-semibold uppercase tracking-wider text-[10px]">Départ</th>
                                    <th class="px-3 py-2 font-semibold uppercase tracking-wider text-[10px]">Site</th>
                                    <th class="px-3 py-2 font-semibold uppercase tracking-wider text-[10px]">Distance</th>
                                    <th class="px-3 py-2 font-semibold uppercase tracking-wider text-[10px]">Retard</th>
                                    <th class="px-3 py-2 font-semibold uppercase tracking-wider text-[10px]">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#ecebe3] dark:divide-[#222630]">
                                @foreach($block['days'] as $day)
                                <tr class="{{ $day['absent'] ? 'bg-rose-50/40 dark:bg-rose-950/20' : 'hover:bg-[#faf9f6] dark:hover:bg-[#181c24]' }} transition-colors">
                                    <td class="px-3 py-2 font-medium text-[#241f1b] dark:text-[#f8fafc] whitespace-nowrap">
                                        {{ $day['date']->locale('fr')->isoFormat('ddd D MMM') }}
                                        <span class="text-[10px] text-[#a89f93]">{{ $day['date']->format('Y') }}</span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if($day['arrival'])
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $day['arrival'] }}</span>
                                        @else
                                        <span class="text-[#a89f93]">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if($day['departure'])
                                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ $day['departure'] }}</span>
                                        @else
                                        <span class="text-[#a89f93]">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-[#6e6862] dark:text-[#a89f93] whitespace-nowrap">
                                        {{ $day['site_name'] ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-[#6e6862] dark:text-[#a89f93] whitespace-nowrap">
                                        {{ $day['distance'] ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if($day['late_minutes'] > 0)
                                        <span class="font-bold text-rose-600 dark:text-rose-400">-{{ formatMinutes($day['late_minutes']) }}</span>
                                        @else
                                        <span class="text-[#a89f93]">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if($day['status'] === 'on_time')
                                        <span class="ks-stat-change ks-stat-change--positive">À l'heure</span>
                                        @elseif($day['status'] === 'late')
                                        <span class="ks-stat-change ks-stat-change--amber">En retard</span>
                                        @elseif($day['status'] === 'absent')
                                        <span class="ks-stat-change ks-stat-change--negative">Absent</span>
                                        @elseif(!empty($day['is_permission']))
                                        <span class="ks-stat-change ks-stat-change--blue" title="{{ $day['permission_reason'] ?? 'Permission approuvée' }}">Permission</span>
                                        @elseif(($day['departure_status'] ?? null) === 'auto_closed')
                                        <span class="ks-stat-change ks-stat-change--amber" title="Départ non pointé — clôturé d'office">Départ oublié</span>
                                        @else
                                        <span class="ks-stat-change ks-stat-change--neutral">Corrigé</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- ── VUE MOBILE (Cartes compactes < 640px) ── --}}
                    <div class="ps-mobile-list divide-y divide-[#ecebe3] dark:divide-[#222630] p-2 space-y-2">
                        @foreach($block['days'] as $day)
                        <div class="rounded-lg p-2.5 {{ $day['absent'] ? 'bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200/50 dark:border-rose-900/30' : 'bg-[#faf9f6] dark:bg-[#181c24] border border-[#ecebe3] dark:border-[#222630]' }}">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-xs font-bold text-[#241f1b] dark:text-[#f8fafc]">
                                    {{ $day['date']->locale('fr')->isoFormat('ddd D MMM YYYY') }}
                                </span>
                                <div>
                                    @if($day['status'] === 'on_time')
                                    <span class="ks-stat-change ks-stat-change--positive">À l'heure</span>
                                    @elseif($day['status'] === 'late')
                                    <span class="ks-stat-change ks-stat-change--amber">En retard</span>
                                    @elseif($day['status'] === 'absent')
                                    <span class="ks-stat-change ks-stat-change--negative">Absent</span>
                                    @elseif(!empty($day['is_permission']))
                                    <span class="ks-stat-change ks-stat-change--blue">Permission</span>
                                    @elseif(($day['departure_status'] ?? null) === 'auto_closed')
                                    <span class="ks-stat-change ks-stat-change--amber">Départ oublié</span>
                                    @else
                                    <span class="ks-stat-change ks-stat-change--neutral">Corrigé</span>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mb-1.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">Arrivée:</span>
                                    <span class="font-bold {{ $day['arrival'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#a89f93]' }}">
                                        {{ $day['arrival'] ?? '—' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] text-[#6e6862] dark:text-[#a89f93]">Départ:</span>
                                    <span class="font-bold {{ $day['departure'] ? 'text-blue-600 dark:text-blue-400' : 'text-[#a89f93]' }}">
                                        {{ $day['departure'] ?? '—' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-1 text-[11px] text-[#6e6862] dark:text-[#a89f93] pt-1 border-t border-[#ecebe3] dark:border-[#222630]">
                                <span>📍 {{ $day['site_name'] ?? '—' }} @if($day['distance']) ({{ $day['distance'] }}) @endif</span>
                                @if($day['late_minutes'] > 0)
                                <span class="font-semibold text-rose-600 dark:text-rose-400">⏱ -{{ formatMinutes($day['late_minutes']) }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-center py-10 px-4">
                    <span class="ks-stat-icon !w-10 !h-10 mx-auto mb-2 text-[#a89f93] bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630]">
                        <svg class="!w-5 !h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <p class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc]">Aucun pointage ni absence pour ces critères.</p>
                    <p class="text-[11px] text-[#6e6862] dark:text-[#a89f93] mt-0.5">Ajustez la période ou sélectionnez un autre collaborateur / école.</p>
                </div>
                @endforelse
            </div>

            @if(method_exists($detail, 'hasPages') && $detail->hasPages())
            <div class="p-2.5 sm:p-3 bg-[#faf9f6] dark:bg-[#181c24] border-t border-[#dcdad2] dark:border-[#222630]">
                {{ $detail->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function printTable() {
            const params = new URLSearchParams(window.location.search);
            const printUrl = "{{ route('admin.presence.print') }}?" + params.toString();
            window.open(printUrl, '_blank', 'width=1000,height=800');
        }

        function toggleDateFields() {
            const sel = document.getElementById('period-select');
            if (!sel) return;
            const isCustom = sel.value === 'custom';
            document.getElementById('single-date-group')?.classList.toggle('hidden', isCustom);
            document.getElementById('date-from-group')?.classList.toggle('hidden', !isCustom);
            document.getElementById('date-to-group')?.classList.toggle('hidden', !isCustom);
        }

        document.addEventListener('DOMContentLoaded', toggleDateFields);
        document.getElementById('period-select')?.addEventListener('change', toggleDateFields);
    </script>
    @endpush

</x-app-layout>