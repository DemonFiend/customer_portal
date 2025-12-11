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
}
