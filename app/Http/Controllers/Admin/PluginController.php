<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PluginManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PluginController extends Controller
{
    protected PluginManager $pluginManager;

    public function __construct(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * Display a listing of plugins
     */
    public function index()
    {
        $plugins = $this->pluginManager->getPlugins();

        return view('admin.plugins.index', compact('plugins'));
    }

    /**
     * Enable a plugin
     */
    public function enable(Request $request, string $name)
    {
        try {
            $success = $this->pluginManager->enablePlugin($name);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => "Plugin '{$name}' has been enabled."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Failed to enable plugin '{$name}'."
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error("Failed to enable plugin {$name}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while enabling the plugin.'
            ], 500);
        }
    }

    /**
     * Disable a plugin
     */
    public function disable(Request $request, string $name)
    {
        try {
            $success = $this->pluginManager->disablePlugin($name);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => "Plugin '{$name}' has been disabled."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Failed to disable plugin '{$name}'."
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error("Failed to disable plugin {$name}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while disabling the plugin.'
            ], 500);
        }
    }

    /**
     * Show plugin details
     */
    public function show(string $name)
    {
        $plugin = $this->pluginManager->getPlugin($name);

        if (!$plugin) {
            abort(404, 'Plugin not found');
        }

        return view('admin.plugins.show', compact('plugin'));
    }

    /**
     * Install a plugin
     */
    public function install(Request $request)
    {
        $request->validate([
            'plugin_file' => 'required|file|mimes:zip',
        ]);

        try {
            // Extract and install plugin
            // This is a simplified implementation
            $tempPath = $request->file('plugin_file')->getPathname();
            $success = $this->pluginManager->installPlugin($tempPath);

            if ($success) {
                return redirect()->route('admin.plugins.index')
                    ->with('success', 'Plugin installed successfully.');
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to install plugin.');
            }
        } catch (\Exception $e) {
            Log::error("Failed to install plugin: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while installing the plugin.');
        }
    }

    /**
     * Uninstall a plugin
     */
    public function uninstall(Request $request, string $name)
    {
        try {
            $success = $this->pluginManager->uninstallPlugin($name);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => "Plugin '{$name}' has been uninstalled."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Failed to uninstall plugin '{$name}'."
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error("Failed to uninstall plugin {$name}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uninstalling the plugin.'
            ], 500);
        }
    }

    /**
     * Get plugin information via API
     */
    public function apiInfo(string $name)
    {
        $plugin = $this->pluginManager->getPlugin($name);

        if (!$plugin) {
            return response()->json([
                'success' => false,
                'message' => 'Plugin not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $plugin
        ]);
    }

    /**
     * List all plugins via API
     */
    public function apiList()
    {
        $plugins = $this->pluginManager->getPlugins();

        return response()->json([
            'success' => true,
            'data' => $plugins->toArray()
        ]);
    }
}
