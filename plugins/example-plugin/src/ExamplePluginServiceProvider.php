<?php

namespace Plugins\ExamplePlugin;

use App\Providers\BasePluginServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class ExamplePluginServiceProvider extends BasePluginServiceProvider
{
    /**
     * Plugin name
     */
    protected string $pluginName = 'example-plugin';

    /**
     * Boot the plugin
     */
    protected function bootPlugin(): void
    {
        Log::info('ExamplePluginServiceProvider booted');

        parent::bootPlugin();

        // Register view composers
        $this->registerViewComposers();

        // Register custom hooks
        $this->registerHooks();
    }

    /**
     * Register plugin services
     */
    protected function registerPlugin(): void
    {
        // Register example service
        $this->app->singleton('example.service', function ($app) {
            return new Services\ExampleService();
        });

        // Register other plugin-specific services
        $this->registerExampleServices();
    }

    /**
     * Register view composers
     */
    protected function registerViewComposers(): void
    {
        View::composer('example-plugin::*', function ($view) {
            $view->with('exampleConfig', $this->getExampleConfig());
        });
    }

    /**
     * Register hooks
     */
    protected function registerHooks(): void
    {
        // Example of hooking into application events
        if (app()->bound('plugin.hooks')) {
            app('plugin.hooks')->addAction('user.after_login', function ($user) {
                Log::info("Example plugin: User {$user->id} logged in");
            });

            app('plugin.hooks')->addFilter('content.render', function ($content) {
                // Add a signature to rendered content
                return $content . "\n<!-- Rendered with Example Plugin -->";
            });
        }
    }

    /**
     * Register example-specific services
     */
    protected function registerExampleServices(): void
    {
        $this->app->bind('example.helper', function ($app) {
            return new Services\ExampleHelper();
        });
    }

    /**
     * Get example configuration
     */
    protected function getExampleConfig(): array
    {
        return [
            'feature_enabled' => $this->pluginConfig['config']['feature_enabled'] ?? true,
            'max_items' => $this->pluginConfig['config']['max_items'] ?? 10,
            'theme' => $this->pluginConfig['config']['theme'] ?? 'default',
        ];
    }
}
