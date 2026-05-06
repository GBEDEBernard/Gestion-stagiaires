# TODO_POINTAGE_IMPLEMENTATION.md - Implémentation Pointage Temps Réel TFG SARL

**Plan approuvé le {{ now()->format('d/m/Y H:i') }}**

## Information Gathered (Complète ✅)

- Couleurs déjà implémentées dans PresenceService::computeArrivalStatus()
- Geofence support complet (SiteGeofence, distance calc 5m)
- UI pointage.blade.php prête (couleurs dynamiques FR)
- Manque: Données TFG SARL + JS GPS complet

## Étapes (0/5 complètes)

### 1. **Créer SiteGeofenceSeeder** (Priorité 1) ✅\n- [✅] `database/seeders/SiteGeofenceSeeder.php` avec TFG SARL coords\n- [✅] `php artisan db:seed --class=SiteGeofenceSeeder` (site_id=4)

### 2. **Ajouter JS Géolocalisation** (Priorité 1) ✅\n\n- [✅] `resources/views/presence/pointage.blade.php` → bootPresenceForms() complet (GPS high accuracy, fingerprint, auto-submit)

### 3. **Vérifier Site/Stage Link**

- [ ] Lister sites: `list_files app/Models/Site.php`, read, seed if missing
- [ ] Linker stages à site TFG (via admin)

### 4. **Mettre à jour Historique**

- [ ] `resources/views/presence/historique.blade.php` couleurs
- [ ] `resources/views/components/presence-history-table.blade.php`

### 5. **Tests & Démo**

- [ ] `php artisan serve`
- [ ] Tester pointage 7:30 (vert), 7:55 (jaune), 8:05 (rouge) + GPS TFG
- [ ] Vérif DB: `select * from attendance_events order by id desc limit 5;`

**Prochaines étapes immédiates: 1 → 2 → Tests**
