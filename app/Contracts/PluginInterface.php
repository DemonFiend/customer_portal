<?php

namespace App\Contracts;

interface PluginInterface
{
    /**
     * Get plugin name
     */
    public function getName(): string;

    /**
     * Get plugin version
     */
    public function getVersion(): string;

    /**
     * Get plugin description
     */
    public function getDescription(): string;

    /**
     * Boot the plugin
     */
    public function boot(): void;

    /**
     * Register plugin services
     */
    public function register(): void;

    /**
     * Check if plugin is enabled
     */
    public function isEnabled(): bool;

    /**
     * Get plugin dependencies
     */
    public function getDependencies(): array;

    /**
     * Get plugin configuration
     */
    public function getConfig(): array;
}
