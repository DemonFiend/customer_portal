<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PluginManager
{
    protected Collection $plugins;
    protected array $loadedPlugins = [];
    protected string $pluginsPath;

    public function __construct()
    {
        $this->plugins = collect();
        $this->pluginsPath = base_path('plugins');
    }

    /**
     * Discover and load all plugins
     */
    public function discoverPlugins(): Collection
    {
        if (!File::exists($this->pluginsPath)) {
            File::makeDirectory($this->pluginsPath, 0755, true);
            return $this->plugins;
        }

        $pluginDirectories = File::directories($this->pluginsPath);

        foreach ($pluginDirectories as $directory) {
            $pluginName = basename($directory);
            $configPath = $directory . '/plugin.json';

            if (File::exists($configPath)) {
                try {
                    $config = json_decode(File::get($configPath), true);
                    if ($this->validatePluginConfig($config)) {
                        $plugin = $this->loadPlugin($pluginName, $directory, $config);
                        if ($plugin) {
                            $this->plugins->put($pluginName, $plugin);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to load plugin {$pluginName}: " . $e->getMessage());
                }
            }
        }

        return $this->plugins;
    }

    /**
     * Load a specific plugin
     */
    protected function loadPlugin(string $name, string $path, array $config): ?array
    {
        if (isset($this->loadedPlugins[$name])) {
            return $this->loadedPlugins[$name];
        }

        $plugin = [
            'name' => $name,
            'path' => $path,
            'config' => $config,
            'enabled' => $config['enabled'] ?? true,
            'service_provider' => null,
            'instance' => null
        ];

        // Load service provider if exists
        if (isset($config['service_provider'])) {
            $serviceProviderClass = $config['service_provider'];
            if (class_exists($serviceProviderClass)) {
                $plugin['service_provider'] = $serviceProviderClass;
            }
        }

        $this->loadedPlugins[$name] = $plugin;
        return $plugin;
    }

    /**
     * Validate plugin configuration
     */
    protected function validatePluginConfig(array $config): bool
    {
        $required = ['name', 'version', 'description'];

        foreach ($required as $field) {
            if (!isset($config[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all plugins
     */
    public function getPlugins(): Collection
    {
        return $this->plugins;
    }

    /**
     * Get enabled plugins only
     */
    public function getEnabledPlugins(): Collection
    {
        return $this->plugins->filter(function ($plugin) {
            return $plugin['enabled'];
        });
    }

    /**
     * Get plugin by name
     */
    public function getPlugin(string $name): ?array
    {
        return $this->plugins->get($name);
    }

    /**
     * Enable a plugin
     */
    public function enablePlugin(string $name): bool
    {
        if (!$this->plugins->has($name)) {
            return false;
        }

        $plugin = $this->plugins->get($name);
        $plugin['enabled'] = true;
        $this->plugins->put($name, $plugin);

        // Update plugin.json
        $this->updatePluginConfig($name, ['enabled' => true]);

        return true;
    }

    /**
     * Disable a plugin
     */
    public function disablePlugin(string $name): bool
    {
        if (!$this->plugins->has($name)) {
            return false;
        }

        $plugin = $this->plugins->get($name);
        $plugin['enabled'] = false;
        $this->plugins->put($name, $plugin);

        // Update plugin.json
        $this->updatePluginConfig($name, ['enabled' => false]);

        return true;
    }

    /**
     * Update plugin configuration
     */
    protected function updatePluginConfig(string $name, array $updates): void
    {
        $plugin = $this->plugins->get($name);
        if (!$plugin) {
            return;
        }

        $configPath = $plugin['path'] . '/plugin.json';
        $config = $plugin['config'];

        foreach ($updates as $key => $value) {
            $config[$key] = $value;
        }

        File::put($configPath, json_encode($config, JSON_PRETTY_PRINT));

        // Update in memory
        $plugin['config'] = $config;
        $this->plugins->put($name, $plugin);
    }

    /**
     * Install a plugin from a directory
     */
    public function installPlugin(string $sourcePath): bool
    {
        $pluginConfigPath = $sourcePath . '/plugin.json';

        if (!File::exists($pluginConfigPath)) {
            throw new \Exception('Plugin configuration file not found');
        }

        $config = json_decode(File::get($pluginConfigPath), true);
        if (!$this->validatePluginConfig($config)) {
            throw new \Exception('Invalid plugin configuration');
        }

        $pluginName = $config['name'];
        $targetPath = $this->pluginsPath . '/' . $pluginName;

        if (File::exists($targetPath)) {
            throw new \Exception('Plugin already exists');
        }

        // Copy plugin files
        File::copyDirectory($sourcePath, $targetPath);

        // Load the plugin
        $this->loadPlugin($pluginName, $targetPath, $config);

        return true;
    }

    /**
     * Uninstall a plugin
     */
    public function uninstallPlugin(string $name): bool
    {
        $plugin = $this->plugins->get($name);
        if (!$plugin) {
            return false;
        }

        // Remove plugin directory
        File::deleteDirectory($plugin['path']);

        // Remove from loaded plugins
        $this->plugins->forget($name);
        unset($this->loadedPlugins[$name]);

        return true;
    }

    /**
     * Get plugin service providers for registration
     */
    public function getServiceProviders(): array
    {
        $providers = [];

        foreach ($this->getEnabledPlugins() as $plugin) {
            if ($plugin['service_provider']) {
                $providers[] = $plugin['service_provider'];
            }
        }

        return $providers;
    }
}
