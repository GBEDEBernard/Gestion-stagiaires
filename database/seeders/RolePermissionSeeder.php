<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\RolePermissionPresetService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // jb -> La matrice reste lisible et extensible:
        // chaque module declare ses permissions metier dans un seul point.
        $entities = [
            'jour_stage',
            'etudiants',
            'stages',
            'type_stages',
            'badges',
            'services',
            'domaines',
            'signataires',
            'users',
            'roles',
            'attestation',
            'dashboard',
            'corbeille',
            'qr_code',
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

                Permission::firstOrCreate(['name' => "$entity.$action"]);
            }
        }

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
    }
}
