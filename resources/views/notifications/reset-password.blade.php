@component('mail::message')
# Réinitialiser votre mot de passe

Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.

@component('mail::button', ['url' => $actionUrl])
Réinitialiser le mot de passe
@endcomponent

Ce lien de réinitialisation de mot de passe expirera dans 60 minutes.

Si vous n'avez pas demandé une réinitialisation de mot de passe, aucune action supplémentaire n'est requise.

Cordialement,<br>
{{ config('app.name') }}

@component('mail::subcopy')
Si vous avez du mal à cliquer sur le bouton "Réinitialiser le mot de passe", copiez et collez l'URL ci-dessous dans votre navigateur:
[{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endcomponent