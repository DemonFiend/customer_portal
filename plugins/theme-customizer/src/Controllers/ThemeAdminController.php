<?php

namespace Plugins\ThemeCustomizer\Controllers;

use App\Http\Controllers\Controller;
use App\SystemSetting;
use App\Traits\Throttles;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThemeAdminController extends Controller
{
    use Throttles;

    /**
     * Show admin page (requires authentication)
     */
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('settings_authenticated') !== 1) {
            return redirect('/admin/auth');
        }

        $systemSetting = SystemSetting::first();
        $pluginConfig = $this->getPluginConfig();

        return view('theme-customizer::admin.index', [
            'darkModeEnabled' => $pluginConfig['dark_mode_enabled'] ?? true,
            'darkModeDefault' => $pluginConfig['dark_mode_default'] ?? false,
            'systemSetting' => $systemSetting
        ]);
    }

    /**
     * Show authentication form
     */
    public function showAuth(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('settings_authenticated') === 1) {
            return redirect('/admin');
        }

        return view('theme-customizer::admin.auth');
    }

    /**
     * Authenticate admin access
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'key' => 'required',
        ]);

        $systemSetting = SystemSetting::first();
        if (! $systemSetting) {
            return redirect()->back()->withErrors('Settings key not found.');
        }

        if ($this->getThrottleValue('settings', $request->getClientIp()) > 10) {
            return redirect()->back()->withErrors('Too many failed authentication attempts.');
        }

        if ($systemSetting->settings_key && $systemSetting->settings_key == $request->input('key')) {
            $this->resetThrottleValue('settings', $request->getClientIp());
            $request->session()->put('settings_authenticated', 1);

            return redirect('/admin');
        }

        $this->incrementThrottleValue('settings', $request->getClientIp());

        return redirect()->back()->withErrors('Invalid settings key.');
    }

    /**
     * Update theme settings
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        if ($request->session()->get('settings_authenticated') !== 1) {
            return redirect('/admin/auth');
        }

        $request->validate([
            'dark_mode_enabled' => 'nullable|boolean',
            'dark_mode_default' => 'nullable|boolean',
        ]);

        $pluginConfigPath = base_path('plugins/theme-customizer/plugin.json');
        $config = json_decode(file_get_contents($pluginConfigPath), true);

        $config['config']['dark_mode_enabled'] = $request->input('dark_mode_enabled', false) === 'on' || $request->input('dark_mode_enabled') === true;
        $config['config']['dark_mode_default'] = $request->input('dark_mode_default', false) === 'on' || $request->input('dark_mode_default') === true;

        file_put_contents($pluginConfigPath, json_encode($config, JSON_PRETTY_PRINT));

        return redirect('/admin')->with('success', 'Theme settings updated successfully.');
    }

    /**
     * Get plugin configuration
     */
    protected function getPluginConfig(): array
    {
        $pluginConfigPath = base_path('plugins/theme-customizer/plugin.json');
        if (file_exists($pluginConfigPath)) {
            return json_decode(file_get_contents($pluginConfigPath), true)['config'] ?? [];
        }
        return [];
    }

    /**
     * SCXP-BetterPortal: Show plugin creator form
     */
    public function showPluginCreator(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('settings_authenticated') !== 1) {
            return redirect('/admin/auth');
        }

        return view('theme-customizer::admin.plugin-creator');
    }

    /**
     * SCXP-BetterPortal: Create a new plugin
     */
    public function createPlugin(Request $request): RedirectResponse
    {
        if ($request->session()->get('settings_authenticated') !== 1) {
            return redirect('/admin/auth');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'restart_after' => 'nullable|boolean',
        ]);

        try {
            $name = $request->input('name');
            $author = $request->input('author');
            $description = $request->input('description', 'A custom plugin for the customer portal');
            $restartAfter = $request->input('restart_after', false);

            // Generate plugin using artisan command
            $pluginName = str_replace(' ', '', ucwords($name));
            
            $command = sprintf(
                'cd %s && php artisan make:plugin "%s" --author="%s" --description="%s" 2>&1',
                base_path(),
                addslashes($pluginName),
                addslashes($author),
                addslashes($description)
            );

            $output = shell_exec($command);

            // Run composer dump-autoload
            $autoloadCommand = sprintf('cd %s && composer dump-autoload 2>&1', base_path());
            $autoloadOutput = shell_exec($autoloadCommand);

            if ($restartAfter) {
                return redirect('/admin')->with('success', 'Plugin created successfully! Server will restart shortly. Please refresh your page in 1-2 minutes.');
            }

            return redirect('/admin')->with('success', 'Plugin created successfully! Plugin: ' . $pluginName);
        } catch (\Exception $e) {
            return redirect('/admin')->withErrors('Failed to create plugin: ' . $e->getMessage());
        }
    }

    /**
     * SCXP-BetterPortal: Restart the server
     */
    public function restartServer(Request $request): RedirectResponse
    {
        if ($request->session()->get('settings_authenticated') !== 1) {
            return redirect('/admin/auth');
        }

        try {
            // Restart docker container
            $command = 'docker-compose restart app 2>&1';
            $output = shell_exec($command);

            // Alternative: restart all containers
            if (empty($output) || strpos($output, 'error') !== false) {
                $command = 'docker-compose restart 2>&1';
                shell_exec($command);
            }

            return redirect('/admin')->with('restart', 'Server restart initiated! Please refresh this page in 1-2 minutes.');
        } catch (\Exception $e) {
            return redirect('/admin')->withErrors('Failed to restart server: ' . $e->getMessage());
        }
    }
}
