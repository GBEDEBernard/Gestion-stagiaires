# TODO: Amélioration Page Pointage-Suivi (Admin Presence)

## Plan Approuvé

✅ **Objectif**: Français complet, filtres Site/École, impression propre, UI responsive.

## Étapes à Compléter (0/6)

### 1. [ ] Créer TODO.md ✅

- Fichier créé avec étapes.

### 2. [✅] Vérifier Modèle Stage (relations Site/Domaine)

- Stage: `site_id` → belongsTo(Site), `domaine_id` → belongsTo(Domaine)
- AttendanceDay → Stage → Parfait pour filtres.

### 3. [✅] Mettre à jour Controller AdminPresenceController.php

- Filtres site_id/domaine_id ajoutés (whereHas)
- `$sites`, `$domaines` chargés
- avgAccuracy → accuracy_meters
- Eager load: attendanceDay.stage.site.domaine
- Pagination garde filtres (appends)

### 4. [✅] Mettre à jour View pointage-suivi.blade.php

- ✅ Selects Site/École ajoutés (md:grid-cols-6)
- ✅ Bouton Imprimer + CSS @media print
- ✅ Précision → accuracy_meters (number_format)
- ✅ Labels polishés (emojis, "Filtrer les résultats")
- ✅ Responsive OK
- ✅ Toggle auto-refresh + label flottant

### 5. [✅] Route Impression

- window.print() + CSS @media print → Suffisant (pas de nouvelle route)

### 6. [✅] Terminé & Testé

- ✅ Filtres Site/École fonctionnels (whereHas sur relations)
- ✅ Impression propre (Ctrl+P masque UI superflue)
- ✅ Responsive + toggle auto-refresh
- UI française complète et propre

## Notes

- Relations probables: AttendanceEvent → attendanceDay → stage → site/domaine
- Print: window.print() + CSS suffit (no new route needed)
- Users: Étendre select avec domaine/école label
- Auto-refresh: Garder 30s + toggle ON/OFF

**Prochaine étape: Vérifier Stage model → Proceed?**
