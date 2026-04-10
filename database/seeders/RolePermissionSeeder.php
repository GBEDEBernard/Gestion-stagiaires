<?php

namespace Database\Seeders;

<<<<<<< HEAD
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
=======
use App\Models\User;
use App\Services\RolePermissionPresetService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
>>>>>>> e9635ab

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
<<<<<<< HEAD
        // 🔹 Liste complète des entités de ton projet
=======
        // jb -> La matrice reste lisible et extensible:
        // chaque module declare ses permissions metier dans un seul point.
>>>>>>> e9635ab
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
<<<<<<< HEAD
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
=======
            'sites',
            'presence',
            'daily_reports',
            'tasks',
            'attendance_anomalies',
            'presence_stats',
        ];

        $actions = [
            'view',
            'create',
            'edit',
            'delete',
            'restore',
            'force-delete',
            'download',
            'print',
            'checkin',
            'checkout',
            'submit',
            'review',
            'approve',
            'audit',
        ];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                if (in_array($entity, ['dashboard', 'qr_code']) && !in_array($action, ['view'])) {
                    continue;
                }

                if ($entity === 'attestation' && !in_array($action, ['view', 'create', 'download', 'print', 'approve'])) {
                    continue;
                }

                if ($entity === 'corbeille' && !in_array($action, ['view'])) {
                    continue;
                }

                if ($entity === 'sites' && !in_array($action, ['view', 'create', 'edit', 'delete'])) {
                    continue;
                }

                if ($entity === 'presence' && !in_array($action, ['view', 'checkin', 'checkout', 'audit'])) {
                    continue;
                }

                if ($entity === 'daily_reports' && !in_array($action, ['view', 'create', 'submit', 'review', 'approve'])) {
                    continue;
                }

                if ($entity === 'tasks' && !in_array($action, ['view', 'create', 'edit', 'delete', 'review'])) {
                    continue;
                }

                if ($entity === 'attendance_anomalies' && !in_array($action, ['view', 'review', 'audit'])) {
                    continue;
                }

                if ($entity === 'presence_stats' && !in_array($action, ['view'])) {
                    continue;
                }
>>>>>>> e9635ab

                Permission::firstOrCreate(['name' => "$entity.$action"]);
            }
        }

<<<<<<< HEAD
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
=======
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $supervisorRole = Role::firstOrCreate(['name' => 'superviseur']);
        $etudiantRole = Role::firstOrCreate(['name' => 'etudiant']);

        // jb -> Les roles servent maintenant de presets fonctionnels
        // dans l'ecran utilisateur unifie. Les permissions effectives
        // sont portees directement par chaque compte pour que l'admin
        // puisse vraiment en retirer ou en ajouter librement.
        $adminRole->syncPermissions([]);
        $supervisorRole->syncPermissions([]);
        $etudiantRole->syncPermissions([]);

        $user = User::find(1);
        if ($user) {
            $presetService = app(RolePermissionPresetService::class);
            $presetService->assignRolesAndPermissions(
                $user,
                ['admin'],
                $presetService->permissionsForRoles(['admin'])
            );
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Permissions et roles crees avec succes !');
>>>>>>> e9635ab
    }
}
