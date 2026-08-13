<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Pointages</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            padding: 18px;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .report-header h1 {
            font-size: 21px;
            margin: 0 0 3px 0;
            color: #0f172a;
            letter-spacing: .2px;
        }
        .report-header .brand {
            font-size: 12px;
            color: #475569;
            font-weight: 600;
        }
        .report-header .meta {
            text-align: right;
            font-size: 11px;
            color: #64748b;
        }
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 24px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 14px;
            font-size: 11px;
        }
        .filters strong { color: #0f172a; }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }
        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            background: #fff;
        }
        .summary-card .label {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 700;
        }
        .summary-card .value { font-size: 17px; font-weight: 800; }
        .summary-card.green .value { color: #15803d; }
        .summary-card.red .value { color: #b91c1c; }
        .summary-card.amber .value { color: #b45309; }
        .summary-card.slate .value { color: #334155; }
        .summary-card.indigo .value { color: #4338ca; }
        .summary-card.blue .value { color: #1d4ed8; }

        .user-block {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 16px;
            page-break-inside: avoid;
            overflow: hidden;
        }
        .user-block.keep-with { page-break-inside: auto; }
        .user-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px 20px;
            background: #0f172a;
            color: #fff;
            padding: 9px 14px;
        }
        .user-head .name { font-size: 13px; font-weight: 700; }
        .user-head .infos { font-size: 10.5px; color: #cbd5e1; }
        .user-head .totals { display: flex; gap: 14px; font-size: 10.5px; font-weight: 700; }
        .user-head .totals .ok { color: #4ade80; }
        .user-head .totals .ko { color: #f87171; }
        .user-head .totals .late { color: #fbbf24; }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        thead { background: #eef2f7; }
        th, td {
            padding: 6px 9px;
            border: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: middle;
            font-size: 10.5px;
        }
        th {
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: .4px;
            color: #334155;
            font-weight: 800;
        }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr.absent-row { background: #fef2f2; }
        tbody tr.corrected-row { background: #f1f5f9; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9.5px;
            font-weight: 700;
        }
        .badge.ok { background: #dcfce7; color: #166534; }
        .badge.late { background: #fee2e2; color: #991b1b; }
        .badge.absent { background: #fee2e2; color: #991b1b; }
        .badge.corrected { background: #e2e8f0; color: #334155; }
        .time { font-weight: 700; }
        .time.arriv { color: #15803d; }
        .time.dep { color: #1d4ed8; }
        .retard { color: #b91c1c; font-weight: 700; }
        .day-label { font-weight: 600; }
        .day-label small { color: #94a3b8; font-weight: 400; }
        .report-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #64748b;
        }
        .actions { margin-top: 14px; text-align: center; }
        button {
            padding: 9px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 12px;
        }
        .print-btn { background: #2563eb; color: white; }
        .close-btn { background: #e5e7eb; }
        @media print {
            .actions { display: none; }
            body { padding: 0; }
        }
        .page-number {
            position: fixed;
            bottom: 8px;
            right: 14px;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1200);">

    <div class="report-header">
        <div>
            <h1>Rapport des Pointages</h1>
            <div class="brand">TFG · Suivi de présence — Stagiaires &amp; Employés</div>
        </div>
        <div class="meta">
            <strong>Généré le</strong> {{ now()->format('d/m/Y à H:i') }}<br>
            <strong>Période du</strong> {{ \Illuminate\Support\Carbon::parse($dateFrom)->isoFormat('DD/MM/YYYY') }}
            au {{ \Illuminate\Support\Carbon::parse($dateTo)->isoFormat('DD/MM/YYYY') }}<br>
            {{ count($detail) }} utilisateur(s), {{ $globalTotals['present'] + $globalTotals['absent'] + $globalTotals['corrected'] }} journée(s)
        </div>
    </div>

    <div class="filters">
        <span><strong>Du :</strong> {{ \Illuminate\Support\Carbon::parse($dateFrom)->isoFormat('DD/MM/YYYY') }}</span>
        <span><strong>Au :</strong> {{ \Illuminate\Support\Carbon::parse($dateTo)->isoFormat('DD/MM/YYYY') }}</span>
        <span><strong>Période :</strong> {{ match ($period) { 'week' => 'Semaine', 'month' => 'Mois', 'custom' => 'Personnalisée', default => 'Jour' } }}</span>
        <span><strong>Utilisateur :</strong> {{ $userId ? $detail->firstWhere('user.id', (int) $userId)['user']?->name ?? 'Tous' : 'Tous' }}</span>
        <span><strong>Site :</strong> {{ $siteId ?: 'Tous' }}</span>
        <span><strong>École :</strong> {{ $schoolFilter ?: 'Toutes' }}</span>
    </div>

    <div class="summary-grid">
        <div class="summary-card slate">
            <div class="label">Utilisateurs</div>
            <div class="value">{{ $globalTotals['users'] }}</div>
        </div>
        <div class="summary-card green">
            <div class="label">Jours présents</div>
            <div class="value">{{ $globalTotals['present'] }}</div>
        </div>
        <div class="summary-card red">
            <div class="label">Jours d'absence</div>
            <div class="value">{{ $globalTotals['absent'] }}</div>
        </div>
        <div class="summary-card amber">
            <div class="label">Retard cumulé</div>
            <div class="value">{{ formatMinutes($globalTotals['late_minutes']) }}</div>
        </div>
        <div class="summary-card indigo">
            <div class="label">Heures pointées</div>
            <div class="value">{{ round($globalTotals['worked_minutes'] / 60, 1) }}h</div>
        </div>
        <div class="summary-card blue">
            <div class="label">Jours corrigés</div>
            <div class="value">{{ $globalTotals['corrected'] }}</div>
        </div>
    </div>

    @forelse($detail as $block)
    @php
        $u = $block['user'];
        $t = $block['totals'];
        $workedHours = round($t['worked_minutes'] / 60, 1);
    @endphp
    <div class="user-block {{ count($block['days']) > 12 ? 'keep-with' : '' }}">
        <div class="user-head">
            <div>
                <span class="name">{{ $u->name }}</span>
                <span class="infos">
                    &nbsp;·&nbsp; {{ $block['group'] === 'etudiant' ? 'Stagiaire' : 'Employé' }}
                    @if($block['school']) &nbsp;·&nbsp; {{ $block['school'] }} @endif
                    @if($block['site_name']) &nbsp;·&nbsp; {{ $block['site_name'] }} @endif
                </span>
            </div>
            <div class="totals">
                <span class="ok">✓ {{ $t['present'] }} présents</span>
                <span class="ko">✗ {{ $t['absent'] }} absences</span>
                @if($t['corrected'] > 0)<span>◌ {{ $t['corrected'] }} corrigés</span>@endif
                <span class="late">⏱ Retard : {{ formatMinutes($t['late_minutes']) }}</span>
                <span>🕒 {{ $workedHours }}h</span>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:14%">Journée</th>
                    <th>Arrivée</th>
                    <th>Départ</th>
                    <th>Site</th>
                    <th style="width:9%">Distance</th>
                    <th style="width:10%">Retard</th>
                    <th style="width:10%">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($block['days'] as $day)
                <tr class="{{ $day['absent'] ? 'absent-row' : ($day['corrected'] ? 'corrected-row' : '') }}">
                    <td class="day-label">{{ $day['date']->locale('fr')->isoFormat('dddd D MMM YYYY') }}</td>
                    <td>@if($day['arrival'])<span class="time arriv">{{ $day['arrival'] }}</span>@else — @endif</td>
                    <td>@if($day['departure'])<span class="time dep">{{ $day['departure'] }}</span>@else — @endif</td>
                    <td>{{ $day['site_name'] ?? '—' }}</td>
                    <td>{{ $day['distance'] ?? '—' }}</td>
                    <td>@if($day['late_minutes'] > 0)<span class="retard">-{{ formatMinutes($day['late_minutes']) }}</span>@else — @endif</td>
                    <td>
                        @if($day['status'] === 'on_time')<span class="badge ok">À l'heure</span>
                        @elseif($day['status'] === 'late')<span class="badge late">En retard</span>
                        @elseif($day['status'] === 'absent')<span class="badge absent">Absent</span>
                        @else<span class="badge corrected">Corrigé</span>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <div style="text-align:center; padding:40px 0; color:#64748b; border:1px dashed #cbd5e1; border-radius:10px;">
        Aucun pointage ni absence pour cette période et ces filtres.
    </div>
    @endforelse

    <div class="report-footer">
        <span>Rapport des pointages — TFG {{ now()->year }}</span>
        <span>Page 1 / 1</span>
    </div>

    <div class="actions">
        <button class="print-btn" onclick="window.print()">Imprimer</button>
        <button class="close-btn" onclick="window.close()">Fermer</button>
    </div>
</body>
</html>