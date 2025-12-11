# Customer Portal Plugin System - Quick Start Guide

## Overview

The customer portal includes a powerful plugin system that allows you to easily customize and extend functionality without modifying core files. This guide will help you get started quickly.

## What Can You Do With Plugins?

- **Customize Themes**: Override views, add custom CSS/JS
- **Add New Features**: Create new pages, forms, and functionality
- **Modify Behavior**: Use hooks and filters to change how the portal works
- **Integrate Services**: Connect to third-party APIs and services
- **Extend Data Models**: Add new database tables and models
- **Custom Middleware**: Add authentication, logging, or request processing

## Quick Start: Create Your First Plugin

### 1. Generate a Plugin

The easiest way to create a plugin is using the built-in generator:

```bash
php artisan make:plugin "MyCustomTheme" --author="Your Name" --description="Custom theme for our portal"
```

This creates a complete plugin structure in `plugins/my-custom-theme/` with:
- Service provider
- Controllers
- Models
- Routes
- Views
- Migrations
- Configuration

### 2. Plugin Structure

Your new plugin will have this structure:

```
plugins/my-custom-theme/
├── plugin.json                 # Plugin configuration
├── README.md                   # Plugin documentation
├── src/
│   ├── MyCustomThemeServiceProvider.php
│   ├── Controllers/           # Your controllers
│   ├── Models/                # Your models
│   ├── Services/              # Your services
│   └── Middleware/            # Your middleware
├── routes/
│   ├── web.php                # Web routes
│   └── api.php                # API routes
├── resources/
│   ├── views/                 # Blade templates
│   ├── lang/                  # Translations
│   └── assets/                # CSS, JS, images
├── config/                     # Configuration files
└── database/
    └── migrations/            # Database migrations
```

### 3. Enable Your Plugin

Plugins are automatically discovered and loaded if enabled. To control this, edit `plugins/my-custom-theme/plugin.json`:

```json
{
    "name": "my-custom-theme",
    "version": "1.0.0",
    "description": "Custom theme for our portal",
    "author": "Your Name",
    "enabled": true,  // Set to false to disable
    "service_provider": "Plugins\\MyCustomTheme\\MyCustomThemeServiceProvider"
}
```

### 4. Reload Autoloader

After creating or modifying plugins, run:

```bash
composer dump-autoload
```

## Common Use Cases

### Use Case 1: Custom Theme/Styling

**Goal**: Override the portal's look and feel

1. Create a theme plugin:
```bash
php artisan make:plugin "CustomTheme" --author="Your Name"
```

2. Add your CSS in `plugins/custom-theme/resources/assets/css/custom.css`

3. Register the stylesheet in your service provider:
```php
protected function bootPlugin(): void
{
    parent::bootPlugin();
    
    // Add custom stylesheet
    if (File::exists($this->pluginPath . '/resources/assets/css/custom.css')) {
        $this->publishes([
            $this->pluginPath . '/resources/assets/css/custom.css' 
                => public_path('css/custom-theme.css'),
        ], 'custom-theme-assets');
    }
}
```

4. Publish assets:
```bash
php artisan vendor:publish --tag=custom-theme-assets
```

### Use Case 2: Add a Custom Page

**Goal**: Add a new page to the portal

1. Create the route in `routes/web.php`:
```php
use Plugins\MyPlugin\Controllers\MyPageController;

Route::get('/my-custom-page', [MyPageController::class, 'index'])
    ->name('my-custom-page');
```

2. Create the controller:
```php
namespace Plugins\MyPlugin\Controllers;

use App\Http\Controllers\Controller;

class MyPageController extends Controller
{
    public function index()
    {
        return view('my-plugin::my-page', [
            'title' => 'My Custom Page'
        ]);
    }
}
```

3. Create the view in `resources/views/my-page.blade.php`:
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $title }}</h1>
    <p>This is my custom page!</p>
</div>
@endsection
```

### Use Case 3: Modify Existing Functionality with Hooks

**Goal**: Run custom code when users log in

In your service provider's `registerHooks()` method:

```php
protected function registerHooks(): void
{
    // Hook into user login event
    app('plugin.hooks')->addAction('user.after_login', function ($user) {
        // Your custom logic here
        Log::info("User {$user->name} logged in at " . now());
        
        // Send welcome email, update last login, etc.
    });
}
```

### Use Case 4: Modify Content with Filters

**Goal**: Modify content before it's displayed

```php
protected function registerHooks(): void
{
    // Add a filter to modify rendered content
    app('plugin.hooks')->addFilter('content.render', function ($content) {
        // Add custom footer to all content
        return $content . '<div class="custom-footer">Powered by Custom Plugin</div>';
    }, 10); // Priority 10
}
```

### Use Case 5: Add Custom Settings

**Goal**: Add configuration options for your plugin

1. Define settings in `plugin.json`:
```json
{
    "config": {
        "feature_enabled": true,
        "api_key": "",
        "webhook_url": "https://example.com/webhook",
        "max_items": 100
    }
}
```

2. Access settings in your code:
```php
$config = $this->pluginConfig['config'];
$apiKey = $config['api_key'] ?? '';
$maxItems = $config['max_items'] ?? 10;
```

## Available Hooks and Filters

### Action Hooks

Hooks let you execute code at specific points in the application:

- `app.booted` - After the application boots
- `user.after_login` - After a user logs in
- `user.before_logout` - Before a user logs out
- `content.before_render` - Before content is rendered
- `content.after_save` - After content is saved
- `payment.processed` - After a payment is processed
- `ticket.created` - After a support ticket is created

**Usage:**
```php
app('plugin.hooks')->addAction('user.after_login', function ($user) {
    // Your code here
}, $priority = 10);
```

### Filters

Filters let you modify data before it's used:

- `content.render` - Modify content before rendering
- `menu.items` - Modify menu items
- `user.permissions` - Modify user permissions
- `payment.amount` - Modify payment amounts
- `email.content` - Modify email content before sending

**Usage:**
```php
app('plugin.hooks')->addFilter('menu.items', function ($items) {
    // Add your custom menu item
    $items[] = [
        'label' => 'My Plugin',
        'url' => '/my-plugin',
        'icon' => 'fa-puzzle-piece'
    ];
    return $items;
}, $priority = 10);
```

### Triggering Hooks in Core Code

If you're extending the core, you can trigger hooks:

```php
// Trigger an action
app('plugin.hooks')->doAction('custom.event', $data);

