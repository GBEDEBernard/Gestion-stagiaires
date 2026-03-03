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
        // 🔹 Liste complète des entités de ton projet
        $entities = [
            'jour_stage',
            'etudiants',
            'stages',
            'type_stages',
            'badges',
            'services',
            'signataires',
            'users',
            'roles',
            'attestation',
            'dashboard',
            'corbeille',
            'qr_code',
        ];

        // 🔹 Actions CRUD + spécifiques
        $actions = ['view', 'create', 'edit', 'delete', 'restore', 'force-delete', 'download', 'print'];

        // 🔹 Création des permissions
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                // Certaines actions n'ont pas de sens pour certaines entités
                if (in_array($entity, ['dashboard', 'qr_code']) && !in_array($action, ['view'])) continue;
                if ($entity === 'attestation' && !in_array($action, ['view', 'create', 'download', 'print'])) continue;
                if ($entity === 'corbeille' && !in_array($action, ['view'])) continue;

                Permission::firstOrCreate(['name' => "$entity.$action"]);
            }
        }

        // 🔹 Création des rôles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // 🔹 Attribution de toutes les permissions au rôle admin
        $adminRole->syncPermissions(Permission::all());

        // 🔹 Permissions limitées pour le rôle user
        $userRole->syncPermissions([
            'etudiants.view',
            'attestation.view',
            'badges.view',
            'dashboard.view',
        ]);

        // 🔹 Assigner le rôle admin au premier utilisateur si existe
        $user = User::find(1);
        if ($user) {
            $user->assignRole('admin');
        }

        // 🔹 Nettoyer le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info("✅ Permissions et rôles créés avec succès !");
    }
}
