# TODO_OBSERVATION_RETARDS_FIX

- [ ]   1. Corriger `app/Models/AttendanceDay.php` : supprimer la duplication `lateAnomaly()` et conserver une seule relation fiable + accessor.
- [ ]   2. Corriger `app/Services/PresenceService.php` (employés) : inverser l’ordre `syncEmployeeAttendanceDay()` puis `recordAnomaly()` pour `retard_arrivee`, avec `$event->refresh()`.
- [ ]   3. Corriger `app/Services/PresenceService.php` (étudiants) : inverser l’ordre `syncAttendanceDay()` puis `recordAnomaly()` pour `retard_arrivee`, avec `$event->refresh()`.
- [ ]   4. Rattraper les anomalies orphelines : anomalies `retard_arrivee` avec `attendance_day_id` NULL -> rattachement par (user_id, attendance_date).
- [ ]   5. Tests manuels : valider un retard puis vérifier sur la vue `presence.historique`/`employee.presence.historique` que `late_observation` s’affiche.
