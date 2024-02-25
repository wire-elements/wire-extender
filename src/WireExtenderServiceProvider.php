<?php

namespace WireElements\WireExtender;

use Illuminate\Support\ServiceProvider;

class WireExtenderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPackageRoutes();
        $this->registerAssets();
    }

    /*
     * Register package routes.
     */
    private function registerPackageRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }

    private function registerAssets(): void
    {
        $this->publishes([
            __DIR__.'/../js' => public_path('vendor/wire-elements'),
        ], 'wire-extender');
    }
}
