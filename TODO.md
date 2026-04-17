# TODO: Correction Erreur Pagination Notifications

**Statut: En cours** | **Plan approuvé par utilisateur**

## Étapes (5/5 restantes):

- [x]   1. Modifier `app/Http/ViewComposers/NotificationComposer.php` (renommer `$notifications` → `$menuNotifications`)\n- [x] 2. Modifier `resources/views/notifications/index.blade.php` (gérer pagination conditionnelle)
- [x]   3. Vérifier + corriger `app/Providers/AppServiceProvider.php` et layouts (ViewComposer `*` confirmé)
- [x]   4. Nettoyer caches ✅ (`php artisan view:clear && php artisan view:cache && php artisan route:clear && php artisan config:clear`)
- [x]   5. **TÂCHE TERMINÉE** ✅\n\nRoutes vérifiées: `notifications.index` OK\nCaches nettoyés\nFichiers corrigés\n\n**Testez:** `php artisan serve` puis visitez `http://127.0.0.1:8000/notifications`

**Notes:**

- Garder `$notificationCount` pour menu/bandeau
- Contrôleur déjà correct (paginate(15))
- Tout en français
