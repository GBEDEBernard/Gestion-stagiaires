# Plan: Ajout Gestion des Rôles dans le Sidebar

## Étape 1: Créer RoleController.php

- Chemin: `app/Http/Controllers/Admin/RoleController.php`
- Méthodes: index, create, store, edit, update, destroy

## Étape 2: Mettre à jour RolePermissionSeeder.php

- Ajouter l'entité 'roles' avec les permissions CRUD
- Chemin: `database/seeders/RolePermissionSeeder.php`

## Étape 3: Créer les vues pour les rôles

- `resources/views/admin/roles/index.blade.php` - Liste avec statistiques
- `resources/views/admin/roles/create.blade.php` - Formulaire de création
- `resources/views/admin/roles/edit.blade.php` - Formulaire d'édition

## Étape 4: Ajouter les routes

- Dans `routes/web.php`
- CRUD complet avec middleware de permissions

## Étape 5: Mettre à jour NavigationComposer

- Ajouter le comptage des rôles
- Chemin: `app/Http/ViewComposers/NavigationComposer.php`

## Étape 6: Ajouter l'onglet Rôles dans le sidebar

- Dans `resources/views/layouts/navigation.blade.php`
- Design moderne style gradient avec icône
