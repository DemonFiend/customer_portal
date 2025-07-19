# Customer Portal Plugin System - Implementation Guide

## Overview

I've created a comprehensive plugin system for your customer portal that allows you to modularly extend functionality without modifying core files. Here's what I've implemented:

## Core Components Created

### 1. Plugin Manager (`app/Services/PluginManager.php`)
- **Purpose**: Central hub for discovering, loading, and managing plugins
- **Features**:
  - Auto-discovery of plugins from the `plugins/` directory
  - Plugin enabling/disabling
  - Plugin installation/uninstallation
  - Validation of plugin configurations
  - Service provider registration

### 2. Plugin Hook Manager (`app/Services/PluginHookManager.php`)
- **Purpose**: Provides WordPress-style hooks and filters system
- **Features**:
  - Action hooks for triggering events
  - Filters for modifying data
  - Priority-based execution
  - Hook management (add/remove hooks)

### 3. Base Plugin Service Provider (`app/Providers/BasePluginServiceProvider.php`)
- **Purpose**: Abstract base class for all plugin service providers
- **Features**:
  - Automatic loading of routes, views, migrations, translations
  - Asset publishing
  - Configuration management
  - Standardized plugin structure

### 4. Plugin Service Provider (`app/Providers/PluginServiceProvider.php`)
- **Purpose**: Main service provider that bootstraps the plugin system
- **Features**:
  - Registers PluginManager as singleton
  - Auto-discovers and registers plugin service providers

### 5. Plugin Generator Command (`app/Console/Commands/MakePluginCommand.php`)
- **Purpose**: Artisan command to generate new plugins
- **Usage**: `php artisan make:plugin PluginName --author="Your Name" --description="Plugin description"`

### 6. Admin Interface (`app/Http/Controllers/Admin/PluginController.php`)
- **Purpose**: Web interface for managing plugins
- **Features**:
  - List all plugins
  - Enable/disable plugins
  - Install/uninstall plugins
  - View plugin details
  - API endpoints for plugin management

## Directory Structure

```
plugins/
├── README.md                          # Plugin system documentation
├── example-plugin/                    # Example plugin
│   ├── plugin.json                    # Plugin configuration
│   ├── src/
│   │   ├── ExamplePluginServiceProvider.php
│   │   └── Controllers/
│   │       └── ExampleController.php
│   ├── routes/
│   │   └── web.php
│   └── resources/
│       └── views/
│           ├── index.blade.php
│           └── create.blade.php
└── markdowneditor/                    # Updated existing plugin
    ├── plugin.json                    # Added configuration
    └── src/
        └── MarkdownEditorServiceProvider.php  # Updated to use new system
```

## Plugin Configuration (plugin.json)

Each plugin must have a `plugin.json` file:

```json
{
    "name": "plugin-name",
    "version": "1.0.0",
    "description": "Plugin description",
    "author": "Author Name",
    "enabled": true,
    "service_provider": "Plugins\\PluginName\\PluginServiceProvider",
    "dependencies": ["other-plugin"],
    "requires": {
        "php": ">=8.0",
        "laravel": ">=9.0"
    },
    "autoload": {
        "psr-4": {
            "Plugins\\PluginName\\": "src/"
        }
    },
    "config": {
        "custom_setting": "value"
    },
    "permissions": ["permission1", "permission2"],
    "hooks": ["hook1", "hook2"]
}
```

## How to Create a New Plugin

### Method 1: Using the Generator Command
```bash
php artisan make:plugin "MyAwesomePlugin" --author="Your Name" --description="An awesome plugin"
```

### Method 2: Manual Creation
1. Create plugin directory: `plugins/my-awesome-plugin/`
2. Create `plugin.json` with configuration
3. Create service provider in `src/MyAwesomePluginServiceProvider.php`
4. Add routes, views, controllers as needed

## Plugin Service Provider Example

```php
<?php

namespace Plugins\MyAwesomePlugin;

use App\Providers\BasePluginServiceProvider;

class MyAwesomePluginServiceProvider extends BasePluginServiceProvider
{
    protected string $pluginName = 'my-awesome-plugin';

    protected function bootPlugin(): void
    {
        parent::bootPlugin();
        
        // Custom boot logic
        $this->registerHooks();
    }

    protected function registerPlugin(): void
    {
        // Register services
        $this->app->singleton('my-awesome.service', function ($app) {
            return new Services\MyAwesomeService();
        });
    }

    protected function registerHooks(): void
    {
        // Add hooks
        app('plugin.hooks')->addAction('user.after_login', function ($user) {
            // Do something when user logs in
        });

        app('plugin.hooks')->addFilter('content.render', function ($content) {
            // Modify content before rendering
            return $content;
        });
    }
}
```

## Plugin Features Available

### 1. Routes
- Automatic loading from `routes/web.php` and `routes/api.php`
- Namespaced and prefixed automatically

### 2. Views
- Store in `resources/views/`
- Access via `plugin-name::view-name`
- Automatic view namespace registration

### 3. Controllers
- Create in `src/Controllers/`
- Follow standard Laravel controller structure

### 4. Models
- Create in `src/Models/`
- Standard Eloquent models

### 5. Middleware
- Create in `src/Middleware/`
- Register in service provider

### 6. Migrations
- Store in `database/migrations/`
- Automatically loaded and registered

### 7. Translations
- Store in `resources/lang/`
- Automatic namespace registration

### 8. Assets
- Store in `resources/assets/` or `public/`
- Auto-publishing to `public/plugins/plugin-name/`

### 9. Configuration
- Store config files in `config/`
- Automatically merged with app config

### 10. Hooks and Filters
- WordPress-style hook system
- Action hooks for events
- Filters for data modification

## Configuration Integration

The plugin system is integrated with your existing configuration:

1. **Updated `config/app.php`**:
   - Added `App\Providers\PluginServiceProvider::class`
   - Updated markdown editor service provider reference

2. **Created `config/plugins.php`**:
   - Plugin system configuration options
   - Security, caching, and development settings

## Next Steps to Complete Implementation

### 1. Install Dependencies
```bash
composer install  # Install Laravel dependencies
```

### 2. Register Plugin Service Provider
The `PluginServiceProvider` is already added to `config/app.php`.

### 3. Register Hook Manager
Add to `app/Providers/AppServiceProvider.php`:

```php
public function register()
{
    $this->app->singleton('plugin.hooks', function ($app) {
        return new \App\Services\PluginHookManager();
    });
}
```

### 4. Create Admin Views (Optional)
Create views for the plugin admin interface in `resources/views/admin/plugins/`.

### 5. Set Up Autoloading
You may need to update `composer.json` to include plugin autoloading:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Plugins\\": "plugins/"
        }
    }
}
```

Then run: `composer dump-autoload`

## Plugin Management

### Via Code
```php
$pluginManager = app(\App\Services\PluginManager::class);

// Enable a plugin
$pluginManager->enablePlugin('plugin-name');

// Disable a plugin
$pluginManager->disablePlugin('plugin-name');

// Get all plugins
$plugins = $pluginManager->getPlugins();

// Get enabled plugins only
$enabledPlugins = $pluginManager->getEnabledPlugins();
```

### Via Admin Interface
Access the plugin management interface at `/admin/plugins` (requires admin middleware).

### Via API
- `GET /api/plugins` - List all plugins
- `GET /api/plugins/{name}` - Get plugin info
- `POST /api/plugins/{name}/enable` - Enable plugin
- `POST /api/plugins/{name}/disable` - Disable plugin

## Security Considerations

1. **Plugin Validation**: All plugins are validated against required configuration fields
2. **Namespace Isolation**: Each plugin has its own namespace
3. **Permission System**: Plugin permissions can be defined in `plugin.json`
4. **Asset Isolation**: Plugin assets are published to separate directories

## Benefits of This System

1. **Modular**: Easy to add/remove functionality
2. **Extensible**: Hooks and filters allow deep customization
3. **Maintainable**: Plugins don't modify core files
4. **Shareable**: Plugins can be packaged and distributed
5. **Configurable**: Each plugin has its own configuration
6. **Laravel Native**: Uses Laravel's service container and features

## Examples in Action

I've created two example plugins:

1. **Updated Markdown Editor**: Shows how to upgrade existing plugin
2. **Example Plugin**: Demonstrates full plugin capabilities

You can use these as templates for creating your own plugins.

This plugin system gives you the flexibility to extend your customer portal in any way you need while keeping the core application clean and maintainable.
