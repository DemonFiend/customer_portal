# Plugin System Documentation

## Overview

This customer portal includes a comprehensive plugin system that allows you to extend functionality without modifying the core application. Plugins can add new features, modify existing behavior, add routes, views, services, and more.

## Plugin Structure

Each plugin should follow this directory structure:

```
plugins/
├── your-plugin-name/
│   ├── plugin.json              # Plugin configuration
│   ├── src/                     # PHP classes
│   │   ├── YourPluginServiceProvider.php
│   │   ├── Controllers/         # Controllers
│   │   ├── Models/             # Models
│   │   ├── Services/           # Services
│   │   ├── Middleware/         # Middleware
│   │   └── Listeners/          # Event listeners
│   ├── routes/                 # Routes
│   │   ├── web.php
│   │   └── api.php
│   ├── resources/              # Resources
│   │   ├── views/              # Blade templates
│   │   ├── lang/               # Translations
│   │   └── assets/             # CSS, JS, images
│   ├── public/                 # Public assets
│   ├── config/                 # Configuration files
│   ├── database/               # Database files
│   │   └── migrations/         # Migrations
│   └── tests/                  # Tests
```

## Plugin Configuration (plugin.json)

Every plugin must have a `plugin.json` file in its root directory:

```json
{
    "name": "your-plugin-name",
    "version": "1.0.0",
    "description": "Description of your plugin",
    "author": "Your Name",
    "enabled": true,
    "service_provider": "Plugins\\YourPluginName\\YourPluginServiceProvider",
    "dependencies": ["another-plugin"],
    "requires": {
        "php": ">=8.0",
        "laravel": ">=9.0"
    },
    "autoload": {
        "psr-4": {
            "Plugins\\YourPluginName\\": "src/"
        }
    },
    "config": {
        "setting1": "value1",
        "setting2": true
    },
    "permissions": [
        "view_your_feature",
        "edit_your_feature"
    ],
    "hooks": [
        "content.before_render",
        "user.after_login"
    ]
}
```

## Creating a Plugin Service Provider

Your plugin should extend `BasePluginServiceProvider`:

```php
<?php

namespace Plugins\YourPluginName;

use App\Providers\BasePluginServiceProvider;

class YourPluginServiceProvider extends BasePluginServiceProvider
{
    protected string $pluginName = 'your-plugin-name';

    protected function bootPlugin(): void
    {
        parent::bootPlugin();
        
        // Custom boot logic
        $this->registerCustomFeatures();
    }

    protected function registerPlugin(): void
    {
        // Register services
        $this->app->singleton('your.service', function ($app) {
            return new YourService();
        });
    }

    private function registerCustomFeatures(): void
    {
        // Add custom logic here
    }
}
```

## Plugin Features

### 1. Routes

Create routes in `routes/web.php` or `routes/api.php`:

```php
// routes/web.php
use Illuminate\Support\Facades\Route;
use Plugins\YourPlugin\Controllers\YourController;

Route::prefix('your-plugin')->group(function () {
    Route::get('/', [YourController::class, 'index']);
    Route::post('/save', [YourController::class, 'save']);
});
```

### 2. Views

Create Blade templates in `resources/views/`:

```blade
{{-- resources/views/your-view.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>Your Plugin Content</h1>
@endsection
```

### 3. Controllers

Create controllers in `src/Controllers/`:

```php
<?php

namespace Plugins\YourPlugin\Controllers;

use App\Http\Controllers\Controller;

class YourController extends Controller
{
    public function index()
    {
        return view('your-plugin::your-view');
    }
}
```

### 4. Models

Create models in `src/Models/`:

```php
<?php

namespace Plugins\YourPlugin\Models;

use Illuminate\Database\Eloquent\Model;

class YourModel extends Model
{
    protected $fillable = ['name', 'description'];
}
```

### 5. Services

Create services in `src/Services/`:

```php
<?php

namespace Plugins\YourPlugin\Services;

class YourService
{
    public function doSomething()
    {
        // Service logic
    }
}
```

### 6. Middleware

Create middleware in `src/Middleware/`:

```php
<?php

namespace Plugins\YourPlugin\Middleware;

use Closure;

class YourMiddleware
{
    public function handle($request, Closure $next)
    {
        // Middleware logic
        return $next($request);
    }
}
```

### 7. Migrations

Create migrations in `database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYourTable extends Migration
{
    public function up()
    {
        Schema::create('your_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('your_table');
    }
}
```

## Hooks and Filters

The plugin system includes a hook system for extending functionality:

### Adding Hooks

```php
// In your service provider
app('plugin.hooks')->addAction('user.after_login', function ($user) {
    // Do something when user logs in
});
```

### Adding Filters

```php
// In your service provider
app('plugin.hooks')->addFilter('content.render', function ($content) {
    // Modify content before rendering
    return $content;
});
```

### Common Hooks

- `app.booted` - After application boots
- `user.after_login` - After user logs in
- `user.before_logout` - Before user logs out
- `content.before_render` - Before content is rendered
- `content.after_save` - After content is saved

### Common Filters

- `content.render` - Filter content before rendering
- `menu.items` - Filter menu items
- `user.permissions` - Filter user permissions

## Plugin Management

### Enabling/Disabling Plugins

```php
$pluginManager = app(PluginManager::class);

// Enable a plugin
$pluginManager->enablePlugin('plugin-name');

// Disable a plugin
$pluginManager->disablePlugin('plugin-name');
```

### Installing Plugins

```php
$pluginManager = app(PluginManager::class);
$pluginManager->installPlugin('/path/to/plugin');
```

### Uninstalling Plugins

```php
$pluginManager = app(PluginManager::class);
$pluginManager->uninstallPlugin('plugin-name');
```

## Best Practices

1. **Namespace**: Use a clear namespace for your plugin (e.g., `Plugins\YourPluginName`)
2. **Configuration**: Store configuration in the `plugin.json` file
3. **Dependencies**: Declare dependencies in the `plugin.json` file
4. **Versioning**: Use semantic versioning for your plugin
5. **Documentation**: Include README.md with installation and usage instructions
6. **Testing**: Include tests in the `tests/` directory
7. **Assets**: Store CSS/JS in `resources/assets/` and use Laravel Mix for compilation
8. **Translations**: Support multiple languages using Laravel's localization features

## Example Plugin

See the `markdowneditor` plugin for a complete example of how to structure and implement a plugin.

## Troubleshooting

- Check Laravel logs in `storage/logs/` for plugin-related errors
- Ensure your plugin's `plugin.json` is valid JSON
- Verify that your service provider class exists and is properly namespaced
- Make sure all required dependencies are installed
