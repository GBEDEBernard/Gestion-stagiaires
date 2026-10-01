@component('mail::message')
# Bonjour {{ $user->name }},

Vous avez pointé votre arrivée du **{{ $attendanceDate }}** mais pas votre départ. Le système a donc clôturé la journée d'office.

| Information | Valeur |
|:---|---:|
| Arrivée | {{ $arrivalTime }} |
| Départ (clôturé d'office) | {{ $departureTime }} |
| Heures comptabilisées | {{ $workedHours }} |

@if($reportMissing)
Votre rapport de travail du **{{ $attendanceDate }}** n'a pas non plus été déposé.
@endif

**Ces heures ne comptent pas encore.** Sans votre pointage de départ, le temps
travaillé de cette journée reste à zéro : la clôture automatique ne sert qu'à
éviter que la journée reste ouverte indéfiniment.

## Pour faire compter cette journée

1. Connectez-vous et allez sur votre page de pointage.
2. Une fenêtre vous proposera de **déclarer l'heure à laquelle vous êtes parti**.
3. Écrivez cette heure et la raison de votre absence de pointage, puis validez.

Votre responsable reçoit alors votre déclaration et peut rétablir votre heure
réelle de départ. Lui seul le peut.

@component('mail::button', ['url' => url('/pointage'), 'color' => 'primary'])
Déclarer mon heure de départ
@endcomponent

@component('mail::button', ['url' => url('/historique'), 'color' => 'gray'])
Voir mon historique
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent