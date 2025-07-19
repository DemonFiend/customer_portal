@extends('layouts.app')

@section('title', 'Markdown Editor Demo')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Markdown Editor Integration Demo</h1>
            <p>This page demonstrates different ways to integrate the markdown editor into your forms.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Method 1: CSS Class Integration</h5>
                </div>
                <div class="card-body">
                    <p>Add the <code>markdown-editor</code> class to any textarea:</p>
                    <form>
                        <div class="mb-3">
                            <label for="demo1" class="form-label">Content</label>
                            <textarea class="form-control markdown-editor" id="demo1" rows="8" placeholder="Type your markdown here...

# Example
This textarea will automatically become a **markdown editor** with toolbar and preview!

* Feature 1
* Feature 2
* Feature 3

[Learn more](https://example.com)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Content</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Method 2: Data Attribute Configuration</h5>
                </div>
                <div class="card-body">
                    <p>Use <code>data-markdown-editor</code> for custom configuration:</p>
                    <form>
                        <div class="mb-3">
                            <label for="demo2" class="form-label">Article Content</label>
                            <textarea
                                class="form-control"
                                id="demo2"
                                rows="8"
                                data-markdown-editor='{"preview": true, "toolbar": ["bold", "italic", "heading", "|", "unordered-list", "link"], "theme": "dark"}'
                                placeholder="This editor has a custom toolbar configuration...

## Custom Configuration
Only specific toolbar buttons are shown.

**Bold text** and *italic text* are available.

### Lists work too:
* Item 1  
* Item 2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Publish Article</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Method 3: JavaScript Initialization</h5>
                </div>
                <div class="card-body">
                    <p>Manually initialize with JavaScript for full control:</p>
                    <form>
                        <div class="mb-3">
                            <label for="demo3" class="form-label">Manual Setup</label>
                            <textarea class="form-control" id="demo3" rows="10" placeholder="This editor is initialized manually with JavaScript...

```javascript
// Example code block
const editor = new MarkdownEditor(element, {
    theme: 'default',
    preview: true,
    autosave: true
});
```

> This gives you full control over the editor initialization."></textarea>
                        </div>
                        <button type="button" class="btn btn-info" onclick="initializeManualEditor()">Initialize Editor</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Method 4: Existing Form Enhancement</h5>
                </div>
                <div class="card-body">
                    <p>Example of how to enhance existing customer portal forms:</p>

                    <div class="alert alert-info">
                        <h6>For Ticket Creation:</h6>
                        <p>Add to your ticket form view:</p>
                        <pre><code>&lt;textarea class="form-control markdown-editor" name="ticket_content"&gt;&lt;/textarea&gt;</code></pre>
                    </div>

                    <div class="alert alert-success">
                        <h6>For Customer Notes:</h6>
                        <p>Add to your notes form:</p>
                        <pre><code>&lt;textarea class="form-control md-editor" name="notes"&gt;&lt;/textarea&gt;</code></pre>
                    </div>

                    <div class="alert alert-warning">
                        <h6>For Email Templates:</h6>
                        <p>Enhance email template editing:</p>
                        <pre><code>&lt;textarea class="form-control" data-markdown-editor='{"toolbar": ["bold", "italic", "link"]}' name="email_content"&gt;&lt;/textarea&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('plugins/markdowneditor/css/markdown-editor.css') }}">
<style>
    pre {
        background: #f8f9fa;
        padding: 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    .alert pre {
        margin-bottom: 0;
        background: rgba(255, 255, 255, 0.3);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('plugins/markdowneditor/js/markdown-editor.js') }}"></script>
<script>
    function initializeManualEditor() {
        const textarea = document.getElementById('demo3');

        // Check if already initialized
        if (textarea.style.display === 'none') {
            alert('Editor already initialized!');
            return;
        }

        // Initialize the markdown editor
        const editor = new MarkdownEditor(textarea, {
            theme: 'default',
            preview: true,
            autosave: false,
            toolbar: [
                'bold', 'italic', 'strikethrough', '|',
                'heading', 'quote', 'code', '|',
                'unordered-list', 'ordered-list', '|',
                'link', 'image', '|',
                'preview'
            ]
        });

        // Listen for save events
        textarea.addEventListener('markdownsave', function(e) {
            console.log('Content auto-saved:', e.detail.content);
        });

        alert('Markdown editor initialized successfully!');
    }

    // Demo of programmatic content manipulation
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Add event listeners to all markdown editors
        document.querySelectorAll('.markdown-editor, .md-editor').forEach(function(element) {
            element.addEventListener('input', function() {
                console.log('Markdown content changed in:', element.id || 'unnamed textarea');
            });
        });
    });
</script>
@endpush