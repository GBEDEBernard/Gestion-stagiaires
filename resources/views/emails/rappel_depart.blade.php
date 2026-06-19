@component('mail::message')
# Bonjour {{ $user->name }},

Vous avez pointé votre arrivée à **{{ $arrivalTime }}** ({{ $lateMinutes > 0 ? $lateMinutes . ' min de retard' : 'à l\'heure' }}), mais vous **n'avez pas encore pointé votre départ**.

@component('mail::button', ['url' => url('/pointage'), 'color' => 'warning'])
Pointer mon départ
@endcomponent

Si vous ne le faites pas avant **19h30**, votre départ sera enregistré automatiquement.

Merci,<br>
{{ config('app.name') }}
@endcomponent
