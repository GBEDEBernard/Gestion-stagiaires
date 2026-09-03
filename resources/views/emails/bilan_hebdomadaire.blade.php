@component('mail::message')
# Votre bilan hebdomadaire

Bonjour **{{ $user->name }}**,

Voici votre bilan de présence pour la semaine du **{{ $weekStart->format('d/m/Y') }} au {{ $weekEnd->format('d/m/Y') }}** au sein de **TECHNOLOGY FOREVER GROUP**.

@php
    $totalDays   = $stats['total_days'] ?? 0;
    $presentDays = $stats['present_days'] ?? 0;
    $rate        = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
    $absences    = max(0, $totalDays - $presentDays);
    $lateDays    = $stats['late_days'] ?? 0;
    $lateMinutes = $stats['total_late_minutes'] ?? 0;
    $workedHours = $stats['total_worked_hours'] ?? 0;
@endphp

@component('mail::table')
| | |
|:---|---|
| **Taux de présence** | **{{ $rate }}%** |
| **Jours présents** | {{ $presentDays }} / {{ $totalDays }} jours attendus |
| **Absences** | {{ $absences }} |
| **Retards** | {{ $lateDays }} ({{ $lateMinutes }} minutes) |
| **Heures travaillées** | {{ $workedHours }} h |
| **Rapports soumis** | {{ $reportsCount }} |
@endcomponent

@component('mail::button', ['url' => route('presence.historique')])
Voir mon historique de présence
@endcomponent

**Pour rappel** : il est important de pointer votre présence chaque jour et de soumettre votre rapport journalier en fin de journée. Un taux de présence régulier contribue à un bon suivi de vos activités.

En cas de question ou de difficulté, n'hésitez pas à contacter le service **Technique / IT** à l'adresse **[email de contact]** ou au **[numéro de téléphone]**.

Nous vous remercions pour votre implication.

Cordialement,<br>
**La Direction Technique** — **TECHNOLOGY FOREVER GROUP**
@endcomponent