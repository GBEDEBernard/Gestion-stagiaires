<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Impression pointages</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 2cm; background: white; }
        h2, h3 { margin-top: 0; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; page-break-inside: avoid; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f1f5f9; font-weight: 600; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
        .text-center { text-align: center; }
        .mt-4 { margin-top: 1rem; }
        .mb-2 { margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <h2>📋 Suivi des pointages</h2>
    <p><strong>Date / Période :</strong> {{ $displayDate ?? '' }}</p>
    @if(request('school'))
        <p><strong>École :</strong> {{ request('school') }}</p>
    @endif
    @if(request('site_id'))
        @php $site = \App\Models\Site::find(request('site_id')); @endphp
        <p><strong>Site :</strong> {{ $site?->name ?? 'Non trouvé' }}</p>
    @endif
    @if(request('user_id'))
        @php $user = \App\Models\User::find(request('user_id')); @endphp
        <p><strong>Utilisateur :</strong> {{ $user?->name ?? 'Non trouvé' }}</p>
    @endif
    <hr class="mb-4">

    @if(isset($attendanceStudents) && $attendanceStudents->count())
    <h3>🎓 Étudiants</h3>
    <table>
        <thead><tr><th>Nom complet</th><th>Arrivée</th><th>Départ</th><th>Site</th><th>Statut</th><th>Retard</th></tr></thead>
        <tbody>
            @foreach($attendanceStudents as $day)
            @php
                $statut = $day->first_check_in_at ? (($day->first_check_in_at->hour < 8 || ($day->first_check_in_at->hour == 7 && $day->first_check_in_at->minute <= 45)) ? 'À l\'heure' : (($day->first_check_in_at->hour == 7 && $day->first_check_in_at->minute > 45) ? 'En retard (léger)' : 'Retard important')) : 'Absent';
                $siteName = $day->stage?->site?->name ?? 'À distance';
            @endphp
            <tr>
                <td>{{ $day->etudiant?->user?->name ?? 'N/A' }}</td>
                <td>{{ $day->first_check_in_at?->format('H:i') ?? '—' }}</td>
                <td>{{ $day->last_check_out_at?->format('H:i') ?? '—' }}</td>
                <td>{{ $siteName }}</td>
                <td>{{ $statut }}</td>
                <td>{{ $day->late_minutes > 0 ? $day->late_minutes.' min' : '0' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(isset($attendanceEmployees) && $attendanceEmployees->count())
    <h3>👔 Employés</h3>
    <table>
        <thead><tr><th>Nom</th><th>Arrivée</th><th>Départ</th><th>Site</th><th>Statut</th><th>Retard</th></tr></thead>
        <tbody>
            @foreach($attendanceEmployees as $day)
            @php
                $statut = $day->first_check_in_at ? (($day->first_check_in_at->hour < 8 || ($day->first_check_in_at->hour == 7 && $day->first_check_in_at->minute <= 45)) ? 'À l\'heure' : (($day->first_check_in_at->hour == 7 && $day->first_check_in_at->minute > 45) ? 'En retard (léger)' : 'Retard important')) : 'Absent';
                $siteName = $day->stage?->site?->name ?? 'À distance';
            @endphp
            <tr>
                <td>{{ $day->user?->name ?? 'N/A' }}</td>
                <td>{{ $day->first_check_in_at?->format('H:i') ?? '—' }}</td>
                <td>{{ $day->last_check_out_at?->format('H:i') ?? '—' }}</td>
                <td>{{ $siteName }}</td>
                <td>{{ $statut }}</td>
                <td>{{ $day->late_minutes > 0 ? $day->late_minutes.' min' : '0' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if((!isset($attendanceStudents) || $attendanceStudents->isEmpty()) && (!isset($attendanceEmployees) || $attendanceEmployees->isEmpty()))
        <p class="text-center">Aucune donnée de pointage pour les critères sélectionnés.</p>
    @endif

    <div class="mt-4 text-muted" style="font-size: 0.75rem; text-align: center;">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>