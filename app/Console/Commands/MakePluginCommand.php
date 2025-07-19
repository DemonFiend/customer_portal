<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePluginCommand extends Command
{
    protected $signature = 'make:plugin {name : The name of the plugin}
                           {--author= : The author of the plugin}
                           {--description= : The description of the plugin}
                           {--version=1.0.0 : The version of the plugin}';

    protected $description = 'Create a new plugin';

    public function handle()
    {
        $name = $this->argument('name');
        $author = $this->option('author') ?? 'Unknown';
        $description = $this->option('description') ?? 'A new plugin for the customer portal';
        $version = $this->option('version');

        $pluginName = Str::kebab($name);
        $className = Str::studly($name);
        $namespace = "Plugins\\{$className}";

        $pluginPath = base_path("plugins/{$pluginName}");

        if (File::exists($pluginPath)) {
            $this->error("Plugin '{$pluginName}' already exists!");
            return 1;
        }

        $this->info("Creating plugin: {$pluginName}");

        // Create plugin directory structure
        $this->createDirectoryStructure($pluginPath);

        // Create plugin.json
        $this->createPluginConfig($pluginPath, $pluginName, $className, $author, $description, $version, $namespace);

        // Create service provider
        $this->createServiceProvider($pluginPath, $className, $namespace, $pluginName);

        // Create controller
        $this->createController($pluginPath, $className, $namespace);

        // Create model
        $this->createModel($pluginPath, $className, $namespace);

        // Create routes
        $this->createRoutes($pluginPath, $className, $namespace);

        // Create views
        $this->createViews($pluginPath, $pluginName);

        // Create migration
        $this->createMigration($pluginPath, $pluginName);

        // Create README
        $this->createReadme($pluginPath, $pluginName, $description);

        $this->info("Plugin '{$pluginName}' created successfully!");
        $this->info("Don't forget to register the service provider in config/app.php:");
        $this->line("'{$namespace}\\{$className}ServiceProvider::class,'");

        return 0;
    }

    protected function createDirectoryStructure(string $pluginPath): void
    {
        $directories = [
            'src/Controllers',
            'src/Models',
            'src/Services',
            'src/Middleware',
            'src/Listeners',
            'routes',
            'resources/views',
            'resources/lang/en',
            'resources/assets/css',
            'resources/assets/js',
            'public',
            'config',
            'database/migrations',
            'tests/Feature',
            'tests/Unit'
        ];

        foreach ($directories as $directory) {
            File::makeDirectory("{$pluginPath}/{$directory}", 0755, true);
        }
    }

    protected function createPluginConfig(string $pluginPath, string $pluginName, string $className, string $author, string $description, string $version, string $namespace): void
    {
        $config = [
            'name' => $pluginName,
            'version' => $version,
            'description' => $description,
            'author' => $author,
            'enabled' => true,
            'service_provider' => "{$namespace}\\{$className}ServiceProvider",
            'dependencies' => [],
            'requires' => [
                'php' => '>=8.0',
                'laravel' => '>=9.0'
            ],
            'autoload' => [
                'psr-4' => [
                    "{$namespace}\\" => 'src/'
                ]
            ],
            'config' => [
                'enabled' => true
            ],
            'permissions' => [],
            'hooks' => []
        ];

        File::put("{$pluginPath}/plugin.json", json_encode($config, JSON_PRETTY_PRINT));
    }

    protected function createServiceProvider(string $pluginPath, string $className, string $namespace, string $pluginName): void
    {
        $content = "<?php

namespace {$namespace};

use App\Providers\BasePluginServiceProvider;

class {$className}ServiceProvider extends BasePluginServiceProvider
{
    /**
     * Plugin name
     */
    protected string \$pluginName = '{$pluginName}';

    /**
     * Boot the plugin
     */
    protected function bootPlugin(): void
    {
        parent::bootPlugin();

        // Register custom functionality here
    }

    /**
     * Register plugin services
     */
    protected function registerPlugin(): void
    {
        // Register services, bindings, etc.
        \$this->app->singleton('{$pluginName}.service', function (\$app) {
            return new Services\\{$className}Service();
        });
    }
}
";

        File::put("{$pluginPath}/src/{$className}ServiceProvider.php", $content);
    }

    protected function createController(string $pluginPath, string $className, string $namespace): void
    {
        $content = "<?php

namespace {$namespace}\\Controllers;

use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;

class {$className}Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('{$this->argument('name')}::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('{$this->argument('name')}::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request \$request)
    {
        // Store logic here
        return redirect()->route('{$this->argument('name')}.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(\$id)
    {
        return view('{$this->argument('name')}::show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\$id)
    {
        return view('{$this->argument('name')}::edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request \$request, \$id)
    {
        // Update logic here
        return redirect()->route('{$this->argument('name')}.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\$id)
    {
        // Delete logic here
        return redirect()->route('{$this->argument('name')}.index');
    }
}
";

        File::put("{$pluginPath}/src/Controllers/{$className}Controller.php", $content);
    }

    protected function createModel(string $pluginPath, string $className, string $namespace): void
    {
        $tableName = Str::snake(Str::plural($className));
        $content = "<?php

namespace {$namespace}\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;

class {$className} extends Model
{
    use HasFactory;

    protected \$table = '{$tableName}';

    protected \$fillable = [
        'name',
        'description',
    ];

    protected \$casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
";

        File::put("{$pluginPath}/src/Models/{$className}.php", $content);
    }

    protected function createRoutes(string $pluginPath, string $className, string $namespace): void
    {
        $routeName = Str::kebab($this->argument('name'));
        $controllerClass = "{$namespace}\\Controllers\\{$className}Controller";

        $webRoutes = "<?php

use Illuminate\\Support\\Facades\\Route;
use {$controllerClass};

Route::prefix('{$routeName}')->name('{$routeName}.')->group(function () {
    Route::get('/', [{$className}Controller::class, 'index'])->name('index');
    Route::get('/create', [{$className}Controller::class, 'create'])->name('create');
    Route::post('/', [{$className}Controller::class, 'store'])->name('store');
    Route::get('/{{id}}', [{$className}Controller::class, 'show'])->name('show');
    Route::get('/{{id}}/edit', [{$className}Controller::class, 'edit'])->name('edit');
    Route::put('/{{id}}', [{$className}Controller::class, 'update'])->name('update');
    Route::delete('/{{id}}', [{$className}Controller::class, 'destroy'])->name('destroy');
});
";

        File::put("{$pluginPath}/routes/web.php", $webRoutes);

        $apiRoutes = "<?php

use Illuminate\\Support\\Facades\\Route;
use {$controllerClass};

Route::apiResource('{$routeName}', {$className}Controller::class);
";

        File::put("{$pluginPath}/routes/api.php", $apiRoutes);
    }

    protected function createViews(string $pluginPath, string $pluginName): void
    {
        $views = [
            'index' => "@extends('layouts.app')

@section('content')
<div class=\"container\">
    <h1>{$this->argument('name')} - Index</h1>
    <p>Welcome to the {$this->argument('name')} plugin!</p>
</div>
@endsection",

            'create' => "@extends('layouts.app')

@section('content')
<div class=\"container\">
    <h1>Create {$this->argument('name')}</h1>
    <!-- Add your create form here -->
</div>
@endsection",

            'show' => "@extends('layouts.app')

@section('content')
<div class=\"container\">
    <h1>Show {$this->argument('name')}</h1>
    <p>ID: {{ \$id }}</p>
</div>
@endsection",

            'edit' => "@extends('layouts.app')

@section('content')
<div class=\"container\">
    <h1>Edit {$this->argument('name')}</h1>
    <p>ID: {{ \$id }}</p>
    <!-- Add your edit form here -->
</div>
@endsection"
        ];

        foreach ($views as $viewName => $content) {
            File::put("{$pluginPath}/resources/views/{$viewName}.blade.php", $content);
        }
    }

    protected function createMigration(string $pluginPath, string $pluginName): void
    {
        $className = Str::studly($this->argument('name'));
        $tableName = Str::snake(Str::plural($className));
        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table";

        $content = "<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->text('description')->nullable();
            \$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
";

        File::put("{$pluginPath}/database/migrations/{$migrationName}.php", $content);
    }

    protected function createReadme(string $pluginPath, string $pluginName, string $description): void
    {
        $content = "# {$this->argument('name')} Plugin

{$description}

## Installation

This plugin is automatically loaded by the customer portal's plugin system.

## Usage

Describe how to use your plugin here.

## Configuration

Any configuration options should be documented here.

## API

Document any API endpoints provided by this plugin.

## Hooks and Filters

List any hooks and filters that this plugin provides or uses.

## License

Add your license information here.
";

        File::put("{$pluginPath}/README.md", $content);
    }
}
