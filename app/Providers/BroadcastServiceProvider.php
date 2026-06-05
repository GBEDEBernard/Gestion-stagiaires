<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->routes();
    }

    /**
     * Register the broadcasting routes.
     */
    private function routes(): void
    {
        \Illuminate\Broadcasting\BroadcastManager::routes();

        require base_path('routes/channels.php');
    }
}
