@component('mail::message')
# Réinitialisation de mot de passe

Vous avez demandé à réinitialiser votre mot de passe.

Voici votre code de vérification (valide pendant **15 minutes**) :

@component('mail::panel')
# {{ $pin }}
@endcomponent

Ne partagez ce code avec personne.

Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune action supplémentaire n'est requise.

Cordialement,<br>
{{ config('app.name') }}
@endcomponent