// Apply a filter
$modifiedContent = app('plugin.hooks')->applyFilters('custom.filter', $content, $additionalData);
```

## Plugin Management

### Enable/Disable Plugins Programmatically

```php
use App\Services\PluginManager;

$pluginManager = app(PluginManager::class);

// Enable a plugin
$pluginManager->enablePlugin('my-plugin');

// Disable a plugin
$pluginManager->disablePlugin('my-plugin');

// Get all plugins
$plugins = $pluginManager->getPlugins();

// Get only enabled plugins
$enabledPlugins = $pluginManager->getEnabledPlugins();
```

### Enable/Disable Via Configuration

Edit the plugin's `plugin.json`:
```json
{
    "enabled": false  // Set to true to enable
}
```

## Database Migrations

Plugins can include their own database migrations:

1. Create migration in `database/migrations/`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('my_plugin_data', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('my_plugin_data');
    }
};
```

2. Run migrations:
```bash
php artisan migrate
```

## Best Practices

### 1. Naming Conventions
- Use kebab-case for plugin names: `my-awesome-plugin`
- Use PascalCase for class names: `MyAwesomePlugin`
- Namespace: `Plugins\MyAwesomePlugin`

### 2. Configuration
- Store all configuration in `plugin.json`
- Don't hardcode values - use config options
- Document all configuration options in README.md

### 3. Dependencies
- Declare dependencies in `plugin.json`:
```json
{
    "dependencies": ["other-plugin"],
    "requires": {
        "php": ">=8.0",
        "laravel": ">=10.0"
    }
}
```

### 4. Security
- Validate all user input
- Use Laravel's built-in security features
- Don't expose sensitive data in responses
- Use middleware for authorization

### 5. Performance
- Cache expensive operations
- Use lazy loading for resources
- Minimize database queries
- Optimize asset loading

### 6. Documentation
- Include a detailed README.md
- Document all hooks and filters
- Provide usage examples
- List configuration options

## Debugging Plugins

### Enable Debug Mode

Set in `.env`:
```
PLUGIN_DEBUG=true
PLUGIN_SHOW_ERRORS=true
```

### Check Logs

Plugin errors are logged to `storage/logs/laravel.log`:
```bash
tail -f storage/logs/laravel.log
```

### Verify Plugin is Loaded

```php
$pluginManager = app(PluginManager::class);
$plugins = $pluginManager->getEnabledPlugins();
dd($plugins);
```

## Example Plugins

### Example 1: Simple Theme Plugin

See `plugins/example-plugin/` for a complete example demonstrating:
- Custom views
- Controllers
- Routes
- Hooks and filters
- Configuration

### Example 2: Markdown Editor

See `plugins/markdowneditor/` for a real-world example that adds markdown editing capabilities to the portal.

## Common Issues and Solutions

### Issue: Plugin Not Loading

**Solution:**
1. Check `plugin.json` is valid JSON
2. Ensure `enabled` is `true` in plugin.json
3. Run `composer dump-autoload`
4. Check logs for errors
5. Verify service provider class exists and is properly namespaced

### Issue: Views Not Found

**Solution:**
- Views are namespaced: use `'plugin-name::view-name'`
- Ensure views are in `resources/views/`
- Check view namespace in service provider

### Issue: Routes Not Working

**Solution:**
- Check routes are defined in `routes/web.php` or `routes/api.php`
- Verify controller namespace is correct
- Clear route cache: `php artisan route:clear`

### Issue: Assets Not Loading

**Solution:**
- Publish assets: `php artisan vendor:publish --tag=plugin-name-assets`
- Check public path is correct
- Verify file permissions

## Next Steps

1. **Explore Example Plugins**: Check out `plugins/example-plugin/` and `plugins/markdowneditor/`
2. **Read Full Documentation**: See `PLUGIN_SYSTEM_GUIDE.md` for advanced topics
3. **Create Your First Plugin**: Start with the generator command
4. **Join the Community**: Share your plugins and get help

## Support

For issues or questions about the plugin system:
- Check logs in `storage/logs/`
- Review existing plugins for examples
- Consult the full documentation in `PLUGIN_SYSTEM_GUIDE.md`

## Summary

The plugin system provides a clean, maintainable way to customize your customer portal without modifying core files. Key benefits:

- ✅ **Modular**: Easy to add/remove features
- ✅ **Upgrade Safe**: Plugins survive core updates
- ✅ **Shareable**: Package and distribute your plugins
- ✅ **Extensible**: Hooks and filters for deep customization
- ✅ **Laravel Native**: Uses standard Laravel features
- ✅ **Developer Friendly**: Clean structure and good documentation

Start building your first plugin today with `php artisan make:plugin`!
