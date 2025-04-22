<?php

namespace Modules\Shipment\Providers;

use Carbon\Laravel\ServiceProvider;

class ShipmentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations', 'shipment');
        $this->mergeConfigFrom(__DIR__.'/../config.php', 'shipment');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
    }
}
