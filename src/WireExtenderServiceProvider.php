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
        $this->registerConfig();
    }

    /*
     * Register package routes.
     */
    private function registerPackageRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }

    /*
     * Register package assets.
     */
    private function registerAssets(): void
    {
        $this->publishes([
            __DIR__.'/../js' => public_path('vendor/wire-elements'),
        ], 'wire-extender');
    }

    /*
     * Register package config.
     */
    private function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wire-extender.php', 'wire-extender');
    }
}
