# TODO - Système d'activation de compte par email

## Étapes à compléter:

- [ ]   1. Modifier User model pour implémenter MustVerifyEmail
- [ ]   2. Créer la notification VerifyEmailNotification
- [ ]   3. Créer le template d'email de vérification
- [ ]   4. Modifier RegisteredUserController pour envoyer l'email immédiatement
- [ ]   5. Tester et nettoyer le cache

## Problème résolu:

- L'email d'activation n'était pas envoyé car le modèle User n'implémentait pas MustVerifyEmail
