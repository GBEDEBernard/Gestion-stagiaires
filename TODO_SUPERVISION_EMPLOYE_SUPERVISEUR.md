# TODO_SUPERVISION_EMPLOYE_SUPERVISEUR

## Objectif

Permettre à un employé d’avoir un superviseur attitré (rôle superviseur) et l’utiliser comme relation de supervision directe.

## Étapes

- [x]   1. Compléter la migration `2026_06_04_114136_add_supervisor_id_to_employes_table.php` : ajouter `supervisor_id` + FK vers `users` + index.

- [x]   2. Mettre à jour `app/Models/Employe.php` : relation `supervisor()` et éventuellement `supervisor_id` dans `$fillable`.
- [x]   3. Mettre à jour `app/Http/Controllers/Admin/UserController.php` :
    - charger la liste des superviseurs (users role superviseur) côté `create/edit`
    - ajouter la valeur `supervisor_id` pour un employé en edit
    - valider et persister `supervisor_id` en `store/update`

- [x]   4. Mettre à jour `resources/views/admin/users/partials/form.blade.php` : champ select “Superviseur attitré” visible uniquement pour `employe`.
- [x]   5. Mettre à jour l’affichage index `resources/views/admin/users/index.blade.php` : afficher le superviseur attitré quand le user est employé.

- [ ]   6. Mettre à jour la logique tâches : étendre `Task::scopeVisibleTo()` si nécessaire pour que le superviseur voie aussi les tâches des employés qui lui sont liés.
- [ ]   7. Migrer + vérifier : créer/éditer un employé et tester la visibilité des tâches.
