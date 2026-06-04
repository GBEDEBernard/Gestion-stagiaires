<x-mail::message>
# Bonjour {{ $recipientName }},

Un rapport a été soumis sur la tâche **{{ $task->title }}** par **{{ $task->owner->name }}**.

<x-mail::button :url="$taskUrl">
Voir le rapport
</x-mail::button>

Cordialement,  
{{ config('app.name') }}
</x-mail::message>