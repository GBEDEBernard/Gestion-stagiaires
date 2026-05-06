# TODO: Geofence TFG Centre (20-30m Rayon Strict)

## Statut: 🚀 EN COURS

### Paramètres:

- **Coords centre**: `6.408685590450129, 2.3305279419712015`
- **Rayon**: 25m (20-30m)
- **Tolérance accuracy**: 30m (GPS mobile)
- **Site**: "TFG Centre" (créé auto)

### Étapes:

✅ **1. Update SiteGeofenceSeeder.php**

- Ajouter geofence TFG Centre 25m
- Remplacer coords anciennes TFG

✅ **2. Exécuter** `php artisan db:seed --class=SiteGeofenceSeeder`

⏳ **3. Vérifier DB**:

```
SELECT s.name, g.name, g.center_latitude, g.center_longitude, g.radius_meters, g.allowed_accuracy_meters
FROM sites s
JOIN site_geofences g ON s.id = g.site_id
WHERE s.name LIKE '%TFG%';
```

⏳ **4. Test pointage** employé TFG:

- POST coords exactes → doit accepter (distance <25m + accuracy <30m)

⏳ **5. Test rayon limite** (ex: +30m) → refuse "hors zone"

## Tests:

- [ ] Pointage coords exactes → APPROVED
- [ ] Accuracy 25m OK → APPROVED
- [ ] Accuracy 35m → REFUSÉ (gps_accuracy_low)
- [ ] +35m coords → REFUSÉ (outside_geofence)
