<?php

namespace Modules\Order\Infrastructure\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->mergeConfigFrom(__DIR__.'/../config.php', 'order');
        $this->loadRoutesFrom(__DIR__ . '/../../Ui/routes.php');
        $this->loadViewsFrom(__DIR__.'/../../Ui/Views', 'order');
        $this->app->register(EventServiceProvider::class);

        Blade::anonymousComponentPath(__DIR__.'/../../Ui/Views/components', 'order');
        Blade::componentNamespace('Modules\Order\ViewComponents', 'order');
    }
}
