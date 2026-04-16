# TODO.md - Implémentation Statistiques de Présence

## ✅ Plan approuvé par l'utilisateur

**Priorité : Admin global d'abord → Pages personnelles dynamiques → Composants réutilisables**

## 📋 Étapes à compléter (15 étapes)

### **PHASE 1 : Services & Backend (Étapes 1-5)**

- [✅] **1. Étendre AdminPresenceService.php** : Ajouter méthodes getGlobalStats(), getStatsByGroup(), getUserDetailedStats(), getTopLateUsers(), getAbsences().
- [✅] **2. Ajouter scopes AttendanceDay** : scopeGlobalStats(), scopeByGroup(), etc.
- [✅] **3. Créer AdminPresenceController::stats() et ::userStats()**.
- [✅] **4. Ajouter routes web.php** : admin.presence.stats, admin.presence.user-stats/{user}.
- [✅] **5. Installer Chart.js** : `npm i chart.js` + `npm run dev`.

### **PHASE 2 : Dashboard Admin Global (Étapes 6-9)**

- [✅] **6. Créer resources/views/admin/presence/index.blade.php** : Cards globales, graphs, tabs étudiants/employés.
- [ ] **7. Créer resources/views/admin/presence/\_stats-cards.blade.php** : Composant réutilisable.
- [ ] **8. Créer resources/views/admin/presence/user-stats.blade.php** : Vue détaillée par utilisateur.
- [ ] **9. Ajouter navigation admin** : Lien vers stats dans layouts/navigation.blade.php.

### **PHASE 3 : Pages Personnelles Dynamiques (Étapes 10-12)**

- [✅] **10. Améliorer presence/historique.blade.php** : Ajouter graphs Chart.js (bar présence/retards).
- [ ] **11. Créer components/presence-chart.blade.php** : Composant graphique réutilisable.
- [ ] **12. Ajouter JS dynamique** : Filtres AJAX + refresh graphs.

### **PHASE 4 : Finalisation & Tests (Étapes 13-15)**

- [ ] **13. Styles CSS** : Tailwind pour graphs + responsive.
- [ ] **14. Tests** : Vérifier stats avec seeders, responsive, dark mode.
- [ ] **15. Documentation** : Mettre à jour README + migration vers production.

**Commande pour marquer une étape :** `echo "✅ 1" >> TODO.md` (puis edit_file)

**Prochaine étape automatique :** Phase 1 Étape 1 - AdminPresenceService.php
