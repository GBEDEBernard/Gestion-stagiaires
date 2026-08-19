<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Services\PermissionLabelService;

class PermissionLabelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PermissionLabelService::class);
    }

    public function boot(): void
    {
        Blade::directive('permissionLabel', function ($expression) {
            return "<?php echo app(\App\Services\PermissionLabelService::class)->getLabel({$expression}); ?>";
        });

        Blade::directive('permissionGroupLabel', function ($expression) {
            return "<?php echo app(\App\Services\PermissionLabelService::class)->getGroupLabel({$expression}); ?>";
        });
    }
}