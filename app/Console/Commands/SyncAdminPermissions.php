<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\RolePermissionPresetService;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncAdminPermissions extends Command
{
    protected $signature = 'permission:sync-admin {--force : Forcer même si déjà sync}';
    protected $description = 'Synchronise TOUTES les permissions pour tous les utilisateurs Admin (role admin)';

    public function handle(PermissionRegistrar $registrar, RolePermissionPresetService $presetService)
    {
        $this->info('🔄 Synchronisation des permissions Admin...');

        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $this->error('❌ Role "admin" non trouvé !');
            return 1;
        }

        $adminUsers = User::role('admin')->get();
        $count = $adminUsers->count();

        if ($count === 0) {
            $this->warn('⚠️ Aucun utilisateur Admin trouvé.');
            return 0;
        }

        $this->info("✅ {$count} utilisateurs Admin détectés.");

        $allPermissions = $presetService->permissionsForRoles(['admin']);
        $this->info("📋 {$allPermissions->count()} permissions à synchroniser par Admin.");

        foreach ($adminUsers as $index => $user) {
            $this->info('  [' . ($index + 1) . '/' . $count . '] Sync ' . $user->name . ' (ID: ' . $user->id . ')');

            if (!$this->option('force') && $user->permissions()->count() === $allPermissions->count()) {
                $this->line('    → Déjà synchronisé (skip)');
                continue;
            }

            $user->syncRoles(['admin']);
            $user->syncPermissions($allPermissions);

            $this->line('    → ✅ Permissions synchronisées');
        }

        // Vider cache Spatie
        $registrar->forgetCachedPermissions();

        $this->info("\n🎉 Synchronisation terminée ! Cache vidé.");
        $this->info('💡 Testez maintenant: http://127.0.0.1:8000/admin/presence/anomalies');
        $this->info('🔗 Notifications cliquent directement sans 403.');

        return 0;
    }
}
