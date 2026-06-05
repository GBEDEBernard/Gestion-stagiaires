<x-mail::message>
# {{ $greeting }} {{ $recipientCivilite }} {{ $recipientName }},

Un rapport a été soumis sur la tâche **{{ $task->title }}** par **{{ $task->owner->name }}**.

<x-mail::button :url="$taskUrl" color="primary">
Voir le rapport
</x-mail::button>

Cordialement,
{{ config('app.name') }}
</x-mail::message>