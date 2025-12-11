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

        // Add custom CSS to all rendered content
        $hooks->addFilter('content.render', function ($content) {
            if ($this->isCustomCssEnabled()) {
                $customCss = $this->generateCustomCss();
                $content = "<style>{$customCss}</style>\n" . $content;
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
        // Share theme configuration with all views
        View::composer('*', function ($view) {
            $view->with('themeConfig', [
                'primaryColor' => $this->getConfig('primary_color', '#007bff'),
                'secondaryColor' => $this->getConfig('secondary_color', '#6c757d'),
                'customLogoUrl' => $this->getConfig('custom_logo_url', ''),
                'showCustomHeader' => $this->getConfig('show_custom_header', true),
                'footerText' => $this->getConfig('footer_text', ''),
            ]);
        });
    }

    /**
     * Register custom Blade directives
     */
    protected function registerBladeDirectives(): void
    {
        // @themeColor('primary') - Get theme color
        Blade::directive('themeColor', function ($expression) {
            return "<?php echo config('theme.config.{$expression}_color', '#000'); ?>";
        });

        // @themeLogo - Display custom logo if set
        Blade::directive('themeLogo', function () {
            return "<?php 
                \$logoUrl = config('theme.config.custom_logo_url', '');
                if (\$logoUrl) {
                    echo '<img src=\"' . \$logoUrl . '\" alt=\"Logo\" class=\"custom-logo\">';
                }
            ?>";
        });
    }

    /**
     * Generate custom CSS based on configuration
     */
    protected function generateCustomCss(): string
    {
        $primaryColor = $this->getConfig('primary_color', '#007bff');
        $secondaryColor = $this->getConfig('secondary_color', '#6c757d');

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
                background-color: color-mix(in srgb, var(--primary-color) 85%, black) !important;
                border-color: color-mix(in srgb, var(--primary-color) 85%, black) !important;
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
