# TODO_PRESENCE.md - Système de Pointage Temps Réel (Stagiaires + Personnel)

## ✅ Plan Approuvé & État Initial

- [x] Système existant analysé : PresenceService, Attendance models, routes de base
- [x] Plan détaillé confirmé par l'utilisateur
- [x] Ce fichier créé pour tracker les progrès
- [x] **AdminPresenceService.php** créé (stats, listing, anomalies)

## 📋 Étapes de Mise en Œuvre (Priorité Haute → Basse)

### 1. **Modèles & DB (1/4)**

- [ ] **app/Models/User.php** : Ajouter relations `attendanceDays()`, `attendanceEvents()`
- [ ] **app/Models/AttendanceDay.php** : Ajouter relation `user()` nullable (pour personnel)
- [
- [ ] **Migration** : `add_user_id_nullable_to_attendance_days_events` si besoin

### 2. **Services (4/5)**

- [ ] **app/Services/PresenceService.php** : `registerPersonnelEvent($user, $payload, $type)`
- [x] **Nouveau** : `app/Services/AdminPresenceService.php` ✅ (listing, stats, anomalies, export)
- [x] **app/Services/RolePermissionPresetService.php** : Permissions `presence.admin.*` ✅
- [x] **AdminPresenceController.php** créé (index, stats, anomalies, resolve, export) ✅
- [x] **ResolveAnomalyRequest.php** créé ✅
- [x] **Routes admin/presence/** ajoutées ✅

### 3. **Contrôleurs & Requests (3/6)**

- [ ] **app/Http/Controllers/PresenceController.php** : Support personnel (sans stage)
- [ ] **Nouveau** : `app/Http/Controllers/AdminPresenceController.php` (index, anomalies, stats)
- [ ] **app/Http/Requests/Presence/** : `PersonnelAttendanceRequest.php`
- [ ] **Routes** : Ajouter `admin/presence/*`, `presence/personnel/*`

### 4. **Vues Frontend (5/8)**

- [ ] **resources/views/presence/index.blade.php** : Interface mobile GPS auto-detect, boutons checkin/out (existe partiellement)
- [x] **resources/views/admin/presence/index.blade.php** : Tableau supervision (filtres) ✅
- [ ] **admin/presence/anomalies.blade.php** : Liste anomalies à reviewer
- [ ] **admin/presence/stats.blade.php** : Stats mensuelles/heures
- [ ] **Composant** : `presence-geofence-map.blade.php` (Leaflet/Alpine)

### 5. **Temps Réel & Notifications (5/7)**

- [ ] JS : Auto-detect GPS, live statut, bouton pointage (Alpine.js)
- [ ] **NavigationComposer.php** : Compteurs \"Présences Jour/Anomalies\"
- [ ] **Dashboard** : Cards stats présence
- [ ] Notifications temps réel (Pusher pour anomalies)

### 6. **Admin & Permissions (6/6)**

- [ ] Permissions : `presence.view|checkin|checkout|admin.view|anomalies.review|stats.export`
- [ ] **navigation.blade.php** : Liens \"Pointage\", \"Supervision Présence\"
- [ ] Export Excel (MaataWebsite/Excel)

### 7. **Tests & Déploiement (7/7)**

- [ ] `php artisan migrate`
- [ ] Tester : Stagiaire pointage GPS, Personnel pointage, Anomalies
- [ ] `php artisan serve` → `/presence`, `/admin/presence`
- [ ] Permissions seed : `php artisan db:seed --class=RolePermissionSeeder`

## 🚀 Commandes Clés

```
php artisan make:controller AdminPresenceController
php artisan make:request Presence/PersonnelAttendanceRequest
php artisan make:service AdminPresenceService  # ou manuellement
composer require maatwebsite/excel  # pour exports
```

## 📊 Prochaines Étapes Immédiates

1. Créer **AdminPresenceController** + routes
2. Améliorer **presence/index.blade.php**
3. Tester pointage existant

**Progrès : 2/35 étapes (5%)** - Prêt pour implémentation !
