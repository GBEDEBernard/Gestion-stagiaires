<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Pointages</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 30px;
            color: #1e293b;
        }
        h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 20px;
        }
        .filters {
            background: #f8fafc;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 20px;
        }
        .filters span {
            margin-right: 15px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        thead {
            background: #0f172a;
            color: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: top;
        }
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }
        .in { background: #dcfce7; color: #166534; }
        .out { background: #dbeafe; color: #1e3a8a; }
        .approved { background: #dcfce7; color: #166534; }
        .rejected { background: #fee2e2; color: #991b1b; }
        .footer {
            margin-top: 20px;
            font-size: 11px;
            text-align: right;
            color: #64748b;
        }
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .print-btn { background: #2563eb; color: white; }
        .close-btn { background: #e5e7eb; }
        @media print {
            body { margin: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">

    <h1>Rapport des pointages</h1>
    <div class="subtitle">
        Généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

    <div class="filters">
        <span><strong>Date :</strong> {{ $date ?: 'Toutes' }}</span>
        <span><strong>Utilisateur :</strong> {{ $userId ?: 'Tous' }}</span>
        <span><strong>Site :</strong> {{ $siteId ?: 'Tous' }}</span>
        <span><strong>École :</strong> {{ $schoolFilter ?: 'Toutes' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>Utilisateur</th>
                <th>Journée</th>
                <th>Arrivée / Départ</th>
                <th>Site</th>
                <th>Précision</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($days as $day)
            @php
                $userName = $day->user?->name ?? $day->etudiant?->user?->name ?? 'N/A';
                $checkIn = $day->checkInEvent;
                $checkOut = $day->checkOutEvent;
                $hasCheckIn = !is_null($checkIn);
                $hasCheckOut = !is_null($checkOut);
                $precision = $checkIn?->accuracy_meters ?? $checkOut?->accuracy_meters ?? 0;
                $status = $day->status ?? 'pending';
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $userName }}</td>
                <td>{{ $day->attendance_date->format('d/m/Y') }}</td>
                <td>
                    @if($hasCheckIn)
                        <span class="badge in">Arrivée</span> {{ $checkIn->occurred_at->format('H:i') }}<br>
                    @else
                        <span class="badge" style="background:#f1f5f9;">Arrivée —</span><br>
                    @endif
                    @if($hasCheckOut)
                        <span class="badge out">Départ</span> {{ $checkOut->occurred_at->format('H:i') }}
                    @else
                        <span class="badge" style="background:#f1f5f9;">Départ —</span>
                    @endif
                </td>
                <td>{{ $day->resolved_site_name ?? 'À distance' }}</td>
                <td>{{ number_format($precision, 0) }} m</td>
                <td>
                    <span class="badge {{ $status === 'approved' ? 'approved' : 'rejected' }}">
                        {{ ucfirst($status) }}
                    </span>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:20px;">Aucun pointage trouvé</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total résultats : {{ $days->count() }}
    </div>

    <div class="actions">
        <button class="print-btn" onclick="window.print()">🖨️ Imprimer</button>
        <button class="close-btn" onclick="window.close()">Fermer</button>
    </div>
</body>
</html>