<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PluginManager;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the plugin manager as a singleton
        $this->app->singleton(PluginManager::class, function ($app) {
            return new PluginManager();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $pluginManager = $this->app->make(PluginManager::class);

        // Discover all plugins
        $pluginManager->discoverPlugins();

        // Register enabled plugin service providers
        $this->registerPluginServiceProviders($pluginManager);
    }

    /**
     * Register plugin service providers
     */
    protected function registerPluginServiceProviders(PluginManager $pluginManager): void
    {
        $serviceProviders = $pluginManager->getServiceProviders();

        foreach ($serviceProviders as $providerClass) {
            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }
}
