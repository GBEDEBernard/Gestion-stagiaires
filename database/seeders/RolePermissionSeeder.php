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
            'presence.admin',
            'employes',
            'personnels',
            // Ajout des permissions pour les demandes
            'permissions',
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
            'cancel'
        ];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                // Filtres spécifiques
                if (in_array($entity, ['dashboard', 'qr_code']) && !in_array($action, ['view'])) continue;
                if ($entity === 'attestation' && !in_array($action, ['view', 'create', 'download', 'print', 'approve'])) continue;
                if ($entity === 'corbeille' && !in_array($action, ['view'])) continue;
                if ($entity === 'sites' && !in_array($action, ['view', 'create', 'edit', 'delete'])) continue;
                if ($entity === 'presence' && !in_array($action, ['view', 'checkin', 'checkout', 'audit'])) continue;
                if ($entity === 'daily_reports' && !in_array($action, ['view', 'create', 'submit', 'review', 'approve'])) continue;
                if ($entity === 'tasks' && !in_array($action, ['view', 'create', 'edit', 'delete', 'review'])) continue;
                if ($entity === 'attendance_anomalies' && !in_array($action, ['view', 'review', 'audit'])) continue;
                if ($entity === 'presence_stats' && !in_array($action, ['view'])) continue;
                // Permissions : on garde view, create, cancel pour les utilisateurs, review/approve pour superviseurs
                if ($entity === 'permissions' && !in_array($action, ['view', 'create', 'cancel', 'review', 'approve'])) continue;

                Permission::firstOrCreate(['name' => "$entity.$action"]);
            }
        }

        // Permission spéciale pour les signataires d'attestation
        Permission::firstOrCreate(['name' => 'signer_attestation']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $employeRole = Role::firstOrCreate(['name' => 'employe']);
        $supervisorRole = Role::firstOrCreate(['name' => 'superviseur']);
        $etudiantRole = Role::firstOrCreate(['name' => 'etudiant']);

        $presetService = app(RolePermissionPresetService::class);

        // Attribuer les permissions via le service
        $adminRole->givePermissionTo($presetService->permissionsForRoles(['admin']));
        $supervisorRole->syncPermissions($presetService->permissionsForRoles(['superviseur']));
        $employeRole->syncPermissions($presetService->permissionsForRoles(['employe']));
        $etudiantRole->syncPermissions($presetService->permissionsForRoles(['etudiant']));

        // Permissions supplémentaires pour les employés et superviseurs
        $adminRole->givePermissionTo('employes.view');
        $adminRole->givePermissionTo('signer_attestation');
        $supervisorRole->givePermissionTo('employes.view');

        // Permissions explicites pour les demandes de permission
        $employeRole->givePermissionTo(['permissions.view', 'permissions.create', 'permissions.cancel']);
        $etudiantRole->givePermissionTo(['permissions.view', 'permissions.create', 'permissions.cancel']);
        $supervisorRole->givePermissionTo(['permissions.view', 'permissions.review', 'permissions.approve']);

        $user = User::find(1);
        if ($user) {
            $presetService->assignRolesAndPermissions(
                $user,
                ['admin'],
                $presetService->permissionsForRoles(['admin'])
            );
            $user->givePermissionTo('employes.view');
            $user->givePermissionTo('signer_attestation');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Permissions et rôles créés avec succès !');
    }
}
