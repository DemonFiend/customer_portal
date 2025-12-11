<?php

namespace Plugins\ThemeCustomizer\Controllers;

use App\Http\Controllers\Controller;
use App\SystemSetting;
use App\Traits\Throttles;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\Process\Process;

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

        try {
            $pluginConfigPath = base_path('plugins/theme-customizer/plugin.json');
            
            // Check if file exists
            if (!file_exists($pluginConfigPath)) {
                throw new \Exception('Plugin configuration file not found');
            }

            // Check if file is readable
            if (!is_readable($pluginConfigPath)) {
                throw new \Exception('Plugin configuration file is not readable');
            }

            // Backup current config
            $backupPath = $pluginConfigPath . '.backup';
            if (!copy($pluginConfigPath, $backupPath)) {
                throw new \Exception('Failed to create configuration backup');
            }

            $configContent = file_get_contents($pluginConfigPath);
            if ($configContent === false) {
                throw new \Exception('Failed to read plugin configuration');
            }

            $config = json_decode($configContent, true);

            if (!$config || json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid plugin configuration: ' . json_last_error_msg());
            }

            $config['config']['dark_mode_enabled'] = $this->checkboxToBoolean($request->input('dark_mode_enabled', false));
            $config['config']['dark_mode_default'] = $this->checkboxToBoolean($request->input('dark_mode_default', false));

            // Write with atomic operation
            $tempPath = $pluginConfigPath . '.tmp';
            $result = file_put_contents($tempPath, json_encode($config, JSON_PRETTY_PRINT));

            if ($result === false) {
                throw new \Exception('Failed to write configuration');
            }

            // Atomic rename
            if (!rename($tempPath, $pluginConfigPath)) {
                throw new \Exception('Failed to update configuration');
            }

            // Remove backup on success
            if (isset($backupPath) && file_exists($backupPath)) {
                unlink($backupPath);
            }

            return redirect('/admin')->with('success', 'Theme settings updated successfully.');
        } catch (\Exception $e) {
            // Restore from backup on error
            if (isset($backupPath) && file_exists($backupPath)) {
                copy($backupPath, $pluginConfigPath);
                unlink($backupPath);
            }
            
            return redirect('/admin')->withErrors('Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Get plugin configuration
     */
    protected function getPluginConfig(): array
    {
        $pluginConfigPath = base_path('plugins/theme-customizer/plugin.json');
        
        if (!file_exists($pluginConfigPath)) {
            return [];
        }
        
        $content = file_get_contents($pluginConfigPath);
        if ($content === false) {
            return [];
        }
        
        $config = json_decode($content, true);
        if (!is_array($config) || !isset($config['config'])) {
            return [];
        }
        
        return $config['config'];
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
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'author' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-\.]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'restart_after' => 'nullable|boolean',
        ]);

        try {
            $name = $request->input('name');
            $author = $request->input('author');
            $description = $request->input('description', 'A custom plugin for the customer portal');
            $restartAfter = $request->input('restart_after', false);

            // Generate plugin name (spaces will be removed, documented behavior)
            $pluginName = str_replace(' ', '', ucwords($name));
            
            // Sanitize inputs: remove any problematic characters
            $author = preg_replace('/["\';\\\\]/', '', $author);
            $description = preg_replace('/["\';\\\\]/', '', $description);
            
            // Use Symfony Process for secure command execution
            $process = new Process([
                'php',
                'artisan',
                'make:plugin',
                $pluginName,
                '--author=' . $author,
                '--description=' . $description
            ], base_path(), null, null, 300); // 5 minute timeout

            $process->run();

            if (!$process->isSuccessful()) {
                return redirect('/admin')->withErrors('Failed to create plugin: ' . $process->getErrorOutput());
            }

            // Run composer dump-autoload
            $autoloadProcess = new Process([
                'composer',
                'dump-autoload'
            ], base_path(), null, null, 300);

            $autoloadProcess->run();

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
            // Restart docker container using Symfony Process
            $process = new Process([
                'docker-compose',
                'restart',
                'app'
            ], base_path(), null, null, 60);

            $process->run();

            // If first attempt fails, try restarting all containers
            if (!$process->isSuccessful()) {
                $fallbackProcess = new Process([
                    'docker-compose',
                    'restart'
                ], base_path(), null, null, 60);

                $fallbackProcess->run();

                if (!$fallbackProcess->isSuccessful()) {
                    return redirect('/admin')->withErrors('Failed to restart server: ' . $fallbackProcess->getErrorOutput());
                }
            }

            return redirect('/admin')->with('restart', 'Server restart initiated! Please refresh this page in 1-2 minutes.');
        } catch (\Exception $e) {
            return redirect('/admin')->withErrors('Failed to restart server: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Convert checkbox value to boolean
     */
    protected function checkboxToBoolean($value): bool
    {
        return $value === 'on' || $value === true || $value === '1';
    }
}
