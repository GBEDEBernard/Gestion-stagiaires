<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionPresetService
{
    public function allowedRoleNames(): array
    {
        return ['admin', 'superviseur', 'employe', 'etudiant'];
    }

    public function orderedRoles()
    {
        $roles = Role::query()
            ->whereIn('name', $this->allowedRoleNames())
            ->get()
            ->keyBy('name');

        return collect($this->allowedRoleNames())
            ->map(fn(string $roleName) => $roles->get($roleName))
            ->filter()
            ->values();
    }

    public function rolePermissionMap(): array
    {
        $allPermissions = Permission::query()->pluck('name')->all();

        return [
            // jb -> Le role admin sert ici de preset maximal:
            // il precharge toutes les permissions, mais l'admin pourra
            // ensuite retirer celles qu'il ne veut pas garder sur un compte.
            'admin' => $allPermissions,
            'employe' => [
                'dashboard.view',
                'presence.view',
                'presence.checkin',
                'presence.checkout',
                'daily_reports.view',
                'daily_reports.create',
                'daily_reports.submit',
            ],
            'superviseur' => [
                'dashboard.view',
                'etudiants.view',
                'stages.view',
                'attestation.view',
                'attestation.approve',
                'sites.view',
                'presence.view',
                'presence.audit',
                'daily_reports.view',
                'daily_reports.review',
                'daily_reports.approve',
                'tasks.view',
                'tasks.create',
                'tasks.edit',
                'tasks.review',
                'attendance_anomalies.view',
                'attendance_anomalies.review',
                'presence_stats.view',

            ],
            'etudiant' => [
                'presence.view',
                'presence.checkin',
                'presence.checkout',
                'daily_reports.view',
                'daily_reports.create',
                'daily_reports.submit',
                'tasks.view',
            ],
        ];
    }

    public function permissionsForRoles(array $roleNames): array
    {
        $map = $this->rolePermissionMap();
        $normalizedRoleNames = $this->normalizeRoleNames($roleNames);
        $permissions = collect();

        foreach ($normalizedRoleNames as $roleName) {
            $permissions = $permissions->merge($map[$roleName] ?? []);
        }

        return $permissions->unique()->values()->all();
    }

    public function normalizeRoleNames(array $roleNames): array
    {
        return collect($roleNames)
            ->filter(fn($roleName) => in_array($roleName, $this->allowedRoleNames(), true))
            ->unique()
            ->values()
            ->all();
    }

    public function assignRolesAndPermissions(User $user, array $roleNames, array $permissionNames): void
    {
        $normalizedRoles = $this->normalizeRoleNames($roleNames);

        $user->syncRoles($normalizedRoles);
        $user->syncPermissions($this->sanitizePermissionNames($permissionNames));
    }

    public function ensureRoleDefaults(User $user, array $roleNames): void
    {
        $normalizedRoles = $this->normalizeRoleNames($roleNames);

        if (empty($normalizedRoles)) {
            return;
        }

        $existingRoles = $user->roles()->pluck('name')->all();
        $user->syncRoles($this->normalizeRoleNames(array_merge($existingRoles, $normalizedRoles)));

        // jb -> On ne force les presets qu'au moment ou le compte n'a pas
        // encore de permissions directes; ainsi les ajustements manuels
        // faits plus tard par l'admin restent intacts.
        if ($user->permissions()->count() === 0) {
            $user->syncPermissions($this->permissionsForRoles($user->roles()->pluck('name')->all()));
        }
    }

    public function sanitizePermissionNames(array $permissionNames): array
    {
        $allowedPermissions = Permission::query()->pluck('name')->all();

        return collect($permissionNames)
            ->filter(fn($permissionName) => in_array($permissionName, $allowedPermissions, true))
            ->unique()
            ->values()
            ->all();
    }
}
