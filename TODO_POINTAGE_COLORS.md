# Plan d'implémentation: Couleurs Pointage & Géolocalisation TFG

## Étapes à compléter:

### [✅] 1. Créer migration pour `arrival_status` dans `attendance_days`

### [✅] 2. Mettre à jour modèle `AttendanceDay.php`

### [✅] 3. Ajouter `computeArrivalStatus()` dans `PresenceService.php` & updater `syncAttendanceDay()`

### [ ] 4. Mettre à jour UI `pointage.blade.php` (couleurs/textes FR)

### [ ] 5. Créer/mettre à jour seeder `SiteGeofenceSeeder.php` pour TFG (lat:6.424759441415669, lng:2.317200378309422, radius=5m)

### [ ] 6. Mettre à jour `historique.blade.php` & `presence-history-table.blade.php`

### [ ] 7. Exécuter `php artisan migrate && php artisan db:seed --class=SiteGeofenceSeeder`

### [ ] 8. Tester pointages (7:30=vert, 7:55=jaune, 8:05=rouge) + géoloc

**Statut: En cours**  
**Dernière mise à jour:** {{ now()->format('d/m/Y H:i') }}
