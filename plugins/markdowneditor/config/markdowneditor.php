<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Markdown Editor Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the Markdown Editor plugin
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Editor Theme
    |--------------------------------------------------------------------------
    |
    | The visual theme for the markdown editor
    |
    */
    'theme' => env('MARKDOWN_EDITOR_THEME', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Auto Save
    |--------------------------------------------------------------------------
    |
    | Whether to automatically save content as the user types
    |
    */
    'auto_save' => env('MARKDOWN_EDITOR_AUTO_SAVE', true),

    /*
    |--------------------------------------------------------------------------
    | Auto Save Interval
    |--------------------------------------------------------------------------
    |
    | How often to auto-save content (in milliseconds)
    |
    */
    'auto_save_interval' => env('MARKDOWN_EDITOR_AUTO_SAVE_INTERVAL', 30000), // 30 seconds

    /*
    |--------------------------------------------------------------------------
    | Preview Mode
    |--------------------------------------------------------------------------
    |
    | Default preview mode: 'split', 'preview', 'editor'
    |
    */
    'preview_mode' => env('MARKDOWN_EDITOR_PREVIEW_MODE', 'split'),

    /*
    |--------------------------------------------------------------------------
    | Toolbar Buttons
    |--------------------------------------------------------------------------
    |
    | Available toolbar buttons for the editor
    |
    */
    'toolbar' => [
        'bold',
        'italic',
        'strikethrough',
        '|',
        'heading',
        'quote',
        'code',
        '|',
        'unordered-list',
        'ordered-list',
        '|',
        'link',
        'image',
        '|',
        'preview',
        'side-by-side',
        'fullscreen'
    ],

    /*
    |--------------------------------------------------------------------------
    | Spell Check
    |--------------------------------------------------------------------------
    |
    | Enable browser spell checking in the editor
    |
    */
    'spell_check' => env('MARKDOWN_EDITOR_SPELL_CHECK', false),

    /*
    |--------------------------------------------------------------------------
    | Line Numbers
    |--------------------------------------------------------------------------
    |
    | Show line numbers in the editor
    |
    */
    'line_numbers' => env('MARKDOWN_EDITOR_LINE_NUMBERS', false),

    /*
    |--------------------------------------------------------------------------
    | Word Wrap
    |--------------------------------------------------------------------------
    |
    | Enable word wrapping in the editor
    |
    */
    'word_wrap' => env('MARKDOWN_EDITOR_WORD_WRAP', true),

    /*
    |--------------------------------------------------------------------------
    | Tab Size
    |--------------------------------------------------------------------------
    |
    | Number of spaces for tab indentation
    |
    */
    'tab_size' => env('MARKDOWN_EDITOR_TAB_SIZE', 4),

    /*
    |--------------------------------------------------------------------------
    | Parser Options
    |--------------------------------------------------------------------------
    |
    | Options for the markdown parser
    |
    */
    'parser' => [
        'enable_tables' => true,
        'enable_footnotes' => false,
        'enable_definition_lists' => false,
        'enable_abbreviations' => false,
        'enable_math' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Security options for markdown rendering
    |
    */
    'security' => [
        'strip_html' => false,
        'escape_html' => false,
        'allow_raw_html' => false,
        'allowed_tags' => [
            'p',
            'br',
            'strong',
            'em',
            'u',
            's',
            'del',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'ul',
            'ol',
            'li',
            'blockquote',
            'pre',
            'code',
            'a',
            'img',
            'table',
            'thead',
            'tbody',
            'tr',
            'th',
            'td'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload
    |--------------------------------------------------------------------------
    |
    | Configuration for file uploads (images, attachments)
    |
    */
    'uploads' => [
        'enabled' => env('MARKDOWN_EDITOR_UPLOADS', false),
        'path' => 'uploads/markdown',
        'max_size' => env('MARKDOWN_EDITOR_MAX_UPLOAD_SIZE', 2048), // KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Hooks
    |--------------------------------------------------------------------------
    |
    | Hooks for integrating with forms and content areas
    |
    */
    'hooks' => [
        'replace_textareas' => env('MARKDOWN_EDITOR_REPLACE_TEXTAREAS', false),
        'target_classes' => ['markdown-editor', 'md-editor'],
        'target_ids' => [],
        'exclude_classes' => ['no-markdown'],
    ],
];
