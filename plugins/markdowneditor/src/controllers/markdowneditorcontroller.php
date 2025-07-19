<?php

namespace Plugins\MarkdownEditor\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MarkdownEditorController extends Controller
{
    /**
     * Display the markdown editor demo page
     */
    public function index()
    {
        return view('markdowneditor::index');
    }

    /**
     * Display the markdown editor component
     */
    public function editor()
    {
        return view('markdowneditor::editor');
    }

    /**
     * Preview markdown content
     */
    public function preview(Request $request): JsonResponse
    {
        $content = $request->input('content', '');

        // Convert markdown to HTML
        $html = $this->parseMarkdown($content);

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Save markdown content
     */
    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // In a real application, you'd save to database
        // For demo purposes, we'll just return success

        return response()->json([
            'success' => true,
            'message' => 'Content saved successfully!',
            'data' => [
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'html' => $this->parseMarkdown($request->input('content')),
                'saved_at' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Get markdown editor configuration
     */
    public function config(): JsonResponse
    {
        $config = config('markdowneditor', []);

        return response()->json([
            'success' => true,
            'config' => array_merge([
                'theme' => 'default',
                'auto_save' => true,
                'preview_mode' => 'split',
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
                ]
            ], $config)
        ]);
    }

    /**
     * Parse markdown content to HTML
     */
    protected function parseMarkdown(string $content): string
    {
        // Basic markdown parsing - in production, use a proper parser like Parsedown
        $html = $content;

        // Headers
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);

        // Bold and italic
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);

        // Code blocks
        $html = preg_replace('/```(\w+)?\n(.*?)\n```/s', '<pre><code class="language-$1">$2</code></pre>', $html);
        $html = preg_replace('/`(.+?)`/', '<code>$1</code>', $html);

        // Links
        $html = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $html);

        // Lists
        $html = preg_replace('/^\* (.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $html);

        // Line breaks
        $html = preg_replace('/\n\n/', '</p><p>', $html);
        $html = '<p>' . $html . '</p>';

        // Clean up empty paragraphs
        $html = preg_replace('/<p><\/p>/', '', $html);

        return $html;
    }
}
