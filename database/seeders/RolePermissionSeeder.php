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
        // Liste des entités et actions
        $entities = ['jours', 'stagiaires', 'type_stages', 'badges', 'contacts'];
        $actions = ['view', 'create', 'edit', 'delete'];
        
        // Création des permissions
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "$entity.$action"]);
            }
        }

        Permission::create(['name' => 'access dashboard']);

        // Création des rôles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        
        // Attribution de toutes les permissions au rôle admin
        $adminRole->syncPermissions(Permission::all());
        
        // Attribution de permissions spécifiques au rôle user (exemple)
        $userRole->syncPermissions([
            'jours.view',
            'stagiaires.view',
            'type_stages.view',
            // ... autres permissions pour le rôle user
        ]);

        // Récupérer un user (par exemple id = 1)
        $user = User::find(1);
        
        if ($user) {
            $user->assignRole('admin'); // ou 'user'
        }
    }
}