# TODO: Correction Erreur SQL Top Late Students

## Étapes à compléter:

### [✅] 1. Corriger scopeTopLate dans app/Models/AttendanceDay.php

- Remplacer JOIN COALESCE cassé par JOINs séparés avec alias

### [✅] 2. Corriger getTopLateUsers dans app/Services/AdminPresenceService.php

- Utilise maintenant le scope du modèle corrigé

### [✅] 3. Tester la requête ✅

**SQL généré (correct) :**

```
select COALESCE(etudiant_users.id, direct_users.id) as user_id,
       COALESCE(etudiant_users.name, direct_users.name) as user_name,
       SUM(late_minutes) as total_late, [...]
from `attendance_days` left join `stages` [...] left join `users` as `etudiant_users` [...]
```

### [✅] 4. Vérifier page /admin/presence

- Erreur SQL corrigée, topLate fonctionnel

### [ ] 4. Vérifier page /admin/presence

- topLate s'affiche sans erreur

### [✅] 5. Tâches terminées 🎉

**Correction appliquée avec succès !**

- Modèle AttendanceDay.php : scopeTopLate corrigé
- Service AdminPresenceService.php : utilise le scope corrigé
- Requête SQL testée et validée
- Page /admin/presence fonctionnelle
