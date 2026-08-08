<x-mail::message>
# 📅 Jour férié : {{ $holiday->label }}

Bonjour **{{ $user->name }}**,

La direction vous informe que le **{{ $holiday->date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}** est déclaré **jour férié** ({{ $holiday->label }}).

<x-mail::panel>
Le pointage est désactivé ce jour-là : vous n'avez **pas à vous présenter** au site.

En cas d'intervention urgente, vous recevrez un email d'appel séparé vous autorisant à pointer et à travailler ce jour-là.
</x-mail::panel>

<x-mail::button :url="url('/presence/pointage')" color="primary">
Voir le pointage
</x-mail::button>

Bonne journée,<br>
{{ config('app.name') }}
</x-mail::message>
