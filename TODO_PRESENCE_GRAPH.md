# TODO: Graphique Évolution Présence Unique

## Étapes complétées ✅

- [x] Créer fichier TODO
- [x] Modifier index.blade.php: Remplacer 3 graphiques séparés par 1 graphique multi-courbes
- [x] Modifier AdminPresenceService.php: 'late' → total_late_minutes par jour
- [x] Ajouter canvas #chartEvolution avec 3 datasets (Présence, À l'heure, Retards minutes)
- [x] CSS responsive full-width + legend + courbes smooth (tension 0.45)

## À tester

- [ ] Vérifier tous les periods (aujourd'hui/semaine/mois/année)
- [ ] Vérifier courbes smooths, tooltips, mobile responsive
- [ ] Confirmer retards en minutes (pas count jours)
- [ ] `php artisan serve` → /admin/presence

## Complété par BLACKBOXAI
