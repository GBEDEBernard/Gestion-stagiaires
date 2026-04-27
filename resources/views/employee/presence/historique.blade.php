<x-app-layout title="Historique de présence - Employé">
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

        * { box-sizing: border-box; }

        .pres-wrap {
            font-family: var(--font);
            color: var(--text);
            background: var(--bg);
            min-height: 100vh;
        }

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

        .pres-page {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem 1.5rem 3rem;
        }

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

        .pres-filter-panel {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1rem;
            padding: 1rem;
            margin-top: 1rem;
            color: var(--muted);
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
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
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
            .pres-filter-grid { grid-template-columns: repeat(2, 1fr); }
            .pres-filter-actions { grid-column: span 2; }
        }

        @media (max-width: 640px) {
            .pres-filter-grid { grid-template-columns: 1fr; }
            .pres-filter-actions { grid-column: span 1; }
            .pres-filter-actions .pres-btn { flex: 1; }
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
            cursor: pointer;
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

        .pres-kpis {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        @media(max-width:900px) {
            .pres-kpis { grid-template-columns: repeat(2, 1fr); }
        }

        @media(max-width:500px) {
            .pres-kpis { grid-template-columns: 1fr; }
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

        .kpi-emerald .pres-kpi-accent { background: var(--emerald); }
        .kpi-emerald .pres-kpi-icon { background: rgba(16, 185, 129, .12); color: var(--emerald); }
        .kpi-emerald .pres-kpi-badge { background: rgba(16, 185, 129, .12); color: var(--emerald); }

        .kpi-amber .pres-kpi-accent { background: var(--amber); }
        .kpi-amber .pres-kpi-icon { background: rgba(245, 158, 11, .12); color: var(--amber); }
        .kpi-amber .pres-kpi-badge { background: rgba(245, 158, 11, .12); color: var(--amber); }

        .kpi-blue .pres-kpi-accent { background: var(--blue); }
        .kpi-blue .pres-kpi-icon { background: rgba(59, 130, 246, .12); color: var(--blue); }
        .kpi-blue .pres-kpi-badge { background: rgba(59, 130, 246, .12); color: var(--blue); }

        .kpi-rose .pres-kpi-accent { background: var(--rose); }
        .kpi-rose .pres-kpi-icon { background: rgba(244, 63, 94, .12); color: var(--rose); }
        .kpi-rose .pres-kpi-badge { background: rgba(244, 63, 94, .12); color: var(--rose); }

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
            height: 280px;
        }

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

        .mt-6 { margin-top: 1.5rem; }
        .mt-4 { margin-top: 1rem; }

        @media(max-width: 1024px) {
            .pres-page { padding: 1rem 1rem 2.5rem; }
            .pres-header { gap: .75rem; }
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
            .pres-header-title { font-size: 1.45rem; }
            .pres-header-sub { font-size: .82rem; }
            .pres-tab {
                flex: 1 1 auto;
                min-width: 120px;
            }
        }

        .mobile-cards { display: none; }
        .desktop-table { display: block; }

        @media(max-width: 768px) {
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

        .mobile-card-row:last-child {
            border-bottom: none;
        }

        .mobile-label {
            color: var(--muted);
            font-size: .8rem;
        }

        .mobile-value {
            font-weight: 600;
            font-size: .85rem;
        }
    </style>

    <div class="pres-wrap">
        <div class="pres-header">
            <div class="pres-header-left">
                <div class="pres-header-badge">Espace employé</div>
                <h1 class="pres-header-title">Mon historique de présence</h1>
                <p class="pres-header-sub">Vue personnelle · Statistiques détaillées</p>
            </div>
            <div class="pres-header-actions">
                <a href="{{ route('presence.pointage') }}" class="pres-btn-export">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Nouveau pointage
                </a>
            </div>
        </div>

        <div class="pres-page">
            {{-- PERIOD TABS --}}
            <div class="pres-tabs">
                @foreach(['today'=>"Aujourd'hui",'week'=>'Semaine','month'=>'Mois','year'=>'Année'] as $k=>$lbl)
                <a href="?period={{ $k }}" class="pres-tab {{ request('period',$k==='today'?'today':null)===$k?'active':'' }}">
                    {{ $lbl }}
                    @if(request('period')===$k)<span class="pres-tab-indicator"></span>@endif
                </a>
                @endforeach
            </div>

            {{-- FILTRE DATE --}}
            <div class="pres-filter-panel">
                <form method="GET" action="{{ route('presence.historique') }}" class="pres-filter-form">
                    <input type="hidden" name="period" value="custom" />

                    <div class="pres-filter-grid">
                        <div class="pres-filter-field">
                            <label>Du</label>
                            <input type="date" name="date_from"
                                value="{{ request('date_from', today()->format('Y-m-d')) }}"
                                class="pres-filter-input" />
                        </div>

                        <div class="pres-filter-field">
                            <label>Au</label>
                            <input type="date" name="date_to"
                                value="{{ request('date_to', today()->format('Y-m-d')) }}"
                                class="pres-filter-input" />
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
                        {{ ($userStats['total_days'] ?? 0) > 0 ? round((($userStats['present_days'] ?? 0) / ($userStats['total_days'] ?? 1)) * 100, 1) : 0 }}%
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
                        <span class="pres-kpi-badge">+{{ $userStats['total_worked_hours'] ?? 0 }}h</span>
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
                    <div class="pres-kpi-value">
                        {{ ($userStats['total_days'] ?? 0) - ($userStats['present_days'] ?? 0) }}
                    </div>
                    <div class="pres-kpi-label">Absences</div>
                    <div class="pres-kpi-sub">{{ $userStats['open_anomalies'] ?? 0 }} anomalies ouvertes</div>
                </div>
            </div>

            {{-- CHARTS --}}
            @if(isset($userStats['chart_data']) && count($userStats['chart_data']['labels'] ?? []) > 0)
            <div class="mt-6">
                <div class="pres-section-title">Évolution · Présence & Ponctualité</div>
                <div class="pres-card">
                    <div class="pres-chart-wrap">
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
            @endif

            {{-- TABLEAU DES POINTAGES --}}
            <div class="pres-table-card mt-6">
                <div class="pres-table-head">
                    <span class="pres-table-head-title">📋 Pointages récents</span>
                    <span class="pres-table-head-meta">
                        Période : {{ $period === 'custom' ? (request('date_from') . ' → ' . request('date_to')) : ucfirst($period ?? 'mois') }}
                        · {{ $attendanceDays->count() }} jours
                    </span>
                </div>

                {{-- Desktop Table --}}
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
                                <td style="font-family:var(--mono);">{{ round($day->worked_minutes / 60, 1) }}h</td>
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="pres-empty">
                                        <div class="pres-empty-icon">📭</div>
                                        Aucun pointage trouvé pour cette période
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="mobile-cards" style="padding: 1rem;">
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
                            <span class="mobile-value">{{ round($day->worked_minutes / 60, 1) }}h</span>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Chart.defaults.font.family = "'DM Sans', sans-serif";
            Chart.defaults.color = '#9ca3af';

            const chartData = @json($userStats['chart_data'] ?? []);
            const labels = chartData.labels ?? [];
            const present = chartData.present ?? [];
            const onTime = chartData.on_time ?? [];
            const lateDays = chartData.late_days ?? [];
            const absences = chartData.absences ?? [];
            const lateMinutes = chartData.late_minutes ?? [];
            const workedHours = chartData.worked_hours ?? [];

            const createGradient = (ctx, colorStart, colorEnd) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, colorStart);
                gradient.addColorStop(1, colorEnd);
                return gradient;
            };

            // Graphique ligne principal
            const ctxG = document.getElementById('chartGlobal');
            if (ctxG && labels.length > 0) {
                const g = ctxG.getContext('2d');

                new Chart(ctxG, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: '✅ Présence',
                                data: present,
                                borderColor: '#10b981',
                                backgroundColor: createGradient(g, 'rgba(16,185,129,0.25)', 'rgba(16,185,129,0.02)'),
                                fill: true,
                                stepped: 'before',
                                tension: 0,
                                borderWidth: 2.5,
                                pointRadius: present.map(v => v > 0 ? 6 : 0),
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                yAxisID: 'yBinary'
                            },
                            {
                                label: "🟢 À l'heure",
                                data: onTime,
                                borderColor: '#3b82f6',
                                backgroundColor: createGradient(g, 'rgba(59,130,246,0.18)', 'rgba(59,130,246,0.02)'),
                                fill: true,
                                stepped: 'before',
                                tension: 0,
                                borderWidth: 2.5,
                                pointRadius: onTime.map(v => v > 0 ? 6 : 0),
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                yAxisID: 'yBinary'
                            },
                            {
                                label: '⚠️ Jours retard',
                                data: lateDays,
                                borderColor: '#f59e0b',
                                backgroundColor: createGradient(g, 'rgba(245,158,11,0.18)', 'rgba(245,158,11,0.02)'),
                                fill: true,
                                stepped: 'before',
                                tension: 0,
                                borderWidth: 2.5,
                                pointRadius: lateDays.map(v => v > 0 ? 6 : 0),
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#f59e0b',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                yAxisID: 'yBinary'
                            },
                            {
                                label: '📉 Absences',
                                data: absences,
                                borderColor: '#f43f5e',
                                backgroundColor: createGradient(g, 'rgba(244,63,94,0.18)', 'rgba(244,63,94,0.02)'),
                                fill: true,
                                stepped: 'before',
                                tension: 0,
                                borderWidth: 2.5,
                                pointRadius: absences.map(v => v > 0 ? 6 : 0),
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#f43f5e',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                yAxisID: 'yBinary'
                            },
                            {
                                label: '⏱ Retards (min)',
                                data: lateMinutes,
                                borderColor: '#f59e0b',
                                backgroundColor: createGradient(g, 'rgba(245,158,11,0.08)', 'rgba(245,158,11,0.01)'),
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#f59e0b',
                                yAxisID: 'yMinutes'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 1000, easing: 'easeOutQuart' },
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { labels: { color: '#e5e7eb', usePointStyle: true } },
                            tooltip: { backgroundColor: '#111827', borderColor: '#374151', borderWidth: 1 }
                        },
                        scales: {
                            x: { grid: { color: 'rgba(255,255,255,0.05)' } },
                            yBinary: {
                                beginAtZero: true,
                                max: 1,
                                position: 'left',
                                title: { display: true, text: 'Présence (0/1)', color: '#9ca3af' },
                                grid: { color: 'rgba(255,255,255,0.05)' }
                            },
                            yMinutes: {
                                beginAtZero: true,
                                position: 'right',
                                title: { display: true, text: 'Minutes de retard', color: '#f59e0b' },
                                grid: { drawOnChartArea: false }
                            }
                        }
                    }
                });
            }

            // Graphique barres
            const ctxOv = document.getElementById('chartOverview');
            if (ctxOv && labels.length > 0) {
                new Chart(ctxOv, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Présents', data: present, backgroundColor: '#10b981' },
                            { label: "À l'heure", data: onTime, backgroundColor: '#3b82f6' },
                            { label: 'Retards (min)', data: lateMinutes, backgroundColor: '#f59e0b' },
                            { label: 'Absences', data: absences, backgroundColor: '#f43f5e' }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 800 },
                        plugins: { legend: { labels: { color: '#e5e7eb' } } },
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>