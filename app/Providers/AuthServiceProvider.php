<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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

        // Gate pour accès Admin Présence/Anomalies
        Gate::define('accessAdminPresence', function (User $user) {
            return $user->hasRole('admin') ||
                $user->hasPermissionTo('presence.admin.view');
        });

        // Gate pour review anomalies (déjà protégé middleware mais cohérent)
        Gate::define('reviewAdminAnomalies', function (User $user) {
            return $user->hasRole('admin') ||
                $user->hasPermissionTo('presence.admin.anomalies.review');
        });
    }
}
