<x-app-layout title="Historique de présence">
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
            --font: 'DM Sans', sans-serif;
            --mono: 'DM Mono', monospace;
        }

        /* ===== MODE SOMBRE (classe .dark) ===== */
        .dark {
            --bg: #0f1117;
            --bg2: #161b27;
            --bg3: #1c2333;
            --border: rgba(255, 255, 255, 0.07);
            --border-hi: rgba(255, 255, 255, 0.14);
            --text: #e8eaf0;
            --muted: #6b7280;
        }

        * { box-sizing: border-box; }

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
            padding: 1.5rem;
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
        .pres-header-actions { display: flex; align-items: center; gap: .75rem; }
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
        .pres-page { max-width: 1400px; margin: 0 auto; padding: 1.5rem 1.5rem 3rem; }

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
        .pres-tabs::-webkit-scrollbar { display: none; }
        .pres-tab {
            padding: .45rem 1.1rem;
            border-radius: .5rem;
            font-size: .83rem;
            font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            transition: all .2s;
            white-space: nowrap;
        }
        .pres-tab:hover {
            color: var(--text);
            background: var(--bg3);
        }
        .pres-tab.active {
            background: var(--bg3);
            color: var(--text);
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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

        /* FILTER */
        .pres-filter-panel {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1rem;
            margin-top: 1rem;
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
            gap: .5rem;
            font-size: .85rem;
            font-weight: 500;
            color: var(--muted);
        }
        .pres-filter-input {
            width: 100%;
            padding: .85rem 1rem;
            border: 1px solid var(--border);
            border-radius: .75rem;
            background: var(--bg);
            color: var(--text);
            font-size: .9rem;
            transition: all .2s;
        }
        .pres-filter-input:focus {
            outline: none;
            border-color: var(--emerald);
            box-shadow: 0 0 0 2px rgba(16,185,129,.2);
        }
        .pres-filter-actions { display: flex; gap: .75rem; flex-wrap: wrap; }
        .pres-filter-actions .pres-btn { flex: 1; text-align: center; justify-content: center; white-space: nowrap; }

        @media(max-width:900px) {
            .pres-filter-grid { grid-template-columns: repeat(2,1fr); }
            .pres-filter-actions { grid-column: span 2; }
        }
        @media(max-width:640px) {
            .pres-filter-grid { grid-template-columns: 1fr; }
            .pres-filter-actions { grid-column: span 1; }
        }

        /* BUTTONS */
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

        /* KPI CARDS */
        .pres-kpis {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        @media(max-width:900px) { .pres-kpis { grid-template-columns: repeat(2,1fr); } }
        @media(max-width:500px) { .pres-kpis { grid-template-columns: 1fr; } }
        .pres-kpi {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.4rem 1.5rem;
            position: relative;
            overflow: hidden;
            transition: border-color .2s, transform .2s;
        }
        .pres-kpi:hover { border-color: var(--border-hi); transform: translateY(-2px); }
        .pres-kpi-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            border-radius: 1rem 1rem 0 0;
        }
        .pres-kpi-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: .75rem; }
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
        .pres-kpi-label { font-size: .82rem; color: var(--muted); }
        .pres-kpi-sub {
            font-size: .75rem;
            color: var(--muted);
            margin-top: .5rem;
            padding-top: .5rem;
            border-top: 1px solid var(--border);
        }

        .kpi-emerald .pres-kpi-accent { background: var(--emerald); }
        .kpi-emerald .pres-kpi-icon { background: rgba(16,185,129,.12); color: var(--emerald); }
        .kpi-emerald .pres-kpi-badge { background: rgba(16,185,129,.12); color: var(--emerald); }

        .kpi-amber .pres-kpi-accent { background: var(--amber); }
        .kpi-amber .pres-kpi-icon { background: rgba(245,158,11,.12); color: var(--amber); }
        .kpi-amber .pres-kpi-badge { background: rgba(245,158,11,.12); color: var(--amber); }

        .kpi-blue .pres-kpi-accent { background: var(--blue); }
        .kpi-blue .pres-kpi-icon { background: rgba(59,130,246,.12); color: var(--blue); }
        .kpi-blue .pres-kpi-badge { background: rgba(59,130,246,.12); color: var(--blue); }

        .kpi-rose .pres-kpi-accent { background: var(--rose); }
        .kpi-rose .pres-kpi-icon { background: rgba(244,63,94,.12); color: var(--rose); }
        .kpi-rose .pres-kpi-badge { background: rgba(244,63,94,.12); color: var(--rose); }

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

        /* CARDS / CHARTS */
        .pres-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: border-color .2s;
        }
        .pres-card:hover { border-color: var(--border-hi); }
        .chart-container { position: relative; height: 300px; }

        /* LEGEND */
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

        /* TABLE */
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
            background: rgba(0,0,0,0.02);
            border-bottom: 1px solid var(--border);
        }
        .dark table.pres-table th {
            background: rgba(255,255,255,.02);
        }
        table.pres-table td {
            padding: .85rem 1.2rem;
            font-size: .85rem;
            color: var(--text);
            border-bottom: 1px solid var(--border);
        }
        table.pres-table tbody tr:last-child td { border-bottom: none; }
        table.pres-table tbody tr:hover td {
            background: rgba(0,0,0,0.02);
        }
        .dark table.pres-table tbody tr:hover td {
            background: rgba(255,255,255,.02);
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
            background: rgba(245,158,11,.12);
            color: var(--amber);
            border: 1px solid rgba(245,158,11,.2);
        }
        .tag-rose {
            background: rgba(244,63,94,.12);
            color: var(--rose);
            border: 1px solid rgba(244,63,94,.2);
        }
        .tag-emerald {
            background: rgba(16,185,129,.12);
            color: var(--emerald);
            border: 1px solid rgba(16,185,129,.2);
        }

        .pres-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--muted);
            font-size: .85rem;
        }
        .pres-empty-icon { font-size: 2rem; margin-bottom: .5rem; }

        .mt-6 { margin-top: 1.5rem; }
        .mt-4 { margin-top: 1rem; }

        /* MOBILE */
        .mobile-cards { display: none; }
        .desktop-table { display: block; }
        @media(max-width:768px) {
            .mobile-cards { display: block; }
            .desktop-table { display: none; }
        }
        .mobile-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: .75rem;
        }
        .mobile-card-row {
            display: flex;
            justify-content: space-between;
            padding: .4rem 0;
            border-bottom: 1px solid var(--border);
        }
        .mobile-card-row:last-child { border-bottom: none; }
        .mobile-label { color: var(--muted); font-size: .8rem; }
        .mobile-value { font-weight: 600; font-size: .85rem; }

        /* RESPONSIVE HEADER */
        @media(max-width:1024px) {
            .pres-page { padding: 1rem 1rem 2.5rem; }
        }
        @media(max-width:700px) {
            .pres-header { flex-direction: column; align-items: stretch; }
            .pres-header-actions { width: 100%; justify-content: space-between; }
            .pres-header-title { font-size: 1.45rem; }
        }
    </style>
    <div class="pres-wrap">
        <div class="pres-header">
            <div class="pres-header-left">
                <div class="pres-header-badge">Mon espace</div>
                <h1 class="pres-header-title">
                    @isset($user)
                        Pointages de {{ $user->name }}
                    @else
                        Mon historique de présence
                    @endisset
                </h1>
                <p class="pres-header-sub">Vue personnelle · Statistiques détaillées</p>
            </div>
            <div class="pres-header-actions">
                <a href="{{ route('presence.pointage') }}" class="pres-btn-export">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Nouveau pointage
                </a>
            </div>
        </div>

        <div class="pres-page">

            {{-- PERIOD TABS --}}
            <div class="pres-tabs">
                @foreach(['today'=>"Aujourd'hui",'week'=>'Semaine','month'=>'Mois','year'=>'Année'] as $k=>$lbl)
                <a href="?period={{ $k }}" class="pres-tab {{ request('period', 'month')===$k ? 'active' : '' }}">
                    {{ $lbl }}
                    @if(request('period')===$k)<span class="pres-tab-indicator"></span>@endif
                </a>
                @endforeach
            </div>

            {{-- FILTRE DATE --}}
            <div class="pres-filter-panel">
                <form method="GET" action="{{ route('presence.historique') }}">
                    <input type="hidden" name="period" value="custom"/>
                    <div class="pres-filter-grid">
                        <div class="pres-filter-field">
                            <label>Du</label>
                            <input type="date" name="date_from" value="{{ request('date_from', today()->format('Y-m-d')) }}" class="pres-filter-input"/>
                        </div>
                        <div class="pres-filter-field">
                            <label>Au</label>
                            <input type="date" name="date_to" value="{{ request('date_to', today()->format('Y-m-d')) }}" class="pres-filter-input"/>
                        </div>
                        <div class="pres-filter-actions">
                            <button type="submit" class="pres-btn pres-btn-primary">Afficher</button>
                            <a href="{{ route('presence.historique') }}" class="pres-btn pres-btn-secondary">Réinitialiser</a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- KPI CARDS --}}
            <div class="pres-kpis mt-6">
                <div class="pres-kpi kpi-emerald">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">✓</div>
                        <span class="pres-kpi-badge">{{ $userStats['present_days'] ?? 0 }}/{{ $userStats['total_days'] ?? 0 }}</span>
                    </div>
                    <div class="pres-kpi-value">
                        {{ ($userStats['total_days'] ?? 0) > 0 ? round((($userStats['present_days'] ?? 0) / ($userStats['total_days'] ?? 1)) * 100) : 0 }}%
                    </div>
                    <div class="pres-kpi-label">Taux de Présence</div>
                    <div class="pres-kpi-sub">{{ $userStats['present_days'] ?? 0 }} jours présents</div>
                </div>

                <div class="pres-kpi kpi-amber">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">⏱</div>
                        <span class="pres-kpi-badge">{{ $userStats['late_days'] ?? 0 }} jours</span>
                    </div>
                    <div class="pres-kpi-value">{{ round(($userStats['total_late_minutes'] ?? 0) / 60, 1) }}h</div>
                    <div class="pres-kpi-label">Retards Cumulés</div>
                    <div class="pres-kpi-sub">{{ number_format($userStats['total_late_minutes'] ?? 0) }} min au total</div>
                </div>

                <div class="pres-kpi kpi-blue">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">💼</div>
                        <span class="pres-kpi-badge">{{ $userStats['total_worked_hours'] ?? 0 }}h</span>
                    </div>
                    <div class="pres-kpi-value">{{ $userStats['total_worked_hours'] ?? 0 }}h</div>
                    <div class="pres-kpi-label">Heures Travaillées</div>
                    <div class="pres-kpi-sub">{{ $userStats['avg_daily_hours'] ?? 0 }}h/jour moyenne</div>
                </div>

                <div class="pres-kpi kpi-rose">
                    <div class="pres-kpi-accent"></div>
                    <div class="pres-kpi-top">
                        <div class="pres-kpi-icon">!</div>
                        <span class="pres-kpi-badge">{{ ($userStats['total_days'] ?? 0) - ($userStats['present_days'] ?? 0) }} jours</span>
                    </div>
                    <div class="pres-kpi-value">{{ ($userStats['total_days'] ?? 0) - ($userStats['present_days'] ?? 0) }}</div>
                    <div class="pres-kpi-label">Absences</div>
                    <div class="pres-kpi-sub">{{ $userStats['open_anomalies'] ?? 0 }} anomalies ouvertes</div>
                </div>
            </div>

            {{-- CHARTS --}}
            @php
                $chartData   = $userStats['chart_data'] ?? [];
                $hasChartData = !empty($chartData['labels']);
            @endphp

            @if($hasChartData)
            <div class="mt-6">
                <div class="pres-section-title">Évolution · Présence & Ponctualité</div>
                <div class="pres-card">
                    {{-- Légende manuelle pour clarté --}}
                    <div class="chart-legend">
                        <div class="legend-item"><span class="legend-dot" style="background:#10b981"></span>Présent</div>
                        <div class="legend-item"><span class="legend-dot" style="background:#3b82f6"></span>À l'heure</div>
                        <div class="legend-item"><span class="legend-dot" style="background:#f59e0b"></span>En retard</div>
                        <div class="legend-item"><span class="legend-dot" style="background:#f43f5e"></span>Absent</div>
                        <div class="legend-item"><span class="legend-dot" style="background:#f97316; border-radius:2px;"></span>Retard (min) →</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartPresence"></canvas>
                    </div>
                </div>
            </div>

            <div class="pres-card mt-6">
                <div class="pres-section-title">Vue d'Ensemble · Heures & Retards</div>
                <div class="chart-container">
                    <canvas id="chartOverview"></canvas>
                </div>
            </div>
            @endif

            {{-- TABLEAU --}}
            <div class="pres-table-card mt-6">
                <div class="pres-table-head">
                    <span class="pres-table-head-title">📋 Pointages récents</span>
                    <span class="pres-table-head-meta">
                        Période : {{ $period === 'custom' ? (request('date_from').' → '.request('date_to')) : ucfirst($period ?? 'mois') }}
                        · {{ $attendanceDays->count() }} enregistrements
                    </span>
                </div>

                {{-- Desktop --}}
                <div class="desktop-table">
                    <table class="pres-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Arrivée</th>
                                <th>Départ</th>
                                <th>Heures</th>
                                <th>Retard</th>
                                <th>Statut</th>
                                <th>Rapport</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceDays->sortByDesc('attendance_date') as $day)
                            <tr>
                                <td>
                                    <div style="font-weight:500;">{{ $day->attendance_date->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
                                    <div style="font-size:.75rem;color:var(--muted);">{{ $day->attendance_date->locale('fr')->isoFormat('dddd') }}</div>
                                </td>
                                <td>{{ $day->first_check_in_at?->format('H:i') ?? '—' }}</td>
                                <td>{{ $day->last_check_out_at?->format('H:i') ?? '—' }}</td>
                                <td style="font-family:var(--mono);">{{ $day->worked_minutes > 0 ? round($day->worked_minutes / 60, 1).'h' : '—' }}</td>
                                <td>
                                    @if($day->late_minutes > 0)
                                        <span class="pres-tag tag-amber">{{ $day->late_minutes }} min</span>
                                    @else
                                        <span style="color:var(--muted);">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($day->first_check_in_at)
                                        @if($day->arrival_status === 'late')
                                            <span class="pres-tag tag-amber">En retard</span>
                                        @else
                                            <span class="pres-tag tag-emerald">À l'heure</span>
                                        @endif
                                    @else
                                        <span class="pres-tag tag-rose">Absent</span>
                                    @endif
                                </td>
                                <td>
                                    @php $report = $day->dailyReports->first(); @endphp
                                    @if($report)
                                        @if($report->status === 'approved')
                                            <span class="pres-tag tag-emerald">Approuvé</span>
                                        @elseif($report->status === 'submitted')
                                            <span class="pres-tag tag-emerald">Soumis</span>
                                        @elseif($report->status === 'draft')
                                            <span class="pres-tag tag-amber">Brouillon</span>
                                        @else
                                            <span class="pres-tag">{{ ucfirst($report->status) }}</span>
                                        @endif
                                    @else
                                        <span style="color:var(--muted);font-size:.8rem;">Aucun</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7">
                                <div class="pres-empty">
                                    <div class="pres-empty-icon">📭</div>
                                    Aucun pointage trouvé pour cette période
                                </div>
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile --}}
                <div class="mobile-cards" style="padding:1rem;">
                    @forelse($attendanceDays->sortByDesc('attendance_date') as $day)
                    <div class="mobile-card">
                        <div class="mobile-card-row">
                            <span class="mobile-label">Date</span>
                            <span class="mobile-value">{{ $day->attendance_date->locale('fr')->isoFormat('D MMM') }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-label">Arrivée</span>
                            <span class="mobile-value">{{ $day->first_check_in_at?->format('H:i') ?? '—' }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-label">Départ</span>
                            <span class="mobile-value">{{ $day->last_check_out_at?->format('H:i') ?? '—' }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-label">Heures</span>
                            <span class="mobile-value">{{ $day->worked_minutes > 0 ? round($day->worked_minutes/60,1).'h' : '—' }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-label">Statut</span>
                            <span class="mobile-value">
                                @if($day->first_check_in_at)
                                    @if($day->arrival_status === 'late')
                                        <span class="pres-tag tag-amber">En retard</span>
                                    @else
                                        <span class="pres-tag tag-emerald">À l'heure</span>
                                    @endif
                                @else
                                    <span class="pres-tag tag-rose">Absent</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="pres-empty">
                        <div class="pres-empty-icon">📭</div>
                        Aucun pointage trouvé pour cette période
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        Chart.defaults.font.family = "'DM Sans', sans-serif";
        Chart.defaults.color = '#9ca3af';

        /* ── Données injectées depuis PHP ─────────────────────────── */
        @php
            $chartDataSafe = $userStats['chart_data'] ?? [
                'labels' => [],
                'present' => [],
                'on_time' => [],
                'late_days' => [],
                'absences' => [],
                'late_minutes' => [],
                'worked_hours' => [],
            ];
        @endphp

        const cd = @json($chartDataSafe);

        const labels      = cd.labels       ?? [];
        const present     = cd.present      ?? [];
        const onTime      = cd.on_time      ?? [];
        const lateDays    = cd.late_days    ?? [];
        const absences    = cd.absences     ?? [];
        const lateMinutes = cd.late_minutes ?? [];
        const workedHours = cd.worked_hours ?? [];

        if (!labels.length) return; // pas de données → pas de graphique

        /* ── Helpers ──────────────────────────────────────────────── */
        const mkGradient = (ctx, top, bottom) => {
            const g = ctx.createLinearGradient(0, 0, 0, 300);
            g.addColorStop(0, top);
            g.addColorStop(1, bottom);
            return g;
        };

        const tooltipStyle = {
            backgroundColor: '#1c2333',
            borderColor: 'rgba(255,255,255,0.1)',
            borderWidth: 1,
            titleColor: '#fff',
            bodyColor: '#d1d5db',
            padding: 12,
            cornerRadius: 10,
        };

        /* ══════════════════════════════════════════════════════════
           GRAPHIQUE 1 — Courbes binaires + minutes retard
           Axe gauche  (yBin)  : 0 / 1 (présent, à l'heure, retard, absent)
           Axe droit   (yMin)  : minutes de retard (échelle libre)
        ══════════════════════════════════════════════════════════ */
        const ctx1 = document.getElementById('chartPresence');
        if (ctx1) {
            const g1 = ctx1.getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Présent',
                            data: present,
                            borderColor: '#10b981',
                            backgroundColor: mkGradient(g1,'rgba(16,185,129,.22)','rgba(16,185,129,.02)'),
                            fill: true, tension: 0.4,cubicInterpolationMode: 'monotone',
                            borderWidth: 2.5,
                            pointRadius: present.map(v => v ? 5 : 0),
                            pointHoverRadius: 8,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#fff', pointBorderWidth: 2,
                            yAxisID: 'yBin',
                        },
                        {
                            label: "À l'heure",
                            data: onTime,
                            borderColor: '#3b82f6',
                            backgroundColor: mkGradient(g1,'rgba(59,130,246,.18)','rgba(59,130,246,.02)'),
                            fill: true,tension: 0.4,cubicInterpolationMode: 'monotone',
                            borderWidth: 2,
                            pointRadius: onTime.map(v => v ? 5 : 0),
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#fff', pointBorderWidth: 2,
                            yAxisID: 'yBin',
                        },
                        {
                            label: 'En retard',
                            data: lateDays,
                            borderColor: '#f59e0b',
                            backgroundColor: mkGradient(g1,'rgba(245,158,11,.18)','rgba(245,158,11,.02)'),
                            fill: true, tension: 0.4,cubicInterpolationMode: 'monotone',
                            borderWidth: 2,
                            pointRadius: lateDays.map(v => v ? 5 : 0),
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#f59e0b',
                            pointBorderColor: '#fff', pointBorderWidth: 2,
                            yAxisID: 'yBin',
                        },
                        {
                            label: 'Absent',
                            data: absences,
                            borderColor: '#f43f5e',
                            backgroundColor: mkGradient(g1,'rgba(244,63,94,.16)','rgba(244,63,94,.02)'),
                            fill: true, tension: 0.4,cubicInterpolationMode: 'monotone',
                            borderWidth: 2,
                            pointRadius: absences.map(v => v ? 5 : 0),
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#f43f5e',
                            pointBorderColor: '#fff', pointBorderWidth: 2,
                            yAxisID: 'yBin',
                        },
                        {
                            label: 'Retard (min)',
                            data: lateMinutes,
                            borderColor: '#f97316',
                            backgroundColor: 'transparent',
                            fill: false, tension: 0.35,
                            borderWidth: 2,
                            borderDash: [6, 4],
                            pointRadius: lateMinutes.map(v => v > 0 ? 4 : 0),
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#f97316',
                            pointBorderColor: '#fff', pointBorderWidth: 1,
                            yAxisID: 'yMin',
                        },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false }, // on utilise la légende HTML custom
                        tooltip: {
                            ...tooltipStyle,
                            callbacks: {
                                label(ctx) {
                                    const v = ctx.parsed.y;
                                    const lbl = ctx.dataset.label;
                                    if (lbl === 'Retard (min)') return `${lbl}: ${v} min`;
                                    return `${lbl}: ${v === 1 ? 'Oui' : 'Non'}`;
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255,255,255,0.04)' },
                            ticks: { font: { size: 10 }, maxRotation: 45, maxTicksLimit: 14 },
                        },
                        yBin: {
                            type: 'linear', position: 'left',
                            min: -0.1, max: 1.4,
                            title: { display: true, text: '0 = Non  /  1 = Oui', color: '#6b7280', font: { size: 10 } },
                            ticks: {
                                stepSize: 1,
                                callback: v => v === 0 ? '0' : v === 1 ? '1' : '',
                                color: '#6b7280', font: { size: 10 },
                            },
                            grid: { color: 'rgba(255,255,255,0.05)' },
                        },
                        yMin: {
                            type: 'linear', position: 'right',
                            min: 0,
                            title: { display: true, text: 'Minutes', color: '#f97316', font: { size: 10 } },
                            ticks: { callback: v => v + ' min', color: '#f97316', font: { size: 10 } },
                            grid: { drawOnChartArea: false },
                        },
                    },
                },
            });
        }

        /* ══════════════════════════════════════════════════════════
           GRAPHIQUE 2 — Barres : heures travaillées + retard (min)
           Axe gauche : heures
           Axe droit  : minutes de retard
        ══════════════════════════════════════════════════════════ */
        const ctx2 = document.getElementById('chartOverview');
        if (ctx2) {
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Heures travaillées',
                            data: workedHours,
                            backgroundColor: 'rgba(59,130,246,.75)',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            borderRadius: 5,
                            borderSkipped: false,
                            barPercentage: 0.65,
                            categoryPercentage: 0.75,
                            yAxisID: 'yHours',
                        },
                        {
                            label: 'Retard (min)',
                            data: lateMinutes,
                            backgroundColor: 'rgba(245,158,11,.7)',
                            borderColor: '#f59e0b',
                            borderWidth: 1,
                            borderRadius: 5,
                            borderSkipped: false,
                            barPercentage: 0.65,
                            categoryPercentage: 0.75,
                            yAxisID: 'yMin',
                        },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: 800, easing: 'easeOutQuart' },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: '#e5e7eb', usePointStyle: true, padding: 14, font: { size: 12 } },
                        },
                        tooltip: {
                            ...tooltipStyle,
                            callbacks: {
                                label(ctx) {
                                    const v = ctx.parsed.y;
                                    return ctx.dataset.label === 'Heures travaillées'
                                        ? `Heures: ${v}h`
                                        : `Retard: ${v} min`;
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, maxRotation: 45, maxTicksLimit: 14 },
                        },
                        yHours: {
                            type: 'linear', position: 'left',
                            min: 0,
                            title: { display: true, text: 'Heures', color: '#3b82f6', font: { size: 10 } },
                            ticks: { callback: v => v + 'h', color: '#3b82f6', font: { size: 10 } },
                            grid: { color: 'rgba(255,255,255,0.05)' },
                        },
                        yMin: {
                            type: 'linear', position: 'right',
                            min: 0,
                            title: { display: true, text: 'Retard (min)', color: '#f59e0b', font: { size: 10 } },
                            ticks: { callback: v => v + ' min', color: '#f59e0b', font: { size: 10 } },
                            grid: { drawOnChartArea: false },
                        },
                    },
                },
            });
        }
    });
    </script>
    @endpush
</x-app-layout>