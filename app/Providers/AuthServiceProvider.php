<?php

namespace App\Providers;

use App\Models\PermissionRequest;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        $hasPermission = static function (User $user, string $permissionName): bool {
            return Permission::query()->where('name', $permissionName)->exists()
                && $user->hasPermissionTo($permissionName);
        };

        $hasAnyPermissions = static function (User $user, array $permissionNames): bool {
            $existingPermissions = Permission::query()
                ->whereIn('name', $permissionNames)
                ->pluck('name')
                ->all();

            return !empty($existingPermissions) && $user->hasAnyPermission($existingPermissions);
        };

        Gate::define('accessAdminPresence', function (User $user) use ($hasPermission) {
            return $user->hasRole('admin')
                || $hasPermission($user, 'presence.admin.view');
        });

        Gate::define('reviewAdminAnomalies', function (User $user) use ($hasPermission) {
            return $user->hasRole('admin')
                || $hasPermission($user, 'presence.admin.anomalies.review');
        });

        Gate::define('reviewPermissionRequests', function (User $user) use ($hasAnyPermissions) {
            return $user->hasRole('admin')
                || $user->hasRole('superviseur')
                || $hasAnyPermissions($user, ['permission_requests.review', 'permission_requests.approve']);
        });

        Gate::define('viewPermissionRequest', function (User $user, PermissionRequest $permissionRequest) use ($hasAnyPermissions) {
            return $permissionRequest->user_id === $user->id
                || $permissionRequest->first_approver_id === $user->id
                || $user->hasRole('admin')
                || $hasAnyPermissions($user, ['permission_requests.review', 'permission_requests.approve']);
        });

        Gate::define('actOnPermissionRequest', function (User $user, PermissionRequest $permissionRequest) {
            if (in_array($permissionRequest->status, [
                    PermissionRequest::STATUS_APPROVED,
                    PermissionRequest::STATUS_REJECTED,
                    PermissionRequest::STATUS_SENT,
                ], true)) {
                return false;
            }

            if ($permissionRequest->first_approver_id === $user->id) {
                return true;
            }

            return $permissionRequest->first_approver_id === null
                && $user->hasRole('admin');
        });
    }
}
