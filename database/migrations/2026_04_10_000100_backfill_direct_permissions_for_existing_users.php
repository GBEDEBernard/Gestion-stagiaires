<?php

use App\Models\User;
use App\Services\RolePermissionPresetService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $presetService = app(RolePermissionPresetService::class);

        User::with(['roles', 'permissions'])->get()->each(function (User $user) use ($presetService) {
            if ($user->permissions->isNotEmpty()) {
                return;
            }

            $roleNames = $user->roles->pluck('name')->all();

            if (empty($roleNames)) {
                return;
            }

            // jb -> Cette migration protege l'existant:
            // les anciens comptes qui heritaient des permissions via role
            // recuperent maintenant leur copie directe avant qu'on passe
            // au nouveau modele "roles = presets".
            $user->syncPermissions($presetService->permissionsForRoles($roleNames));
        });
    }

    public function down(): void
    {
        // jb -> On ne retire pas automatiquement ces permissions directes,
        // car elles peuvent deja avoir ete ajustees manuellement ensuite.
    }
};
