@component('mail::message')
# Bonjour {{ $user->name }},

Vous n'avez pas pointé votre départ aujourd'hui. Votre départ a donc été **enregistré automatiquement** par le système.

| Information | Valeur |
|:---|---:|
| Arrivée | {{ $arrivalTime }} |
| Départ (auto) | {{ $departureTime }} |
| Heures travaillées | {{ $workedHours }} |

@component('mail::button', ['url' => url('/historique'), 'color' => 'primary'])
Voir mon historique
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
