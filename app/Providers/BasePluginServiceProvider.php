<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

abstract class BasePluginServiceProvider extends ServiceProvider
{
    /**
     * Plugin name
     */
    protected string $pluginName;

    /**
     * Plugin path
     */
    protected string $pluginPath;

    /**
     * Plugin configuration
     */
    protected array $pluginConfig = [];

    public function __construct($app)
    {
        parent::__construct($app);
        $this->pluginPath = $this->getPluginPath();
        $this->loadPluginConfig();
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        $this->bootPlugin();
    }

    /**
     * Register services
     */
    public function register(): void
    {
        $this->registerPlugin();
    }

    /**
     * Boot the plugin (override in child classes)
     */
    protected function bootPlugin(): void
    {
        // Load routes
        $this->loadPluginRoutes();

        // Load views
        $this->loadPluginViews();

        // Load migrations
        $this->loadPluginMigrations();

        // Load translations
        $this->loadPluginTranslations();

        // Load configuration
        $this->loadPluginConfiguration();

        // Register event listeners
        $this->registerPluginEventListeners();

        // Register middleware
        $this->registerPluginMiddleware();

        // Publish assets
        $this->publishPluginAssets();
    }

    /**
     * Register plugin services (override in child classes)
     */
    protected function registerPlugin(): void
    {
        // Register plugin services, bindings, etc.
    }

    /**
     * Load plugin routes
     */
    protected function loadPluginRoutes(): void
    {
        $webRoutesPath = $this->pluginPath . '/routes/web.php';
        $apiRoutesPath = $this->pluginPath . '/routes/api.php';

        if (File::exists($webRoutesPath)) {
            $this->loadRoutesFrom($webRoutesPath);
        }

        if (File::exists($apiRoutesPath)) {
            Route::prefix('api')
                ->middleware('api')
                ->group($apiRoutesPath);
        }
    }

    /**
     * Load plugin views
     */
    protected function loadPluginViews(): void
    {
        $viewsPath = $this->pluginPath . '/resources/views';

        if (File::exists($viewsPath)) {
            $this->loadViewsFrom($viewsPath, $this->pluginName);
        }
    }

    /**
     * Load plugin migrations
     */
    protected function loadPluginMigrations(): void
    {
        $migrationsPath = $this->pluginPath . '/database/migrations';

        if (File::exists($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    /**
     * Load plugin translations
     */
    protected function loadPluginTranslations(): void
    {
        $translationsPath = $this->pluginPath . '/resources/lang';

        if (File::exists($translationsPath)) {
            $this->loadTranslationsFrom($translationsPath, $this->pluginName);
        }
    }

    /**
     * Load plugin configuration
     */
    protected function loadPluginConfiguration(): void
    {
        $configPath = $this->pluginPath . '/config';

        if (File::exists($configPath)) {
            $configFiles = File::files($configPath);

            foreach ($configFiles as $file) {
                $configName = $this->pluginName . '.' . $file->getFilenameWithoutExtension();
                $this->mergeConfigFrom($file->getPathname(), $configName);
            }
        }
    }

    /**
     * Register plugin event listeners
     */
    protected function registerPluginEventListeners(): void
    {
        $listenersPath = $this->pluginPath . '/src/Listeners';

        if (File::exists($listenersPath)) {
            // Auto-discover and register event listeners
            // This is a simplified version - you can enhance this based on your needs
        }
    }

    /**
     * Register plugin middleware
     */
    protected function registerPluginMiddleware(): void
    {
        $middlewarePath = $this->pluginPath . '/src/Middleware';

        if (File::exists($middlewarePath)) {
            // Auto-discover and register middleware
            // This is a simplified version - you can enhance this based on your needs
        }
    }

    /**
     * Publish plugin assets
     */
    protected function publishPluginAssets(): void
    {
        $assetsPath = $this->pluginPath . '/resources/assets';
        $publicPath = $this->pluginPath . '/public';

        if (File::exists($assetsPath)) {
            $this->publishes([
                $assetsPath => public_path('plugins/' . $this->pluginName),
            ], $this->pluginName . '-assets');
        }

        if (File::exists($publicPath)) {
            $this->publishes([
                $publicPath => public_path('plugins/' . $this->pluginName),
            ], $this->pluginName . '-public');
        }
    }

    /**
     * Get plugin path
     */
    protected function getPluginPath(): string
    {
        if (!$this->pluginName) {
            throw new \Exception('Plugin name must be defined');
        }

        return base_path('plugins/' . $this->pluginName);
    }

    /**
     * Load plugin configuration from plugin.json
     */
    protected function loadPluginConfig(): void
    {
        $configPath = $this->pluginPath . '/plugin.json';

        if (File::exists($configPath)) {
            $this->pluginConfig = json_decode(File::get($configPath), true) ?? [];
        }
    }

    /**
     * Get plugin configuration
     */
    public function getPluginConfig(): array
    {
        return $this->pluginConfig;
    }

    /**
     * Get plugin name
     */
    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    /**
     * Check if plugin is enabled
     */
    public function isEnabled(): bool
    {
        return $this->pluginConfig['enabled'] ?? true;
    }

    /**
     * Register view composers for the plugin
     */
    protected function registerViewComposers(): void
    {
        // Override in child classes to register view composers
    }

    /**
     * Register custom artisan commands
     */
    protected function registerCommands(): void
    {
        // Override in child classes to register commands
    }

    /**
     * Hook into application events
     */
    protected function registerHooks(): void
    {
        // Override in child classes to register hooks/filters
    }
}
