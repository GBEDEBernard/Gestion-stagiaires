<x-app-layout title="Statistiques de Présence - Admin">

    <x-slot name="header">
        <div class="pres-header">
            <div class="pres-header-left">
                <div class="pres-header-badge">Administration</div>
                <h1 class="pres-header-title">Tableau de Bord Présence</h1>
                <p class="pres-header-sub">Vue temps réel · Stagiaires & Employés</p>
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

    {{-- ─── STYLES UNIFIÉS (sans doublons) ─────────────────────── --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

        /* ===== MODE CLAIR (par défaut) ===== */
        :root {
            --bg: #ffffff;
            --bg2: #f8fafc;
            --bg3: #f1f5f9;
            --border: rgba(0, 0, 0, 0.08);
            --border-hi: rgba(0, 0, 0, 0.14);
            --text: #0f172a;
            --muted: #475569;
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

        /* ===== MODE SOMBRE ===== */
        .dark {
            --bg: #0f1117;
            --bg2: #161b27;
            --bg3: #1c2333;
            --border: rgba(255, 255, 255, 0.07);
            --border-hi: rgba(255, 255, 255, 0.14);
            --text: #e8eaf0;
            --muted: #6b7280;
        }

        * {
            box-sizing: border-box;
        }

        .pres-wrap {
            font-family: var(--font);
            color: var(--text);
            background: var(--bg);
            min-height: 100vh;
        }

        /* HEADER */
        .pres-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1rem 1.5rem 0 1.5rem;
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
            color: var(--text);
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

        /* LAYOUT */
        .pres-page {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem 1.5rem 3rem;
        }

        /* TABS */
        .pres-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: .25rem;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: .75rem;
            padding: .3rem;
            width: fit-content;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .pres-tabs::-webkit-scrollbar {
            display: none;
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
            color: var(--text);
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
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

        /* FILTRE */
        .pres-filter-panel {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1rem;
            margin-top: 1rem;
            color: var(--text);
            box-shadow: none;
        }

        .pres-filter-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            align-items: end;
        }

        .pres-filter-field {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--muted);
        }

        .pres-filter-input {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            background: var(--bg);
            color: var(--text);
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .pres-filter-input:focus {
            outline: none;
            border-color: var(--emerald);
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        .pres-filter-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .pres-filter-actions .pres-btn {
            flex: 1;
            text-align: center;
            justify-content: center;
            white-space: nowrap;
        }

        @media (max-width: 900px) {
            .pres-filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .pres-filter-actions {
                grid-column: span 2;
            }
        }

        @media (max-width: 640px) {
            .pres-filter-grid {
                grid-template-columns: 1fr;
            }

            .pres-filter-actions {
                grid-column: span 1;
            }
        }

        /* BOUTONS */
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
            cursor: pointer;
        }

        .pres-btn-primary {
            background: var(--emerald);
            color: #fff;
        }

        .pres-btn-secondary {
            background: transparent;
            border-color: var(--border);
            color: var(--text);
        }

        .pres-btn-secondary:hover {
            background: var(--bg3);
        }

        .pres-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, 0.65);
            padding: 1.5rem;
        }

        .pres-modal-backdrop.active {
            display: flex;
        }

        .pres-modal {
            width: min(100%, 560px);
            background: var(--bg);
            border-radius: 1.25rem;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }

        .pres-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.4rem 1.5rem;
            border-bottom: 1px solid rgba(15, 23, 42, .08);
        }

        .pres-modal-title {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
        }

        .pres-modal-meta {
            margin: .35rem 0 0;
            color: var(--muted);
            font-size: .95rem;
        }

        .pres-modal-close {
            border: none;
            background: transparent;
            color: var(--text);
            font-size: 1.45rem;
            line-height: 1;
            cursor: pointer;
        }

        .pres-modal-body {
            padding: 1.5rem;
            max-height: min(70vh, 420px);
            overflow-y: auto;
        }

        .pres-modal-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: .8rem;
        }

        .pres-modal-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: .85rem;
            align-items: center;
            padding: 1rem 1.1rem;
            border-radius: .95rem;
            background: var(--bg2);
            border: 1px solid var(--border);
            opacity: 0;
            transform: translateY(12px);
            animation: slideIn 280ms ease forwards;
        }

        .pres-modal-item-marker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: rgba(244, 63, 94, 0.12);
            color: var(--rose);
            font-size: 0.95rem;
            font-weight: 700;
        }

        .pres-modal-item-content {
            display: grid;
            gap: .25rem;
        }

        .pres-modal-item-title {
            font-weight: 600;
            color: var(--text);
            font-size: .95rem;
        }

        .pres-modal-item-sub {
            color: var(--muted);
            font-size: .85rem;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pres-modal-empty {
            color: var(--muted);
            font-size: .95rem;
            line-height: 1.65;
        }

        .pres-absence-count-button {
            border: none;
            background: transparent;
            padding: 0;
            cursor: pointer;
            font: inherit;
        }

        /* KPI CARDS */
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
            color: var(--text);
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

        /* Variantes KPI (les accents restent colorés) */
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

        /* SECTIONS */
        .pres-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
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

        /* CHARTS */
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

        .pres-chart-wrap {
            position: relative;
            height: 220px;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem 1.25rem;
            margin-bottom: 1rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: .45rem;
            font-size: .78rem;
            font-weight: 500;
            color: var(--muted);
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* QUICK ACTIONS */
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

        /* GROUP CARDS */
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
            color: var(--text);
        }

        .pres-group-role {
            font-size: .75rem;
            color: var(--muted);
            margin-top: .15rem;
        }

        .pres-group-count {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text);
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

        /* TABLES */
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
            color: var(--text);
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
            background: rgba(0, 0, 0, 0.02);
            border-bottom: 1px solid var(--border);
        }

        .dark table.pres-table th {
            background: rgba(255, 255, 255, .02);
        }

        table.pres-table td {
            padding: .85rem 1.2rem;
            font-size: .85rem;
            color: var(--text);
            border-bottom: 1px solid var(--border);
        }

        table.pres-table tbody tr:last-child td {
            border-bottom: none;
        }

        table.pres-table tbody tr:hover td {
            background: rgba(0, 0, 0, 0.02);
        }

        .dark table.pres-table tbody tr:hover td {
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

        /* REPORTS SECTION */
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

        .mt-6 {
            margin-top: 1.5rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        /* RESPONSIVE GENERAL */
        @media(max-width: 1024px) {
            .pres-page {
                padding: 1rem 1rem 2.5rem;
            }

            .pres-header {
                gap: .75rem;
            }
        }

        @media(max-width: 700px) {
            .pres-header {
                flex-direction: column;
                align-items: stretch;
            }

            .pres-header-actions {
                width: 100%;
                justify-content: space-between;
            }

            .pres-header-title {
                font-size: 1.45rem;
            }

            .pres-header-sub {
                font-size: .82rem;
            }

            .pres-tab {
                flex: 1 1 auto;
                min-width: 120px;
            }
        }
    </style>

    <div class="pres-wrap">
        <h1 class="pres-section-title">
            Statistiques de Présence
        </h1>
        <div class="pres-page">

            {{-- PERIOD TABS --}}
            <div class="pres-tabs" style="background-color: #f3f4f6; border-color: rgba(0, 0, 0, 0.08);">
                @foreach(['today'=>"Aujourd'hui",'week'=>'Semaine','month'=>'Mois','year'=>'Année'] as $k=>$lbl)
                <a href="{{ route('admin.presence.index', array_filter(['period'=>$k, 'date_from'=>$rangeStart->format('Y-m-d')])) }}" class="pres-tab {{ $period===$k?'active':'' }}" style="font-size: larger; font-weight: 600; color: #111;">
                    {{ $lbl }}
                    @if($period===$k)<span class="pres-tab-indicator"></span>@endif
                </a>
                @endforeach
            </div>

            {{-- FILTRE DATE RESPONSIVE --}}
            <div class="pres-filter-panel">
                <form method="GET" action="{{ route('admin.presence.index') }}" class="pres-filter-form">
                    <input type="hidden" name="period" value="custom" />
                    <input type="hidden" name="group" value="{{ $group ?? '' }}" />

                    <div class="pres-filter-grid">
                        <div class="pres-filter-field">
                            <label>Du</label>
                            <input type="date" name="date_from"
                                value="{{ $rangeStart->format('Y-m-d') }}"
                                class="pres-filter-input" />
                        </div>

                        <div class="pres-filter-field">
                            <label>Au</label>
                            <input type="date" name="date_to"
                                value="{{ $rangeEnd->format('Y-m-d') }}"
                                class="pres-filter-input" />
                        </div>

                        <div class="pres-filter-actions">
                            <button type="submit" class="pres-btn pres-btn-primary">Afficher</button>
                            <a href="{{ route('admin.presence.index') }}" class="pres-btn pres-btn-secondary " style="color: #000;font-weight: 600;font-size: 16px; background:#9ca3af; transform: translateY(-1px);">Réinitialiser</a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- KPI CARDS --}}
            <div class="pres-kpis mt-6">
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
                    <div class="pres-kpi-sub">{{ ($globalStats['total_late_minutes'] ?? 0) ? formatMinutes($globalStats['total_late_minutes']) : '0min' }} au total</div>
                </div>

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

            {{-- QUICK ACTIONS --}}
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
                    <a href="{{ route('tasks.index') }}" class="pres-action-card act-blue">
                        <span class="pres-action-icon">📋</span>
                        <span>Tâches</span>
                    </a>
                </div>
            </div>

            {{-- CHARTS --}}
            @php $hasChartData = !empty($globalStats['chart_data']['labels']); @endphp
            @if($hasChartData)
            <div class="mt-6">
                <div class="pres-section-title">Évolution · Présence & Ponctualité</div>
                <div style="font-size:.78rem;color:var(--muted);margin-bottom:.6rem;">💡 Cliquez sur un point d'une courbe pour afficher le détail du jour.</div>
                <div class="pres-card">
                    <div class="chart-legend">
                        <div class="legend-item"><span class="legend-dot" style="background:#10b981"></span>Présents</div>
                        <div class="legend-item"><span class="legend-dot" style="background:#3b82f6"></span>À l'heure</div>
                        <div class="legend-item"><span class="legend-dot" style="background:#f59e0b"></span>En retard</div>
                        <div class="legend-item"><span class="legend-dot" style="background:#f43f5e"></span>Absents</div>
                        <div class="legend-item"><span class="legend-dot" style="background:#f97316;border-radius:2px;"></span>Retard (min) →</div>
                        <div class="legend-item"><span class="legend-dot" style="background:#8b5cf6;border-radius:2px;"></span>Heures travaillées →</div>
                        <div class="legend-item"><span class="legend-dot" style="background:rgba(139,92,246,0.3);"></span>Jour férié</div>
                    </div>
                    <div class="chart-container" style="height:300px;">
                        <canvas id="chartGlobal"></canvas>
                    </div>
                </div>
            </div>

            <div class="pres-card mt-6">
                <div class="pres-section-title">Vue d'Ensemble · Heures & Retards</div>
                <div class="chart-container" style="height:300px;">
                    <canvas id="chartOverview"></canvas>
                </div>
            </div>
            @endif

            {{-- GROUP STATS --}}
            <div style="display:flex;flex-direction:column;gap:1rem; margin-top:1rem;">
                <div class="pres-group-card">
                    <div class="pres-group-head">
                        <div>
                            <div class="pres-group-label">👥 Stagiaires</div>
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

            {{-- REPORTS SECTION --}}
            <div class="mt-6">
                <div class="pres-section-title">Suivi des Rapports Journaliers</div>
                <div class="pres-reports-kpis">
                    <div class="pres-kpi" style="background:var(--bg2);border-color:var(--border);">
                        <div class="pres-kpi-accent" style="background:var(--muted);"></div>
                        <div class="pres-kpi-top">
                            <div class="pres-kpi-icon" style="background:rgba(107,114,128,.12);color:var(--muted);">✏️</div>
                        </div>
                        <div class="pres-kpi-value" style="font-size:1.8rem;">{{ $reportStats['drafts'] }}</div>
                        <div class="pres-kpi-label">Brouillons</div>
                        <div class="pres-kpi-sub">À compléter aujourd'hui</div>
                    </div>
                    <div class="pres-kpi kpi-amber">
                        <div class="pres-kpi-accent"></div>
                        <div class="pres-kpi-top">
                            <div class="pres-kpi-icon">⏳</div>
                        </div>
                        <div class="pres-kpi-value" style="font-size:1.8rem;">{{ $reportStats['pending'] }}</div>
                        <div class="pres-kpi-label">En Attente</div>
                        <div class="pres-kpi-sub">À reviewer par sup.</div>
                    </div>
                    <div class="pres-kpi kpi-emerald">
                        <div class="pres-kpi-accent"></div>
                        <div class="pres-kpi-top">
                            <div class="pres-kpi-icon">✅</div>
                        </div>
                        <div class="pres-kpi-value" style="font-size:1.8rem;">{{ $reportStats['approved'] }}</div>
                        <div class="pres-kpi-label">Approuvés</div>
                        <div class="pres-kpi-sub">Cette semaine</div>
                    </div>
                    <div class="pres-kpi kpi-blue">
                        <div class="pres-kpi-accent"></div>
                        <div class="pres-kpi-top">
                            <div class="pres-kpi-icon">🏆</div>
                        </div>
                        <div class="pres-kpi-value" style="font-size:1.8rem;">{{ $reportStats['validation_rate'] }}%</div>
                        <div class="pres-kpi-label">Taux Validation</div>
                        <div class="pres-kpi-sub">Objectif › 90%</div>
                    </div>
                </div>
                <div class="pres-reports-bottom">
                    <a href="{{ route('tasks.index') }}" class="pres-action-card act-blue" style="padding:1.6rem;">
                        <span class="pres-action-icon">📊</span>
                        <span style="font-size:1rem;">Toutes les Tâches</span>
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
            </div>::

            {{-- TOP RETARDS & ABSENCES --}}
            <div class="pres-charts-grid mt-6">
                <div class="pres-table-card">
                    <div class="pres-table-head">
                        <span class="pres-table-head-title">🏆 Top 10 Retardataires</span>
                        <span class="pres-table-head-meta">Période : {{ $period === 'custom' ? (request('date_from') . ' → ' . request('date_to')) : ucfirst($period ?? 'mois') }}</span>
                    </div>
                    <table class="pres-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Utilisateur</th>
                                <th>Total</th>
                                <th>Moy/jour</th>
                                <th>Jours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topLate ?? [] as $i => $user)
                            <tr>
                                <td><span class="pres-rank {{ $i===0?'pres-rank-1':($i===1?'pres-rank-2':($i===2?'pres-rank-3':'')) }}">{{ $i+1 }}</span></td>
                                <td style="font-weight:500;">{{ $user->user_name ?? $user->name }}</td>
                                <td>
                                    <span class="pres-tag tag-amber" title="{{ $user->total_late ?? 0 }} min total">
                                        ⏰ {{ formatMinutes($user->total_late ?? 0) }}
                                    </span>
                                </td>
                                <td style="color:var(--amber);font-family:var(--mono);font-size:.8rem;">
                                    {{ formatMinutes($user->avg_late ?? 0) }}/j
                                </td>
                                <td style="color:var(--muted);font-family:var(--mono);font-size:.8rem;">{{ $user->days_count }}j</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="pres-empty">
                                        <div class="pres-empty-icon">✅</div>Aucun retard détecté
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

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
                            @foreach($absenceItems as $item)
                            <tr>
                                <td style="font-weight:500;">{{ $item['user'] }}</td>
                                <td>
                                    <button type="button" class="pres-tag tag-rose pres-absence-count-button" data-index="{{ $loop->index }}">
                                        {{ $item['count'] }} j
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>

        </div>
    </div>

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
        <div x-show="open" x-transition.opacity @click="open=false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div x-show="open" x-transition.duration.200ms class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-6xl z-10 my-4 border border-gray-100 dark:border-gray-700 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between gap-3 px-5 sm:px-7 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white truncate">
                            Détail du jour — <span x-text="prefixLabel"></span>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <span x-text="fullDateLabel"></span>
                            (<span x-text="summary.total || 0"></span> personne(s) attendue(s))
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

            {{-- Résumé --}}
            <div x-show="!loading && !error && allRows.length > 0" class="px-5 sm:px-7 pt-4 flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                    Pr&eacute;sents <span x-text="summary.present || 0"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                    &Agrave; l'heure <span x-text="summary.on_time || 0"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                    En retard <span x-text="summary.late || 0"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300">
                    Absents <span x-text="summary.absent || 0"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300" x-show="summary.corrected">
                    Corrig&eacute;s <span x-text="summary.corrected || 0"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    Heures <span x-text="fmtHours(summary.worked_minutes)"></span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    Retard <span x-text="fmtMin(summary.late_minutes)"></span>
                </span>
            </div>

            {{-- Body --}}
            <div class="px-5 sm:px-7 py-4" @keydown.escape.window="open=false">
                <div x-show="loading" class="py-16 text-center">
                    <div class="w-10 h-10 mx-auto rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 font-medium">Chargement du jour…</p>
                </div>

                <div x-show="!loading && error" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Impossible de charger les données.</p>
                    <button @click="loadDay()" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-amber-600 text-white hover:bg-amber-700 transition">
                        R&eacute;essayer
                    </button>
                </div>

                <div x-show="!loading && !error && flags.future" class="py-16 text-center">
                    <div class="text-3xl">🚀</div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Ce jour n'est pas encore arriv&eacute;.</p>
                </div>
                <div x-show="!loading && !error && flags.weekend" class="py-16 text-center">
                    <div class="text-3xl">🌴</div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Jour de week-end — aucune pr&eacute;sence attendue.</p>
                </div>
                <div x-show="!loading && !error && flags.holiday" class="py-16 text-center">
                    <div class="text-3xl">🎉</div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Jour f&eacute;ri&eacute; — aucune pr&eacute;sence attendue.</p>
                </div>
                <div x-show="!loading && !error && flags.before_system" class="py-16 text-center">
                    <div class="text-3xl">🗓️</div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Jour ant&eacute;rieur &agrave; l'activation du syst&egrave;me.</p>
                </div>
                <div x-show="!loading && !error && !flags.future && !flags.weekend && !flags.holiday && !flags.before_system && allRows.length === 0" class="py-16 text-center">
                    <div class="text-3xl">📭</div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Aucune donn&eacute;e de pr&eacute;sence pour ce jour.</p>
                </div>

                <div x-show="!loading && !error && allRows.length > 0" class="overflow-x-auto max-h-[55vh] overflow-y-auto rounded-xl border border-gray-100 dark:border-gray-700">
                    <table class="w-full text-sm min-w-[860px]">
                        <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Utilisateur</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Site</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Entr&eacute;e</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sortie</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Heures</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Retard</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="r in rows" :key="r.id + '-' + r.group">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-5 py-3">
                                        <div class="font-semibold text-gray-900 dark:text-white" x-text="r.name"></div>
                                        <div class="text-xs text-gray-400 mt-0.5" x-show="r.school" x-text="r.school"></div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-700 dark:text-gray-300" x-text="groupLabel(r.group)"></td>
                                    <td class="px-5 py-3 text-gray-700 dark:text-gray-300" x-text="r.site_name || '—'"></td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                                            :class="statusBadge(r.status).cls">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="statusBadge(r.status).dot"></span>
                                            <span x-text="statusBadge(r.status).label"></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300" x-text="r.arrival || '—'"></td>
                                    <td class="px-5 py-3 whitespace-nowrap text-right text-gray-700 dark:text-gray-300" x-text="r.departure || '—'"></td>
                                    <td class="px-5 py-3 whitespace-nowrap text-right text-gray-700 dark:text-gray-300" x-text="fmtHours(r.worked_minutes)"></td>
                                    <td class="px-5 py-3 whitespace-nowrap text-right" x-text="fmtMin(r.late_minutes)" :class="r.late_minutes > 0 ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-gray-400'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div x-show="!loading && !error && allRows.length > 0" class="mt-4 flex items-center justify-between gap-3 flex-wrap">
                    <p class="text-xs text-gray-500 dark:text-gray-400"
                       x-text="`Page ${page} / ${lastPage} — ${allRows.length} personne(s) au total`"></p>
                    <div class="flex gap-2">
                        <button @click="prevPage()" :disabled="page <= 1"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            ‹ Pr&eacute;c&eacute;dent
                        </button>
                        <button @click="nextPage()" :disabled="page >= lastPage"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Suivant ›
                        </button>
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
                        on_time:   { label: "À l'heure", cls: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300', dot: 'bg-blue-500' },
                        late:      { label: 'En retard', cls: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300', dot: 'bg-amber-500' },
                        present:   { label: 'Présent', cls: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300', dot: 'bg-emerald-500' },
                        absent:    { label: 'Absent', cls: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300', dot: 'bg-rose-500' },
                        corrected: { label: 'Corrigé', cls: 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300', dot: 'bg-violet-500' },
                    };
                    return map[status] || { label: status || '—', cls: 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300', dot: 'bg-gray-400' };
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
                                this.statusBadge(r.status).label,
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
            // Récupération des couleurs thème pour les graphiques
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#e8eaf0' : '#0f172a';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const mutedColor = isDark ? '#6b7280' : '#475569';

            Chart.defaults.font.family = "'DM Sans', sans-serif";
            Chart.defaults.color = textColor;

            /* Helper JS : minutes → Xh Ymin */
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

            /* Plugin : bandes verticales pour jours fériés */
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
                            ctx.fillStyle = 'rgba(139,92,246,0.07)';
                            ctx.fillRect(xPos, top, gap, bottom - top);
                            ctx.restore();
                        }
                    });
                }
            };

            /* Clic sur un point d'un graphique → ouvrir le détail du jour */
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
                    <li class="pres-modal-item" style="animation-delay: ${index * 80}ms;">
                        <span class="pres-modal-item-marker">✖</span>
                        <div class="pres-modal-item-content">
                            <div class="pres-modal-item-title">${day.label}</div>
                            <div class="pres-modal-item-sub">${day.date}</div>
                        </div>
                    </li>
                `).join('')}</ul>`;
            };

            const openAbsenceModal = (index) => {
                const item = absenceItems[index] ?? null;
                if (!item) {
                    return;
                }
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

            /* ══════════════════════════════════════════════════════════
               GRAPHIQUE 1 — Courbes : Présence, Retards, Absences
               Axe gauche : nombre de personnes
               Axe droit  : minutes / heures
            ══════════════════════════════════════════════════════════ */
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
                    backgroundColor: isDark ? '#1c2333' : '#ffffff',
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    titleColor: isDark ? '#fff' : '#0f172a',
                    bodyColor: isDark ? '#d1d5db' : '#475569',
                    padding: 12,
                    cornerRadius: 10,
                };

                new Chart(ctx, {
                    type: 'line',
                    plugins: [holidayPlugin],
                    data: {
                        labels,
                        datasets: [{
                                label: 'Présents',
                                data: present,
                                borderColor: '#10b981',
                                backgroundColor: mkGrad('rgba(16,185,129,0.22)', 'rgba(16,185,129,0.02)'),
                                fill: true,
                                tension: 0.4,
                                cubicInterpolationMode: 'monotone',
                                borderWidth: 3,
                                pointRadius: present.map(v => v ? 5 : 0),
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                yAxisID: 'yCount'
                            },
                            {
                                label: "À l'heure",
                                data: onTime,
                                borderColor: '#3b82f6',
                                backgroundColor: mkGrad('rgba(59,130,246,0.18)', 'rgba(59,130,246,0.02)'),
                                fill: true,
                                tension: 0.4,
                                cubicInterpolationMode: 'monotone',
                                borderWidth: 2,
                                pointRadius: onTime.map(v => v ? 4 : 0),
                                pointHoverRadius: 7,
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                yAxisID: 'yCount'
                            },
                            {
                                label: 'En retard',
                                data: lateDays,
                                borderColor: '#f59e0b',
                                backgroundColor: mkGrad('rgba(245,158,11,0.16)', 'rgba(245,158,11,0.02)'),
                                fill: true,
                                tension: 0.4,
                                cubicInterpolationMode: 'monotone',
                                borderWidth: 2,
                                pointRadius: lateDays.map(v => v ? 4 : 0),
                                pointHoverRadius: 7,
                                pointBackgroundColor: '#f59e0b',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                yAxisID: 'yCount'
                            },
                            {
                                label: 'Absents',
                                data: absences,
                                borderColor: '#f43f5e',
                                backgroundColor: mkGrad('rgba(244,63,94,0.16)', 'rgba(244,63,94,0.02)'),
                                fill: true,
                                tension: 0.4,
                                cubicInterpolationMode: 'monotone',
                                borderWidth: 2,
                                pointRadius: absences.map(v => v ? 4 : 0),
                                pointHoverRadius: 7,
                                pointBackgroundColor: '#f43f5e',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
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
                                borderDash: [6, 4],
                                pointRadius: lateMinutes.map(v => v > 0 ? 4 : 0),
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#f97316',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 1,
                                yAxisID: 'yMinutes'
                            },
                            {
                                label: 'Heures travaillées',
                                data: workedMinutes,
                                borderColor: '#8b5cf6',
                                backgroundColor: 'transparent',
                                fill: false,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: workedMinutes.map(v => v > 0 ? 4 : 0),
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#8b5cf6',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 1,
                                yAxisID: 'yMinutes'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 900,
                            easing: 'easeOutQuart'
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        onClick: openDayFromChart,
                        onHover: chartCursor,
                        plugins: {
                            legend: {
                                display: false
                            },
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
                                grid: {
                                    color: gridColor
                                },
                                ticks: {
                                    color: mutedColor,
                                    font: {
                                        size: 10
                                    },
                                    maxRotation: 45,
                                    maxTicksLimit: 20
                                }
                            },
                            yCount: {
                                type: 'linear',
                                position: 'left',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Nombre de personnes',
                                    color: mutedColor,
                                    font: {
                                        size: 10
                                    }
                                },
                                ticks: {
                                    color: mutedColor,
                                    font: {
                                        size: 10
                                    }
                                },
                                grid: {
                                    color: gridColor
                                }
                            },
                            yMinutes: {
                                type: 'linear',
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: 'Minutes / Heures',
                                    color: '#f97316',
                                    font: {
                                        size: 10
                                    }
                                },
                                ticks: {
                                    callback: (v) => fmtMin(v),
                                    color: '#f97316',
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        }
                    }
                });
            }

            /* ══════════════════════════════════════════════════════════
               GRAPHIQUE 2 — Barres : Heures travaillées + Retard (min)
            ══════════════════════════════════════════════════════════ */
            const ctxOv = document.getElementById('chartOverview');
            if (ctxOv && labels.length > 0) {
                new Chart(ctxOv, {
                    type: 'bar',
                    plugins: [holidayPlugin],
                    data: {
                        labels,
                        datasets: [{
                                label: 'Heures travaillées',
                                data: workedHours,
                                backgroundColor: 'rgba(59,130,246,0.75)',
                                borderColor: '#3b82f6',
                                borderWidth: 1,
                                borderRadius: 5,
                                borderSkipped: false,
                                barPercentage: 0.65,
                                categoryPercentage: 0.75,
                                yAxisID: 'yHours'
                            },
                            {
                                label: 'Retard (min)',
                                data: lateMinutes,
                                backgroundColor: 'rgba(245,158,11,0.7)',
                                borderColor: '#f59e0b',
                                borderWidth: 1,
                                borderRadius: 5,
                                borderSkipped: false,
                                barPercentage: 0.65,
                                categoryPercentage: 0.75,
                                yAxisID: 'yMin'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 800,
                            easing: 'easeOutQuart'
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        onClick: openDayFromChart,
                        onHover: chartCursor,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    color: textColor,
                                    usePointStyle: true,
                                    padding: 14,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#1c2333' : '#ffffff',
                                borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                borderWidth: 1,
                                titleColor: isDark ? '#fff' : '#0f172a',
                                bodyColor: isDark ? '#d1d5db' : '#475569',
                                padding: 12,
                                cornerRadius: 10,
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
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: mutedColor,
                                    font: {
                                        size: 10
                                    },
                                    maxRotation: 45,
                                    maxTicksLimit: 20
                                }
                            },
                            yHours: {
                                type: 'linear',
                                position: 'left',
                                min: 0,
                                title: {
                                    display: true,
                                    text: 'Heures',
                                    color: '#3b82f6',
                                    font: {
                                        size: 10
                                    }
                                },
                                ticks: {
                                    callback: v => v + 'h',
                                    color: '#3b82f6',
                                    font: {
                                        size: 10
                                    }
                                },
                                grid: {
                                    color: gridColor
                                }
                            },
                            yMin: {
                                type: 'linear',
                                position: 'right',
                                min: 0,
                                title: {
                                    display: true,
                                    text: 'Retard (min)',
                                    color: '#f59e0b',
                                    font: {
                                        size: 10
                                    }
                                },
                                ticks: {
                                    callback: v => v + ' min',
                                    color: '#f59e0b',
                                    font: {
                                        size: 10
                                    }
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>