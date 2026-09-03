<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * T-003 / Phase 2 — Les producteurs (étudiant + employé) gèrent leurs propres tâches.
     * Le superviseur passe en lecture + commentaire (plus de create/edit).
     */
    public function up(): void
    {
        foreach (['tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete', 'tasks.review'] as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        $producerPerms = ['tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete'];

        foreach (['etudiant', 'employe'] as $roleName) {
            if ($role = Role::where('name', $roleName)->first()) {
                $role->givePermissionTo($producerPerms);
            }
        }

        // Superviseur : lecture + commentaire uniquement.
        if ($sup = Role::where('name', 'superviseur')->first()) {
            $sup->givePermissionTo(['tasks.view', 'tasks.review']);
            $sup->revokePermissionTo(['tasks.create', 'tasks.edit', 'tasks.delete']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $producerPerms = ['tasks.create', 'tasks.edit', 'tasks.delete'];

        foreach (['etudiant', 'employe'] as $roleName) {
            if ($role = Role::where('name', $roleName)->first()) {
                $role->revokePermissionTo($producerPerms);
            }
        }

        if ($sup = Role::where('name', 'superviseur')->first()) {
            $sup->givePermissionTo(['tasks.create', 'tasks.edit']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
