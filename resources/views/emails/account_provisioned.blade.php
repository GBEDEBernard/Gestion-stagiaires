@component('mail::message')
# Bonjour {{ $civility ?? '' }} {{ $fullName }},

Dans le cadre de l'amélioration du suivi de la présence et des activités au sein de **TECHNOLOGY FOREVER GROUP**, nous mettons à votre disposition une plateforme dédiée qui vous permettra de :

- Marquer votre présence quotidienne dans l'entreprise
- Rédiger votre rapport journalier d'activités
- Enregistrer et suivre l'ensemble des tâches réalisées durant la journée

Veuillez trouver ci-dessous vos identifiants de connexion :

**Lien de connexion**  
[{{ config('app.url') }}]({{ config('app.url') }})

**Identifiant**  
{{ $email }}

***Mot de passe**  
{{ $password }}

Nous vous invitons à vous connecter chaque jour afin de marquer votre présence et de renseigner, en fin de journée, votre rapport d'activités ainsi que les tâches effectuées. Cette démarche nous permettra d'assurer un meilleur suivi du travail de chacun et de faciliter la communication entre les équipes.

**Pour des raisons de sécurité, nous vous recommandons de :**

- Modifier votre mot de passe dès votre première connexion
- Ne partager vos identifiants avec personne
- Vous déconnecter après chaque utilisation, notamment sur un poste partagé

En cas de difficulté pour accéder à la plateforme ou pour toute question, n'hésitez pas à contacter le service Direction Techniques à l'adresse techforevergroup@gmail.com ou au +229 01 69 58 06 03 / 01 65 10 39 59.

Nous vous remercions par avance pour votre collaboration.

Cordialement,

**La Direction Technique**  
TECHNOLOGY FOREVER GROUP

---
© {{ date('Y') }} Technology forever group. All rights reserved.
@endcomponent