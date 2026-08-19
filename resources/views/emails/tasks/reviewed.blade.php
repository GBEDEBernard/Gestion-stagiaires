<x-mail::message>
# {{ $greeting }}@if($recipientCivilite) {{ $recipientCivilite }}@endif {{ $recipientName }},

**{{ $reviewer->name }}** a révisé votre tâche **{{ $task->title }}**.

**Décision :**
@if($action === 'approve') ✅ Validée @else ✏️ Corrections demandées @endif

@if($comment)
**Commentaire :**
{{ $comment }}
@endif

<x-mail::button :url="$taskUrl" color="primary">
Voir la tâche
</x-mail::button>

Cordialement,
{{ config('app.name') }}
</x-mail::message>