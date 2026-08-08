@component('mail::message')
# Vos identifiants d'accès à la plateforme

Bonjour **{{ $fullName }}**,

Dans le cadre de l'amélioration du suivi de la présence et des activités au sein de **TECHNOLOGY FOREVER GROUP**, nous mettons à votre disposition une plateforme dédiée qui vous permettra de :

- **Marquer votre présence** quotidienne dans l'entreprise
- **Rédiger votre rapport journalier** d'activités
- **Enregistrer et suivre** l'ensemble des tâches réalisées durant la journée

Veuillez trouver ci-dessous vos identifiants de connexion :

@component('mail::table')
| | |
|:---|---|
| **Lien de connexion** | [https://espace.tfgbusiness.com](https://espace.tfgbusiness.com) |
| **Identifiant** | `{{ $email }}` |
@if($password)
| **Mot de passe** | `{{ $password }}` |
@endif
@endcomponent

@component('mail::button', ['url' => $resetUrl])
Configurer mon mot de passe
@endcomponent

Nous vous invitons à vous connecter chaque jour afin de marquer votre présence et de renseigner, en fin de journée, votre rapport d'activités ainsi que les tâches effectuées. Cette démarche nous permettra d'assurer un meilleur suivi du travail de chacun et de faciliter la communication entre les équipes.

**Pour des raisons de sécurité**, nous vous recommandons de :
- Modifier votre mot de passe dès votre première connexion
- Ne partager vos identifiants avec **personne**
- Vous déconnecter après chaque utilisation, notamment sur un poste partagé

Vous pouvez également accéder directement à la plateforme depuis le lien suivant : [https://presence.tfgbusiness.com](https://presence.tfgbusiness.com)

En cas de difficulté pour accéder à la plateforme ou pour toute question, n'hésitez pas à contacter le **service Technique / IT** à l'adresse **edi002008@yahoo.fr** ou au **(00229) 01 65 103 959**.

Nous vous remercions par avance pour votre collaboration.

Cordialement,

**La Direction Technique**<br>
**TECHNOLOGY FOREVER GROUP**

--
Edino S.Mario AGBELESSESSI<br>
Directeur Technique Adjoint – TECHNOLOGY FOREVER GROUP Sarl

Site web : [www.tfgbusiness.com](https://www.tfgbusiness.com) • [www.easylmd.com](https://www.easylmd.com)<br>
Mail : edi002008@yahoo.fr / edinoagbelessessi@gmail.com
@endcomponent