# TODO - Corrections activation compte (admin)

- [ ] Mettre à jour `app/Notifications/AccountProvisionedNotification.php` pour construire l’URL de reset à partir du token Laravel (au lieu d’une URL pré-construite / mot de passe en clair).
- [ ] Mettre à jour `app/Services/AccountGenerationService.php` pour passer au besoin (token, email) à la notification et/ou aligner exactement les paramètres attendus par le broker Laravel.
- [ ] Mettre à jour `app/Http/Controllers/Auth/NewPasswordController.php` pour remettre `must_change_password=false` après reset.
- [ ] Rechercher d’éventuels autres appels à `AccountProvisionedNotification` et les adapter.
- [ ] Lancer un test manuel : création admin → email → clic → reset → login → dashboard.

