@component('mail::message')
# Merci pour votre message, {{ $name }} !

Nous avons bien reçu votre message et nous vous répondrons dans les plus brefs délais.

**Votre message :**
{{ $message }}

Si vous avez d'autres questions, n'hésitez pas à nous contacter à [gbedebernard60@gmail.com](mailto:gbedebernard60@gmail.com) ou par téléphone au +229 0166439030 / +229 0169580603.

Cordialement,  
L'équipe Gestion des Stagiaires

@component('mail::button', ['url' => url('/')])
Retourner au site
@endcomponent
@endcomponent