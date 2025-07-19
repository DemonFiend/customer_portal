<?php

namespace Plugins\MarkdownEditor;

use App\Providers\BasePluginServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class MarkdownEditorServiceProvider extends BasePluginServiceProvider
{
    /**
     * Plugin name
     */
    protected string $pluginName = 'markdowneditor';

    /**
     * Boot the plugin
     */
    protected function bootPlugin(): void
    {
        Log::info('MarkdownEditorServiceProvider booted');

        parent::bootPlugin();

        // Register view composers
        $this->registerViewComposers();

        // Register custom hooks
        $this->registerHooks();
    }

    /**
     * Register plugin services
     */
    protected function registerPlugin(): void
    {
        // Register markdown parser service (if you have one)
        // $this->app->singleton('markdown.parser', function ($app) {
        //     return new MarkdownParser();
        // });

        // Register other plugin-specific services
        $this->registerMarkdownServices();
    }

    /**
     * Register view composers
     */
    protected function registerViewComposers(): void
    {
        View::composer('markdowneditor::*', function ($view) {
            $view->with('editorConfig', $this->getEditorConfig());
        });
    }

    /**
     * Register hooks
     */
    protected function registerHooks(): void
    {
        // Listen to content rendering events
        $this->app['events']->listen('content.before_render', function ($content) {
            // Process markdown content before rendering
            return $this->processMarkdown($content);
        });
    }

    /**
     * Register markdown-specific services
     */
    protected function registerMarkdownServices(): void
    {
        // Example service registration
        // $this->app->bind('markdown.converter', function ($app) {
        //     return new MarkdownConverter($app->make('markdown.parser'));
        // });
    }

    /**
     * Get editor configuration
     */
    protected function getEditorConfig(): array
    {
        return [
            'theme' => $this->pluginConfig['config']['editor_theme'] ?? 'default',
            'auto_save' => $this->pluginConfig['config']['auto_save'] ?? true,
            'spell_check' => $this->pluginConfig['config']['spell_check'] ?? false,
        ];
    }

    /**
     * Process markdown content
     */
    protected function processMarkdown($content): string
    {
        // Example markdown processing
        // if ($this->app->bound('markdown.converter')) {
        //     $converter = $this->app->make('markdown.converter');
        //     return $converter->convert($content);
        // }

        return $content;
    }
}
