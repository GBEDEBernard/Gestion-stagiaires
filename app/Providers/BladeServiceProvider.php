<?php

namespace App\Providers;

use App\Helpers\RouteHelper;
use App\Services\UrlEncrypter;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        // Macro pour utiliser dans les directives Blade
        // Utilisation: @route_show('badges', $badge)
        Blade::directive('route_show', function ($expression) {
            return "<?php echo RouteHelper::show($expression); ?>";
        });

        // Directive pour les liens d'edit
        // Utilisation: @route_edit('badges', $badge)
        Blade::directive('route_edit', function ($expression) {
            return "<?php echo RouteHelper::edit($expression); ?>";
        });

        // Directive pour les liens de destroy
        // Utilisation: @route_destroy('badges', $badge)
        Blade::directive('route_destroy', function ($expression) {
            return "<?php echo RouteHelper::destroy($expression); ?>";
        });

        // Directive pour les liens de update
        Blade::directive('route_update', function ($expression) {
            return "<?php echo RouteHelper::update($expression); ?>";
        });

        // Directives spécifiques pour Stages
        Blade::directive('route_stage_badge', function ($expression) {
            return "<?php echo RouteHelper::stageBadge($expression); ?>";
        });

        Blade::directive('route_stage_attestation', function ($expression) {
            return "<?php echo RouteHelper::stageAttestation($expression); ?>";
        });

        Blade::directive('route_stage_attestation_download', function ($expression) {
            return "<?php echo RouteHelper::stageAttestationDownload($expression); ?>";
        });

        Blade::directive('route_stage_attestation_print', function ($expression) {
            return "<?php echo RouteHelper::stageAttestationPrint($expression); ?>";
        });
    }
}
