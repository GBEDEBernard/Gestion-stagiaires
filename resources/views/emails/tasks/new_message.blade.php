<x-mail::message>
# Bonjour {{ $recipientName }},

**{{ $sender->name }}** a posté un nouveau message sur la tâche **{{ $task->title }}**.

**Message :**  
{{ $messageBody }}

<x-mail::button :url="$taskUrl">
Voir la discussion
</x-mail::button>

Cordialement,  
{{ config('app.name') }}
</x-mail::message>