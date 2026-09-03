<x-mail::message>
# 🚨 Appel d'urgence

Bonjour **{{ $user->name }}**,

Vous êtes appelé(e) pour une **intervention urgente** le **{{ $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}** ({{ $holiday->label }}).

<x-mail::panel>
{{ $customMessage }}
</x-mail::panel>

Vous avez été automatiquement autorisé(e) à pointer ce jour-là sans restriction. Présentez-vous au site habituel.

<x-mail::button :url="url('/presence/pointage')" color="error">
Pointer mon arrivée
</x-mail::button>

Merci pour votre réactivité,<br>
{{ config('app.name') }}
</x-mail::message>
