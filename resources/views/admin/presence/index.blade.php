<x-app-layout title="Statistiques de Présence - Admin">

    <x-slot name="header">
        <div class="pres-header">
            <div class="pres-header-left">
                <div class="pres-header-badge">Administration</div>
                <h1 class="pres-header-title">Tableau de Bord Présence</h1>
                <p class="pres-header-sub">Vue temps réel · Étudiants & Employés</p>
            </div>
            <div class="pres-header-actions">
                <div class="pres-live-dot"><span></span>Live</div>
                <a href="{{ route('admin.presence.export') }}" class="pres-btn-export">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Exporter CSV
                </a>
            </div>
        </div>
    </x-slot>

    {{-- ─── STYLES ─────────────────────────────────────────────── --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

        :root {
            --bg: #0f1117;
            --bg2: #161b27;
            --bg3: #1c2333;
            --border: rgba(255, 255, 255, 0.07);
            --border-hi: rgba(255, 255, 255, 0.14);
            --text: #e8eaf0;
            --muted: #6b7280;
            --emerald: #10b981;
            --emerald-d: #059669;
            --amber: #f59e0b;
            --rose: #f43f5e;
            --blue: #3b82f6;
            --indigo: #6366f1;
            --violet: #8b5cf6;
            --font: 'DM Sans', sans-serif;
            --mono: 'DM Mono', monospace;
        }

        /* ── BASE ── */
        .pres-wrap * {
            box-sizing: border-box;
        }

        .pres-wrap {
            font-family: var(--font);
            color: var(--text);
        }

        /* ── HEADER ── */
        .pres-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .pres-header-badge {
            display: inline-block;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--emerald);
            background: rgba(16, 185, 129, .12);
            border: 1px solid rgba(16, 185, 129, .25);
            border-radius: 999px;
            padding: .25rem .75rem;
            margin-bottom: .5rem;
        }

        .pres-header-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
            line-height: 1.2;
        }

        .pres-header-sub {
            font-size: .9rem;
            color: var(--muted);
            margin: .25rem 0 0;
        }

        .pres-header-actions {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .pres-live-dot {
            display: flex;
            align-items: center;
            gap: .45rem;
            font-size: .8rem;
            font-weight: 500;
            color: var(--emerald);
            background: rgba(16, 185, 129, .1);
            border: 1px solid rgba(16, 185, 129, .2);
            border-radius: 999px;
            padding: .35rem .85rem;
        }

        .pres-live-dot span {
            width: 8px;
            height: 8px;
            background: var(--emerald);
            border-radius: 50%;
            animation: pulse-dot 1.8s ease-in-out infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, .6);
            }

            50% {
                box-shadow: 0 0 0 5px rgba(16, 185, 129, 0);
            }
        }

        .pres-btn-export {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .55rem 1.2rem;
            background: var(--emerald);
            color: #fff;
            font-size: .85rem;
            font-weight: 600;
            border-radius: .6rem;
            text-decoration: none;
            transition: all .2s;
        }

        .pres-btn-export:hover {
            background: var(--emerald-d);
            transform: translateY(-1px);
        }

        /* ── LAYOUT ── */
        .pres-page {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem 1.5rem 3rem;
        }

        /* ── PERIOD TABS ── */
        .pres-tabs {
            display: flex;
            gap: .25rem;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: .75rem;
            padding: .3rem;
            width: fit-content;
        }

        .pres-tab {
            padding: .45rem 1.1rem;
            border-radius: .5rem;
            font-size: .83rem;
            font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            transition: all .2s;
        }

        .pres-tab:hover {
            color: var(--text);
            background: var(--bg3);
        }

        .pres-tab.active {
            background: var(--bg3);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .4);
        }

        .pres-tab-indicator {
            display: inline-block;
            width: 6px;
            height: 6px;
            background: var(--emerald);
            border-radius: 50%;
            margin-left: .4rem;
            vertical-align: middle;
        }

        /* ── KPI GRID ── */
        .pres-kpis {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        @media(max-width:900px) {
            .pres-kpis {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:500px) {
            .pres-kpis {
                grid-template-columns: 1fr;
            }
        }

        .pres-kpi {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.4rem 1.5rem;
            position: relative;
            overflow: hidden;
            transition: border-color .2s, transform .2s;
        }

        .pres-kpi:hover {
            border-color: var(--border-hi);
            transform: translateY(-2px);
        }

        .pres-kpi-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            border-radius: 1rem 1rem 0 0;
        }

        .pres-kpi-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: .75rem;
        }

        .pres-kpi-icon {
            width: 38px;
            height: 38px;
            border-radius: .6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .pres-kpi-badge {
            font-size: .72rem;
            font-weight: 600;
            padding: .2rem .6rem;
            border-radius: 999px;
        }

        .pres-kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            line-height: 1;
            margin-bottom: .3rem;
            font-family: var(--mono);
        }

        .pres-kpi-label {
            font-size: .82rem;
            color: var(--muted);
        }

        .pres-kpi-sub {
            font-size: .75rem;
            color: var(--muted);
            margin-top: .5rem;
            padding-top: .5rem;
            border-top: 1px solid var(--border);
        }

        /* KPI color variants */
        .kpi-emerald .pres-kpi-accent {
            background: var(--emerald);
        }

        .kpi-emerald .pres-kpi-icon {
            background: rgba(16, 185, 129, .12);
            color: var(--emerald);
        }

        .kpi-emerald .pres-kpi-badge {
            background: rgba(16, 185, 129, .12);
            color: var(--emerald);
        }

        .kpi-amber .pres-kpi-accent {
            background: var(--amber);
        }

        .kpi-amber .pres-kpi-icon {
            background: rgba(245, 158, 11, .12);
            color: var(--amber);
        }

        .kpi-amber .pres-kpi-badge {
            background: rgba(245, 158, 11, .12);
            color: var(--amber);
        }

        .kpi-blue .pres-kpi-accent {
            background: var(--blue);
        }

        .kpi-blue .pres-kpi-icon {
            background: rgba(59, 130, 246, .12);
            color: var(--blue);
        }

        .kpi-blue .pres-kpi-badge {
            background: rgba(59, 130, 246, .12);
            color: var(--blue);
        }

        .kpi-rose .pres-kpi-accent {
            background: var(--rose);
        }

        .kpi-rose .pres-kpi-icon {
            background: rgba(244, 63, 94, .12);
            color: var(--rose);
        }

        .kpi-rose .pres-kpi-badge {
            background: rgba(244, 63, 94, .12);
            color: var(--rose);
        }

        /* ── SECTION TITLE ── */
        .pres-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            margin: 0 0 1rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .pres-section-title::before {
            content: '';
            display: inline-block;
            width: 3px;
            height: 16px;
            border-radius: 99px;
            background: var(--emerald);
        }

        /* ── CHARTS AREA ── */
        .pres-charts-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 1rem;
        }

        @media(max-width:900px) {
            .pres-charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .pres-charts-triple {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        @media(max-width:900px) {
            .pres-charts-triple {
                grid-template-columns: 1fr;
            }
        }

        .pres-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: border-color .2s;
        }

        .pres-card:hover {
            border-color: var(--border-hi);
        }

        .pres-filter-panel {
            margin-top: 1rem;
            padding: 1rem 1rem 0;
            background: rgba(255, 255, 255, .92);
            border: 1px solid rgba(0, 0, 0, .08);
            border-radius: 1rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            align-items: end;
            color: #111;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .08);
        }

        .pres-filter-field {
            display: grid;
            gap: .5rem;
            font-size: .85rem;
            color: #374151;
        }

        .pres-filter-input {
            width: 100%;
            border: 1px solid rgba(148, 163, 184, .35);
            border-radius: .75rem;
            background: #ffffff;
            color: #111;
            padding: .85rem 1rem;
        }

        .pres-filter-input::placeholder {
            color: #94a3b8;
        }

        .pres-filter-actions {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .pres-btn-secondary {
            background: #f8fafc;
            border-color: rgba(148, 163, 184, .35);
            color: #111;
        }

        .pres-btn-secondary:hover {
            background: #eef2ff;
        }

        .dark .pres-filter-panel {
            background: rgba(255, 255, 255, .06);
            border-color: rgba(255, 255, 255, .12);
            color: var(--muted);
            box-shadow: none;
        }

        .dark .pres-filter-field {
            color: var(--muted);
        }

        .dark .pres-filter-input {
            background: rgba(255, 255, 255, .08);
            border-color: rgba(255, 255, 255, .14);
            color: #fff;
        }

        .dark .pres-filter-input::placeholder {
            color: rgba(229, 231, 235, .8);
        }

        .dark .pres-btn-secondary {
            background: transparent;
            border-color: rgba(255, 255, 255, .12);
            color: #e5e7eb;
        }

        .dark .pres-btn-secondary:hover {
            background: rgba(255, 255, 255, .06);
        }

        .pres-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .85rem 1.1rem;
            border-radius: .85rem;
            border: 1px solid transparent;
            font-weight: 600;
            font-size: .85rem;
            text-decoration: none;
        }

        .pres-btn-primary {
            background: var(--emerald);
            color: #fff;
        }

        .pres-btn-secondary {
            background: transparent;
            border-color: rgba(255, 255, 255, .12);
            color: #e5e7eb;
        }

        .pres-btn-secondary:hover {
            background: rgba(255, 255, 255, .06);
        }

        .pres-chart-wrap {
            position: relative;
            height: 220px;
        }

        /* ── QUICK ACTIONS ── */
        .pres-actions-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        @media(max-width:700px) {
            .pres-actions-grid {
                grid-template-columns: 1fr;
            }
        }

        .pres-action-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            padding: 1.4rem 1rem;
            border-radius: 1rem;
            text-decoration: none;
            font-weight: 600;
            font-size: .9rem;
            text-align: center;
            transition: all .25s;
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }

        .pres-action-card::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity .25s;
        }

        .pres-action-card:hover {
            transform: translateY(-3px);
        }

        .pres-action-card:hover::before {
            opacity: .08;
        }

        .pres-action-icon {
            font-size: 1.6rem;
        }

        .pres-action-count {
            font-size: 1.3rem;
            font-weight: 700;
            font-family: var(--mono);
        }

        .act-rose {
            background: linear-gradient(135deg, rgba(244, 63, 94, .18), rgba(244, 63, 94, .08));
            border-color: rgba(244, 63, 94, .25);
            color: var(--rose);
        }

        .act-rose::before {
            background: var(--rose);
        }

        .act-emerald {
            background: linear-gradient(135deg, rgba(16, 185, 129, .18), rgba(16, 185, 129, .08));
            border-color: rgba(16, 185, 129, .25);
            color: var(--emerald);
        }

        .act-emerald::before {
            background: var(--emerald);
        }

        .act-blue {
            background: linear-gradient(135deg, rgba(59, 130, 246, .18), rgba(59, 130, 246, .08));
            border-color: rgba(59, 130, 246, .25);
            color: var(--blue);
        }

        .act-blue::before {
            background: var(--blue);
        }

        /* ── GROUPS ── */
        .pres-groups-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media(max-width:600px) {
            .pres-groups-grid {
                grid-template-columns: 1fr;
            }
        }

        .pres-group-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.4rem;
        }

        .pres-group-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .pres-group-label {
            font-size: .95rem;
            font-weight: 600;
            color: #fff;
        }

        .pres-group-role {
            font-size: .75rem;
            color: var(--muted);
            margin-top: .15rem;
        }

        .pres-group-count {
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            font-family: var(--mono);
        }

        .pres-group-denom {
            font-size: .9rem;
            color: var(--muted);
            font-family: var(--mono);
        }

        .pres-group-avg {
            font-size: .78rem;
            color: var(--muted);
            margin-bottom: .65rem;
        }

        .pres-progress-bg {
            height: 6px;
            background: rgba(255, 255, 255, .07);
            border-radius: 99px;
            overflow: hidden;
        }

        .pres-progress-fill {
            height: 100%;
            border-radius: 99px;
            transition: width .8s cubic-bezier(.34, 1.56, .64, 1);
        }

        /* ── TABLES ── */
        .pres-table-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1rem;
            overflow: hidden;
        }

        .pres-table-head {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pres-table-head-title {
            font-size: .92rem;
            font-weight: 600;
            color: #fff;
        }

        .pres-table-head-meta {
            font-size: .75rem;
            color: var(--muted);
        }

        table.pres-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.pres-table th {
            padding: .65rem 1.2rem;
            text-align: left;
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            background: rgba(255, 255, 255, .02);
            border-bottom: 1px solid var(--border);
        }

        table.pres-table td {
            padding: .85rem 1.2rem;
            font-size: .85rem;
            color: var(--text);
            border-bottom: 1px solid rgba(255, 255, 255, .04);
        }

        table.pres-table tbody tr:last-child td {
            border-bottom: none;
        }

        table.pres-table tbody tr:hover td {
            background: rgba(255, 255, 255, .02);
        }

        .pres-rank {
            font-size: .75rem;
            font-weight: 700;
            font-family: var(--mono);
            color: var(--muted);
        }

        .pres-rank-1 {
            color: var(--amber);
        }

        .pres-rank-2 {
            color: #9ca3af;
        }

        .pres-rank-3 {
            color: #92400e;
        }

        .pres-tag {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .65rem;
            border-radius: 999px;
            font-size: .73rem;
            font-weight: 600;
        }

        .tag-amber {
            background: rgba(245, 158, 11, .12);
            color: var(--amber);
            border: 1px solid rgba(245, 158, 11, .2);
        }

        .tag-rose {
            background: rgba(244, 63, 94, .12);
            color: var(--rose);
            border: 1px solid rgba(244, 63, 94, .2);
        }

        .tag-emerald {
            background: rgba(16, 185, 129, .12);
            color: var(--emerald);
            border: 1px solid rgba(16, 185, 129, .2);
        }

        .pres-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--muted);
            font-size: .85rem;
        }

        .pres-empty-icon {
            font-size: 2rem;
            margin-bottom: .5rem;
        }

        /* ── REPORTS SECTION ── */
        .pres-reports-kpis {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        @media(max-width:900px) {
            .pres-reports-kpis {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .pres-reports-bottom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media(max-width:700px) {
            .pres-reports-bottom {
                grid-template-columns: 1fr;
            }
        }

        .pres-tips {
            background: rgba(16, 185, 129, .06);
            border: 1px solid rgba(16, 185, 129, .15);
            border-radius: .85rem;
            padding: 1.2rem 1.4rem;
        }

        .pres-tips-title {
            font-size: .87rem;
            font-weight: 700;
            color: var(--emerald);
            margin-bottom: .6rem;
        }

        .pres-tips ul {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: .4rem;
        }

        .pres-tips li {
            font-size: .8rem;
            color: #6ee7b7;
            display: flex;
            align-items: flex-start;
            gap: .5rem;
        }

        .pres-tips li::before {
            content: '›';
            color: var(--emerald);
            font-weight: 700;
            flex-shrink: 0;
        }

        /* ── SPACING ── */
        .mt-6 {
            margin-top: 1.5rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }
    </style>

    <div class="pres-wrap">
        <div class="pres-page">

            {{-- ── PERIOD TABS ──────────────────────────────────── --}}
            <div class="pres-tabs">
                @foreach(['today'=>"Aujourd'hui",'week'=>'Semaine','month'=>'Mois','year'=>'Année'] as $k=>$lbl)
                <a href="?period={{ $k }}" class="pres-tab {{ request('period',$k==='today'?'today':null)===$k?'active':'' }}">
                    {{ $lbl }}
                    @if(request('period')===$k)<span class="pres-tab-indicator"></span>@endif
                </a>
                @endforeach
            </div>

            <div class="pres-filter-panel text-gray-600">
                <form method="GET" action="{{ route('admin.presence.index') }}" class="pres-filter-form flex items-end gap-4 w-full">
                    <input type="hidden" name="period" value="custom" />
                    <input type="hidden" name="group" value="{{ $group }}" />

                    <label class="pres-filter-field" text-gray-600>
                        <span>Du</span>
                        <input type="date" name="date_from" value="{{ request('date_from', today()->format('Y-m-d')) }}" class="pres-filter-input" />
                    </label>

                    <label class="pres-filter-field">
                        <span>Au</span>
                        <input type="date" name="date_to" value="{{ request('date_to', today()->format('Y-m-d')) }}" class="pres-filter-input" />
                    </label>

                    <div class=" flex  bg-slate-900">
                        <button type="submit" class="pres-btn pres-btn-primary">Afficher</button>
                        <a href="{{ route('admin.presence.index') }}" class="pres-btn pres-btn-secondary">Réinitialiser</a>
                    </div>
                </form>
            </div>

            {{-- ── KPI CARDS ────────────────────────────────────── --}}
            <div class="pres-kpis mt-6">

                {{-- Taux présence --}}
                <div class="pres-kpi kpi-emerald">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                                <path d="M9 16l2 2 4-4" />
                            </svg>
                        </div>
                        <span class="pres-kpi-badge">↑ {{ $globalStats['present_days'] ?? 0 }}/{{ $globalStats['total_days'] ?? 0 }}</span>
                    </div>
                    <div class="pres-kpi-value">{{ $globalStats['taux_presence'] ?? 0 }}%</div>
                    <div class="pres-kpi-label">Taux de Présence</div>
                    <div class="pres-kpi-sub">{{ $globalStats['present_days'] ?? 0 }} jours présents</div>
                </div>

                {{-- Retards --}}
                <div class="pres-kpi kpi-amber">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <span class="pres-kpi-badge">{{ $globalStats['total_late_days'] ?? 0 }} jours</span>
                    </div>
                    <div class="pres-kpi-value">{{ round(($globalStats['total_late_minutes'] ?? 0) / 60, 1) }}h</div>
                    <div class="pres-kpi-label">Retards Cumulés</div>
                    <div class="pres-kpi-sub">{{ number_format($globalStats['total_late_minutes'] ?? 0) }} min au total</div>
                </div>

                {{-- Heures travaillées --}}
                <div class="pres-kpi kpi-blue">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 3h-8a2 2 0 00-2 2v2h12V5a2 2 0 00-2-2z" />
                            </svg>
                        </div>
                        <span class="pres-kpi-badge">+{{ $globalStats['total_worked_hours'] ?? 0 }}h</span>
                    </div>
                    <div class="pres-kpi-value">{{ $globalStats['total_worked_hours'] ?? 0 }}h</div>
                    <div class="pres-kpi-label">Heures Travaillées</div>
                    <div class="pres-kpi-sub">{{ number_format($globalStats['total_days'] ?? 0) }} jours pointés</div>
                </div>

                {{-- Anomalies --}}
                <div class="pres-kpi kpi-rose">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                <line x1="12" y1="9" x2="12" y2="13" />
                                <line x1="12" y1="17" x2="12.01" y2="17" />
                            </svg>
                        </div>
                        <span class="pres-kpi-badge">‑{{ \App\Models\AttendanceAnomaly::where('status','resolved')->whereDate('reviewed_at',today())->count() }} résolues</span>
                    </div>
                    <div class="pres-kpi-value">{{ $globalStats['total_anomalies'] ?? 0 }}</div>
                    <div class="pres-kpi-label">Anomalies Ouvertes</div>
                    <div class="pres-kpi-sub">À reviewer</div>
                </div>

            </div>

            {{-- ── QUICK ACTIONS ─────────────────────────────────── --}}
            <div class="mt-6">
                <div class="pres-section-title">Actions Rapides</div>
                <div class="pres-actions-grid">
                    <a href="{{ route('admin.presence.anomalies') }}" class="pres-action-card act-rose">
                        <span class="pres-action-icon">🚨</span>
                        <span class="pres-action-count">{{ $globalStats['total_anomalies'] ?? 0 }}</span>
                        <span>Anomalies</span>
                    </a>
                    <a href="{{ route('admin.presence.pointage-suivi') }}" class="pres-action-card act-emerald">
                        <span class="pres-action-icon">📍</span>
                        <span>Suivi Pointage</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="pres-action-card act-blue">
                        <span class="pres-action-icon">📋</span>
                        <span>Rapports</span>
                    </a>
                </div>
            </div>

            <div class="mt-6">
                <div class="pres-section-title">Évolution · Présence & Ponctualité</div>

                <div class="pres-card">
                    <div class="pres-chart-wrap" style="height:280px;">
                        <canvas id="chartGlobal"></canvas>
                    </div>
                </div>
            </div>

            <div class="pres-card mt-6">
                <div class="pres-section-title">Vue d'Ensemble Quotidienne</div>
                <div style="position:relative;height:280px;">
                    <canvas id="chartOverview"></canvas>
                </div>
            </div>
            {{-- Groups --}}
            <div style="display:flex;flex-direction:column;gap:1rem; margin-top:1rem;">

                <div class="pres-group-card">
                    <div class="pres-group-head">
                        <div>
                            <div class="pres-group-label">👥 Étudiants</div>
                            <div class="pres-group-role">Stagiaires actifs</div>
                        </div>
                        <div style="text-align:right;">
                            <span class="pres-group-count">{{ $groupStats['etudiants']['present'] ?? 0 }}</span>
                            <span class="pres-group-denom">/{{ $groupStats['etudiants']['count'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="pres-group-avg">Moy. {{ $groupStats['etudiants']['avg_worked_hours'] ?? 0 }}h/jour</div>
                    <div class="pres-progress-bg">
                        <div class="pres-progress-fill" style="width:{{ ($groupStats['etudiants']['count']??0)>0 ? round(($groupStats['etudiants']['present']??0)/($groupStats['etudiants']['count']??1)*100) : 0 }}%;background:var(--emerald);"></div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;margin-top:.4rem;font-size:.72rem;color:var(--muted);font-family:var(--mono);">
                        {{ ($groupStats['etudiants']['count']??0)>0 ? round(($groupStats['etudiants']['present']??0)/($groupStats['etudiants']['count']??1)*100) : 0 }}%
                    </div>
                </div>

                <div class="pres-group-card">
                    <div class="pres-group-head">
                        <div>
                            <div class="pres-group-label">👔 Employés</div>
                            <div class="pres-group-role">Personnel actif</div>
                        </div>
                        <div style="text-align:right;">
                            <span class="pres-group-count">{{ $groupStats['employes']['present'] ?? 0 }}</span>
                            <span class="pres-group-denom">/{{ $groupStats['employes']['count'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="pres-group-avg">Moy. {{ $groupStats['employes']['avg_worked_hours'] ?? 0 }}h/jour</div>
                    <div class="pres-progress-bg">
                        <div class="pres-progress-fill" style="width:{{ ($groupStats['employes']['count']??0)>0 ? round(($groupStats['employes']['present']??0)/($groupStats['employes']['count']??1)*100) : 0 }}%;background:var(--blue);"></div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;margin-top:.4rem;font-size:.72rem;color:var(--muted);font-family:var(--mono);">
                        {{ ($groupStats['employes']['count']??0)>0 ? round(($groupStats['employes']['present']??0)/($groupStats['employes']['count']??1)*100) : 0 }}%
                    </div>
                </div>

            </div>
        </div>

        {{-- ── REPORTS SECTION ───────────────────────────────── --}}
        <div class="mt-6">
            <div class="pres-section-title">Suivi des Rapports Journaliers</div>
            <div class="pres-reports-kpis">
                <div class="pres-kpi" style="background:var(--bg2);border-color:var(--border);">
                    <div class="pres-kpi-accent" style="background:var(--muted);"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon" style="background:rgba(107,114,128,.12);color:var(--muted);">✏️</div>
                    </div>
                    <div class="pres-kpi-value" style="font-size:1.8rem;">12</div>
                    <div class="pres-kpi-label">Brouillons</div>
                    <div class="pres-kpi-sub">À compléter aujourd'hui</div>
                </div>
                <div class="pres-kpi kpi-amber">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">⏳</div>
                    </div>
                    <div class="pres-kpi-value" style="font-size:1.8rem;">8</div>
                    <div class="pres-kpi-label">En Attente</div>
                    <div class="pres-kpi-sub">À reviewer par sup.</div>
                </div>
                <div class="pres-kpi kpi-emerald">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">✅</div>
                    </div>
                    <div class="pres-kpi-value" style="font-size:1.8rem;">45</div>
                    <div class="pres-kpi-label">Approuvés</div>
                    <div class="pres-kpi-sub">Cette semaine</div>
                </div>
                <div class="pres-kpi kpi-blue">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">🏆</div>
                    </div>
                    <div class="pres-kpi-value" style="font-size:1.8rem;">92%</div>
                    <div class="pres-kpi-label">Taux Validation</div>
                    <div class="pres-kpi-sub">Objectif › 90%</div>
                </div>
            </div>
            <div class="pres-reports-bottom">
                <a href="{{ route('reports.index') }}" class="pres-action-card act-blue" style="padding:1.6rem;">
                    <span class="pres-action-icon">📊</span>
                    <span style="font-size:1rem;">Tous les Rapports</span>
                </a>
                <div class="pres-tips">
                    <div class="pres-tips-title">💡 Astuces Suivi</div>
                    <ul>
                        <li>Vérifiez la géolocalisation avant validation</li>
                        <li>Signalez toute anomalie de +15 min de retard</li>
                        <li>Validez avant 18h pour les stats du jour</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ── TOP RETARDS & ABSENCES ────────────────────────── --}}
        <div class="pres-charts-grid mt-6">

            {{-- Top 10 Retards --}}
            <div class="pres-table-card">
                <div class="pres-table-head">
                    <span class="pres-table-head-title">🚨 Top 10 Retards</span>
                    <span class="pres-table-head-meta">Période : {{ $period === 'custom' ? (request('date_from') . ' → ' . request('date_to')) : ucfirst($period ?? 'mois') }}</span>
                </div>
                <table class="pres-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Utilisateur</th>
                            <th>Total</th>
                            <th>Jours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topLate ?? [] as $i => $user)
                        <tr>
                            <td><span class="pres-rank {{ $i===0?'pres-rank-1':($i===1?'pres-rank-2':($i===2?'pres-rank-3':'')) }}">{{ $i+1 }}</span></td>
                            <td style="font-weight:500;">{{ $user->name }}</td>
                            <td><span class="pres-tag tag-amber">{{ $user->total_late }} min</span></td>
                            <td style="color:var(--muted);font-family:var(--mono);font-size:.8rem;">{{ $user->days_count }}j</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="pres-empty">
                                    <div class="pres-empty-icon">✅</div>Aucun retard détecté
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Absences --}}
            <div class="pres-table-card">
                <div class="pres-table-head">
                    <span class="pres-table-head-title">⭕ Absences</span>
                    <span class="pres-table-head-meta">Période : {{ $period === 'custom' ? (request('date_from') . ' → ' . request('date_to')) : ucfirst($period ?? 'mois') }}</span>
                </div>
                @if(empty($absences))
                <div class="pres-empty">
                    <div class="pres-empty-icon">🎉</div>Aucune absence détectée
                </div>
                @else
                <table class="pres-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Jours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absences as $userName => $count)
                        <tr>
                            <td style="font-weight:500;">{{ $userName }}</td>
                            <td><span class="pres-tag tag-rose">{{ $count }} j</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

        </div>

    </div>{{-- /pres-page --}}
    </div>{{-- /pres-wrap --}}

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            Chart.defaults.font.family = "'DM Sans', sans-serif";
            Chart.defaults.color = '#9ca3af';

            // 🔥 DATA SAFE (ultra important)
            const labels = @json($globalStats['chart_data']['labels'] ?? []);
            const present = @json($globalStats['chart_data']['present'] ?? []);
            const lateMinutes = @json($globalStats['chart_data']['late_minutes'] ?? []);
            const lateDays = @json($globalStats['chart_data']['late_days'] ?? []);

            // 🔥 calcul réel cohérent
            const onTime = present.map((v, i) => v - (lateDays[i] ?? 0));

            /* =========================================================
               🎨 GRADIENTS (EFFET PREMIUM)
            ========================================================= */
            const createGradient = (ctx, color) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 260);
                gradient.addColorStop(0, color);
                gradient.addColorStop(1, 'rgba(0,0,0,0)');
                return gradient;
            };

            /* =========================================================
               📈 GRAPHE PRINCIPAL (COURBES PRO)
            ========================================================= */
            const ctxG = document.getElementById('chartGlobal');

            if (ctxG && labels.length > 0) {

                const gradientGreen = createGradient(ctxG.getContext('2d'), 'rgba(16,185,129,0.4)');
                const gradientBlue = createGradient(ctxG.getContext('2d'), 'rgba(59,130,246,0.4)');
                const gradientOrange = createGradient(ctxG.getContext('2d'), 'rgba(245,158,11,0.4)');

                new Chart(ctxG, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Présence',
                                data: present,
                                borderColor: '#10b981',
                                backgroundColor: gradientGreen,
                                fill: true,
                                tension: 0.45,
                                borderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: "À l'heure",
                                data: onTime,
                                borderColor: '#3b82f6',
                                backgroundColor: gradientBlue,
                                fill: true,
                                tension: 0.45,
                                borderWidth: 2,
                                pointRadius: 4
                            },
                            {
                                label: 'Retards (min)',
                                data: lateMinutes,
                                borderColor: '#f59e0b',
                                backgroundColor: gradientOrange,
                                fill: true,
                                tension: 0.45,
                                borderWidth: 2,
                                yAxisID: 'y1',
                                pointRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        animation: {
                            duration: 1200,
                            easing: 'easeOutQuart'
                        },

                        interaction: {
                            mode: 'index',
                            intersect: false
                        },

                        plugins: {
                            legend: {
                                labels: {
                                    color: '#e5e7eb',
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                backgroundColor: '#111827',
                                borderColor: '#374151',
                                borderWidth: 1,
                                titleColor: '#fff',
                                bodyColor: '#d1d5db'
                            }
                        },

                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(255,255,255,0.05)'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                position: 'left',
                                grid: {
                                    color: 'rgba(255,255,255,0.05)'
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            }

            /* =========================================================
               📊 BAR CHART (clean)
            ========================================================= */
            const ctxOv = document.getElementById('chartOverview');

            if (ctxOv && labels.length > 0) {
                new Chart(ctxOv, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Présents',
                                data: present,
                                backgroundColor: '#10b981'
                            },
                            {
                                label: "À l'heure",
                                data: onTime,
                                backgroundColor: '#3b82f6'
                            },
                            {
                                label: 'Retards (min)',
                                data: lateMinutes,
                                backgroundColor: '#f59e0b'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        animation: {
                            duration: 1000
                        },

                        plugins: {
                            legend: {
                                labels: {
                                    color: '#e5e7eb'
                                }
                            }
                        },

                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

        });
    </script>
    @endpush
</x-app-layout>