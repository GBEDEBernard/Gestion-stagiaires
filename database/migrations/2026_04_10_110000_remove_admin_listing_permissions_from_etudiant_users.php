<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permissionsToRevoke = [
            'etudiants.view',
            'badges.view',
            'attestation.view',
        ];

        User::with('roles')
            ->whereHas('roles', fn($query) => $query->where('name', 'etudiant'))
            ->get()
            ->each(function (User $user) use ($permissionsToRevoke) {
                if ($user->hasAnyRole(['admin', 'superviseur'])) {
                    return;
                }

                // jb -> Les comptes etudiants ne doivent pas ouvrir les
                // ecrans admin de liste. On retire ici les reliquats
                // d'anciens presets trop larges.
                foreach ($permissionsToRevoke as $permissionName) {
                    if ($user->hasPermissionTo($permissionName)) {
                        $user->revokePermissionTo($permissionName);
                    }
                }
            });
    }

    public function down(): void
    {
        $permissionsToRestore = [
            'etudiants.view',
            'badges.view',
            'attestation.view',
        ];

        User::with('roles')
            ->whereHas('roles', fn($query) => $query->where('name', 'etudiant'))
            ->get()
            ->each(function (User $user) use ($permissionsToRestore) {
                if ($user->hasAnyRole(['admin', 'superviseur'])) {
                    return;
                }

                foreach ($permissionsToRestore as $permissionName) {
                    if (!$user->hasPermissionTo($permissionName)) {
                        $user->givePermissionTo($permissionName);
                    }
                }
            });
    }
};
