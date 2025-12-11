<?php

namespace Plugins\ThemeCustomizer;

use App\Providers\BasePluginServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

class ThemeCustomizerServiceProvider extends BasePluginServiceProvider
{
    /**
     * Plugin name
     */
    protected string $pluginName = 'theme-customizer';

    /**
     * Allowed theme color keys
     */
    protected const ALLOWED_COLOR_KEYS = ['primary', 'secondary'];

    /**
     * Cached theme configuration
     */
    protected ?array $cachedThemeConfig = null;

    /**
     * Boot the plugin
     */
    protected function bootPlugin(): void
    {
        parent::bootPlugin();

        // Register hooks for theme customization
        $this->registerHooks();
        
        // Register view composers
        $this->registerViewComposers();
        
        // Register custom Blade directives
        $this->registerBladeDirectives();
    }

    /**
     * Register plugin services
     */
    protected function registerPlugin(): void
    {
        // Make theme config available globally
        $this->app->singleton('theme.config', function ($app) {
            return $this->pluginConfig['config'] ?? [];
        });
    }

    /**
     * Register hooks for theme customization
     */
    protected function registerHooks(): void
    {
        if (!app()->bound('plugin.hooks')) {
            return;
        }

        $hooks = app('plugin.hooks');

        // Cache generated CSS to avoid regenerating on every render
        $cachedCss = null;

        // Add custom CSS to all rendered content (cached)
        $hooks->addFilter('content.render', function ($content) use (&$cachedCss) {
            if ($this->isCustomCssEnabled()) {
                if ($cachedCss === null) {
                    $cachedCss = $this->generateCustomCss();
                }
                $content = "<style>{$cachedCss}</style>\n" . $content;
            }
            return $content;
        }, 10);

        // Add custom footer text
        $hooks->addFilter('footer.text', function ($text) {
            $customFooter = $this->getConfig('footer_text', '');
            return $customFooter ?: $text;
        }, 10);

        // Modify menu items
        $hooks->addFilter('menu.items', function ($items) {
            // You can add or modify menu items here
            return $items;
        }, 10);
    }

    /**
     * Register view composers to inject theme variables
     */
    protected function registerViewComposers(): void
    {
        // Cache theme configuration to avoid repeated getConfig calls
        if ($this->cachedThemeConfig === null) {
            $this->cachedThemeConfig = [
                'primaryColor' => $this->getConfig('primary_color', '#007bff'),
                'secondaryColor' => $this->getConfig('secondary_color', '#6c757d'),
                'customLogoUrl' => $this->getConfig('custom_logo_url', ''),
                'showCustomHeader' => $this->getConfig('show_custom_header', true),
                'footerText' => $this->getConfig('footer_text', ''),
            ];
        }

        // Share theme configuration with layout views only (not all views)
        View::composer(['layouts.*', 'components.*'], function ($view) {
            $view->with('themeConfig', $this->cachedThemeConfig);
        });
    }

    /**
     * Register custom Blade directives
     */
    protected function registerBladeDirectives(): void
    {
        // @themeColor('primary') - Get theme color
        Blade::directive('themeColor', function ($expression) {
            // Safely extract and validate the color key
            // Only allow single-quoted or double-quoted strings
            if (!preg_match("/^['\"]([a-z]+)['\"]$/", $expression, $matches)) {
                return "<?php echo '#000'; ?>";
            }
            
            $key = $matches[1];
            $allowedKeys = self::ALLOWED_COLOR_KEYS;
            
            // Only proceed if key is in allowed list
            if (!in_array($key, $allowedKeys, true)) {
                return "<?php echo '#000'; ?>";
            }
            
            // Now safe to use in config key
            $configKey = var_export("theme.config.{$key}_color", true);
            return "<?php echo config({$configKey}, '#000'); ?>";
        });

        // @themeLogo - Display custom logo if set (with XSS protection)
        Blade::directive('themeLogo', function () {
            return "<?php 
                \$logoUrl = config('theme.config.custom_logo_url', '');
                if (\$logoUrl) {
                    echo '<img src=\"' . e(\$logoUrl) . '\" alt=\"Logo\" class=\"custom-logo\">';
                }
            ?>";
        });
    }

    /**
     * Generate custom CSS based on configuration
     */
    protected function generateCustomCss(): string
    {
        $primaryColor = $this->validateAndSanitizeColor($this->getConfig('primary_color', '#007bff'));
        $secondaryColor = $this->validateAndSanitizeColor($this->getConfig('secondary_color', '#6c757d'));

        return "
            :root {
                --primary-color: {$primaryColor};
                --secondary-color: {$secondaryColor};
            }
            
            .btn-primary {
                background-color: var(--primary-color) !important;
                border-color: var(--primary-color) !important;
            }
            
            .btn-primary:hover {
                filter: brightness(0.85);
            }
            
            .navbar-brand, .nav-link.active {
                color: var(--primary-color) !important;
            }
            
            a {
                color: var(--primary-color);
            }
            
            .bg-primary {
                background-color: var(--primary-color) !important;
            }
            
            .text-primary {
                color: var(--primary-color) !important;
            }
            
            .custom-header {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                color: white;
                padding: 1rem;
            }
        ";
    }

    /**
     * Validate and sanitize CSS color value to prevent injection
     */
    protected function validateAndSanitizeColor(string $color): string
    {
        // Remove any potentially dangerous characters
        $color = trim($color);
        
        // Validate hex color format (#RGB or #RRGGBB)
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            return $color;
        }
        
        // Validate rgb/rgba format with proper value ranges
        if (preg_match('/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)$/', $color, $matches)) {
            // Validate RGB values are 0-255
            $r = (int)$matches[1];
            $g = (int)$matches[2];
            $b = (int)$matches[3];
            
            if ($r > 255 || $g > 255 || $b > 255) {
                return '#000000';
            }
            
            // Validate alpha if present (0-1)
            if (isset($matches[4])) {
                $alpha = (float)$matches[4];
                if ($alpha < 0 || $alpha > 1) {
                    return '#000000';
                }
                return "rgba({$r}, {$g}, {$b}, {$alpha})";
            }
            
            return "rgb({$r}, {$g}, {$b})";
        }
        
        // If invalid, return safe default
        return '#000000';
    }

    /**
     * Check if custom CSS is enabled
     */
    protected function isCustomCssEnabled(): bool
    {
        return $this->getConfig('custom_css_enabled', true);
    }

    /**
     * Get configuration value
     */
    protected function getConfig(string $key, $default = null)
    {
        return $this->pluginConfig['config'][$key] ?? $default;
    }
}
