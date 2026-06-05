<x-mail::message>
# {{ $greeting }} {{ $recipientCivilite }} {{ $recipientName }},

Une nouvelle tâche a été créée par **{{ $task->owner->name }}**.

**Titre :** {{ $task->title }}
**Priorité :** {{ $task->priority }}
**Échéance :** {{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Non définie' }}

<x-mail::button :url="$taskUrl" color="primary">
Voir la tâche
</x-mail::button>

Cordialement,
{{ config('app.name') }}
</x-mail::message>