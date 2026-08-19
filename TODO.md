# Plan d'implémentation — Géolocalisation des rapports + Télétravail

## Contexte
- **Q1** : Vérifier la position exacte (≤ 25 m du site) avant de soumettre un rapport.
- **Q2** : Après le pointage de départ (check-out), seuls les utilisateurs avec une tâche assignée par l'admin ET le flag « télétravail » actif peuvent soumettre des rapports depuis la maison.

## Étapes

- [x] 1. Créer la migration : colonnes géo sur `daily_reports` + flag `remote_work_enabled` sur `users`
- [x] 2. Modifier `PresenceService` : distance max 100 → 25 m + exposer `calculateDistanceMeters` / `verifyLocationOnSite`
- [x] 3. Ajouter helper `canWorkRemotely()` / `hasAssignedActiveTask()` sur le modèle `User`
- [x] 4. Ajouter la vérification géo + garde de fermeture/télétravail dans `DailyReportService`
- [x] 5. Mettre à jour `DailyReportController` (exiger loc + messages d'erreur)
- [x] 6. Mettre à jour `StoreDailyReportRequest` (champs géo)
- [x] 7. Ajouter la capture GPS aux formulaires de rapport (vue detail + index)
- [x] 8. Ajouter la permission `tasks.assign` dans les configs permissions
- [x] 9a. Assignation de tâches par admin/superviseur : page dédiée + logique `TaskController::assign`
- [x] 9b. Bouton « Assigner une tâche » dans le workspace (rôle admin/superviseur) + routes
- [x] 9c. Toggle « Télétravail autorisé » dans le formulaire admin user + logique `UserController`
- [x] 10. Lancer la migration et tester (migration + seeder `tasks.assign` OK, routes & colonnes vérifiées)

