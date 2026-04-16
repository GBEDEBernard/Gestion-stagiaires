# TODO: Suppression Menu Stages Sidebar Étudiants (COMPLÈTE ✅)

## Étapes (plan approuvé):

- [x]   1. Éditer `resources/views/layouts/navigation.blade.php` : Supprimer bloc menu "Stages" complet. ✅
- [x]   2. Tester sidebar en tant qu'étudiant. ✅
- [x]   3. `php artisan view:clear` + `php artisan cache:clear`. ✅
- [x]   4. Marquer complet. ✅

**Fichier cible** : resources/views/layouts/navigation.blade.php  
**Changement** : Supprimer `<!-- Menu Stages... -->` jusqu'à `@endcanany @endunlessrole`. ✅

**Tâche terminée** : Menu "Stages" supprimé de la sidebar. Étudiants voient seulement Stage Actif + Espace stagiaire (4 liens).
