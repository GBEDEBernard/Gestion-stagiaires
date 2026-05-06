# TODO - Refonte Graphe Présence Professionnel

## Étapes

- [x] 1. Analyser les fichiers et comprendre le problème
- [ ] 2. Corriger `AdminPresenceService::getUserDetailedStats()` - ne pas marquer les jours futurs comme absents
- [ ] 3. Refonte graphe `resources/views/presence/historique.blade.php` (chartGlobal + chartOverview)
- [ ] 4. Refonte graphe `resources/views/admin/presence/index.blade.php` (chartGlobal + chartOverview)
- [ ] 5. Refonte graphe `resources/views/attendance/tracking/index.blade.php` (presenceChart)
- [ ] 6. Tester et vérifier le rendu

## Détails techniques

### Problème 1 : Absences sur jours futurs
Dans `getUserDetailedStats()`, la boucle parcourt tous les jours de la période (startDate → endDate). Les jours futurs sans pointage sont marqués comme absents (absences[] = 1). Il faut ajouter une condition `$currentDate <= $today` pour ne marquer l'absence que sur les jours passés ou aujourd'hui.

### Problème 2 : Graphe non professionnel
- Les datasets binaires (0/1) doivent utiliser `stepped: 'before'` pour des pics nets
- L'axe Y gauche doit être borné à max 1.2 pour bien voir les pics
- Les minutes de retard doivent être sur un axe Y droit séparé
- Ajouter des points visibles quand valeur > 0
- Tooltips informatifs avec emoji

### Résultat attendu (Noah)
Jour 1 (présent à l'heure) : Présence=1, À l'heure=1, Retard=0, Absence=0
Jour 2 (retard 30min) : Présence=1, Retard(jours)=1, Retard(min)=30, À l'heure=0
Jour 3 (absent) : Absence=1, tout le reste=0

