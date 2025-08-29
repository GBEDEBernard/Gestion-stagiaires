<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 🔹 Liste de toutes les entités de ton projet
        $entities = [
            'jour_stage',
            'etudiants',
            'stages',
            'type_stages',
            'badges',
            'services'
        ];

        // 🔹 Actions possibles sur chaque entité
        $actions = ['view', 'create', 'edit', 'delete'];

        // 🔹 Création de toutes les permissions
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "$entity.$action"]);
            }
        }

        // 🔹 Permission spécifique pour le dashboard
        Permission::firstOrCreate(['name' => 'access.dashboard']);

        // 🔹 Création des rôles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // 🔹 Attribution de toutes les permissions au rôle admin
        $adminRole->syncPermissions(Permission::all());

        // 🔹 Exemple de permissions pour le rôle user
        $userRole->syncPermissions([
            'jour_stage.view',
            'etudiants.view',
            'stages.view',
            'type_stages.view',
            'badges.view',
        ]);

        // 🔹 Assigner le rôle admin au premier utilisateur
        $user = User::find(1);
        if ($user) {
            $user->assignRole('admin');
        }

        // 🔹 Nettoyer le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
