# TODO: Correction Pointage Employés (erreur etudiant_id NOT NULL)

## Statut: 🚀 EN COURS

### Étapes du Plan (Ordre Prioritaire):

✅ **1. Créer migration** `make_etudiant_id_nullable_in_attendance_days.php`

- Drop FK/unique sur etudiant_id
- Rendre nullable + recreate FK nullOnDelete
- Ajuster indexes

✅ **2. Modifier app/Models/AttendanceDay.php**

- Ajouter `$attributes = ['etudiant_id' => null, 'stage_id' => null];`
- Commentaire français OK

✅ **3. Patch app/Services/PresenceService.php**

- `$day->etudiant_id = null;` explicite dans syncEmployeeAttendanceDay()

⏳ **3. Patch app/Services/PresenceService.php**

- Explicit `$day->etudiant_id = null;` dans syncEmployeeAttendanceDay()

✅ **4. Exécuter migration** `php artisan migrate` → SUCCESS (8s)

✅ **5. Tester flow employé complet**

- Pointage → Validate → Confirm → Historique

⏳ **6. Vérifier AdminPresenceService (list + historique)**

⏳ **7. Migration contrainte unique** (attendance_date + COALESCE(etudiant_id,user_id))

⏳ **6. Vérifier AdminPresenceService (list + historique)**

⏳ **7. Ajouter migration contrainte unique** (attendance_date + COALESCE(etudiant_id,user_id))

⏳ **8. Optimisation** `php artisan optimize:clear`

## Tests à effectuer:

- [ ] Employé pointage arrivée/départ OK
- [ ] Redirect historique après confirm
- [ ] Admin voit présences employés
- [ ] Pas de duplicate AttendanceDay
- [ ] Étudiants toujours OK

**Objectif**: Employés pointent librement sans stage/etudiant_id → DB OK → historique comme étudiants.
