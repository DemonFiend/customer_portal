<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plugin System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the plugin system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Plugin Directory
    |--------------------------------------------------------------------------
    |
    | The base directory where plugins are stored.
    |
    */
    'plugin_directory' => base_path('plugins'),

    /*
    |--------------------------------------------------------------------------
    | Auto Discovery
    |--------------------------------------------------------------------------
    |
    | Whether to automatically discover and load plugins on application boot.
    |
    */
    'auto_discovery' => true,

    /*
    |--------------------------------------------------------------------------
    | Plugin Cache
    |--------------------------------------------------------------------------
    |
    | Whether to cache plugin discovery results for better performance.
    |
    */
    'cache_enabled' => env('PLUGIN_CACHE_ENABLED', true),
    'cache_key' => 'plugins_discovery',
    'cache_ttl' => 3600, // 1 hour

    /*
    |--------------------------------------------------------------------------
    | Hook System
    |--------------------------------------------------------------------------
    |
    | Configuration for the plugin hook system.
    |
    */
    'hooks' => [
        'enabled' => true,
        'cache_hooks' => env('PLUGIN_HOOKS_CACHE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugin Security
    |--------------------------------------------------------------------------
    |
    | Security settings for plugin system.
    |
    */
    'security' => [
        'validate_signature' => false, // Enable plugin signature validation
        'allowed_authors' => [], // Whitelist of allowed plugin authors
        'require_https_downloads' => false, // Require HTTPS for plugin downloads
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugin Assets
    |--------------------------------------------------------------------------
    |
    | Configuration for plugin asset management.
    |
    */
    'assets' => [
        'auto_publish' => true, // Automatically publish plugin assets
        'versioning' => true, // Enable asset versioning
        'compression' => env('PLUGIN_ASSET_COMPRESSION', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    |
    | Settings useful during plugin development.
    |
    */
    'development' => [
        'debug' => env('PLUGIN_DEBUG', false),
        'hot_reload' => env('PLUGIN_HOT_RELOAD', false), // Reload plugins on file changes
        'show_errors' => env('PLUGIN_SHOW_ERRORS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugin Marketplace
    |--------------------------------------------------------------------------
    |
    | Configuration for plugin marketplace integration.
    |
    */
    'marketplace' => [
        'enabled' => false,
        'api_endpoint' => env('PLUGIN_MARKETPLACE_API', ''),
        'api_key' => env('PLUGIN_MARKETPLACE_KEY', ''),
    ],
];
