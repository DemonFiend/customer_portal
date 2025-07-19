@extends('layouts.app')

@section('title', 'Markdown Editor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Markdown Editor
                    </h4>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary" id="previewToggle">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="splitToggle">
                            <i class="fas fa-columns"></i> Split View
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="fullscreenToggle">
                            <i class="fas fa-expand"></i> Fullscreen
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        <!-- Editor Panel -->
                        <div class="col-md-6" id="editorPanel">
                            <div class="p-3">
                                <form id="markdownForm">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Document Title</label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter document title...">
                                    </div>

                                    <!-- Toolbar -->
                                    <div class="markdown-toolbar mb-2">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" data-action="bold" title="Bold">
                                                <i class="fas fa-bold"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" data-action="italic" title="Italic">
                                                <i class="fas fa-italic"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" data-action="strikethrough" title="Strikethrough">
                                                <i class="fas fa-strikethrough"></i>
                                            </button>
                                        </div>

                                        <div class="btn-group btn-group-sm ms-2" role="group">
                                            <button type="button" class="btn btn-outline-secondary" data-action="heading" title="Heading">
                                                <i class="fas fa-heading"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" data-action="quote" title="Quote">
                                                <i class="fas fa-quote-left"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" data-action="code" title="Code">
                                                <i class="fas fa-code"></i>
                                            </button>
                                        </div>

                                        <div class="btn-group btn-group-sm ms-2" role="group">
                                            <button type="button" class="btn btn-outline-secondary" data-action="unordered-list" title="Bullet List">
                                                <i class="fas fa-list-ul"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" data-action="ordered-list" title="Numbered List">
                                                <i class="fas fa-list-ol"></i>
                                            </button>
                                        </div>

                                        <div class="btn-group btn-group-sm ms-2" role="group">
                                            <button type="button" class="btn btn-outline-secondary" data-action="link" title="Link">
                                                <i class="fas fa-link"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" data-action="image" title="Image">
                                                <i class="fas fa-image"></i>
                                            </button>
                                        </div>

                                        <div class="btn-group btn-group-sm ms-2" role="group">
                                            <button type="button" class="btn btn-outline-primary" id="saveBtn" title="Save">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Editor Textarea -->
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content</label>
                                        <textarea class="form-control" id="content" name="content" rows="20" placeholder="Start writing your markdown content here...
                                        
# Example Heading
This is **bold text** and this is *italic text*.

## Code Example
```javascript
console.log('Hello, Markdown!');
```

### Lists
* Item 1
* Item 2
* Item 3

### Links
[Visit Example](https://example.com)

### Quotes
> This is a quote block"></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Preview Panel -->
                        <div class="col-md-6 border-start" id="previewPanel">
                            <div class="p-3">
                                <h5 class="mb-3">
                                    <i class="fas fa-eye"></i> Live Preview
                                </h5>
                                <div id="previewContent" class="markdown-preview">
                                    <p class="text-muted">Start typing to see the preview...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Messages -->
<div class="position-fixed bottom-0 end-0 p-3">
    <div class="toast" id="statusToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Markdown Editor</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="statusMessage">
            <!-- Status message will be inserted here -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .markdown-toolbar {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.5rem;
    }

    .markdown-preview {
        min-height: 400px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
    }

    .markdown-preview h1,
    .markdown-preview h2,
    .markdown-preview h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .markdown-preview h1 {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.3rem;
    }

    .markdown-preview pre {
        background-color: #f6f8fa;
        border: 1px solid #d1d9e0;
        border-radius: 6px;
        padding: 16px;
        overflow: auto;
    }

    .markdown-preview code {
        background-color: rgba(175, 184, 193, 0.2);
        padding: 0.2em 0.4em;
        border-radius: 6px;
        font-size: 85%;
    }

    .markdown-preview blockquote {
        border-left: 4px solid #d1d9e0;
        padding-left: 1rem;
        margin-left: 0;
        color: #6a737d;
    }

    .markdown-preview ul,
    .markdown-preview ol {
        padding-left: 2rem;
    }

    .fullscreen-mode {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        background: white;
    }

    .preview-only #editorPanel {
        display: none;
    }

    .preview-only #previewPanel {
        max-width: 100%;
        flex: 0 0 100%;
    }

    #content {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 14px;
        line-height: 1.5;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contentTextarea = document.getElementById('content');
        const previewContent = document.getElementById('previewContent');
        const titleInput = document.getElementById('title');
        const saveBtn = document.getElementById('saveBtn');
        const markdownForm = document.getElementById('markdownForm');
        const statusToast = new bootstrap.Toast(document.getElementById('statusToast'));

        let debounceTimeout;
        let isPreviewMode = false;
        let isSplitMode = true;
        let isFullscreen = false;

        // Preview functionality
        function updatePreview() {
            const content = contentTextarea.value;

            if (!content.trim()) {
                previewContent.innerHTML = '<p class="text-muted">Start typing to see the preview...</p>';
                return;
            }

            fetch('{{ route("markdowneditor.preview") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        content: content
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        previewContent.innerHTML = data.html;
                    }
                })
                .catch(error => {
                    console.error('Preview error:', error);
                    previewContent.innerHTML = '<p class="text-danger">Error loading preview</p>';
                });
        }

        // Debounced preview update
        contentTextarea.addEventListener('input', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(updatePreview, 500);
        });

        // Toolbar actions
        document.querySelectorAll('[data-action]').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                insertMarkdown(action);
            });
        });

        function insertMarkdown(action) {
            const start = contentTextarea.selectionStart;
            const end = contentTextarea.selectionEnd;
            const selectedText = contentTextarea.value.substring(start, end);
            let replacement = '';

            switch (action) {
                case 'bold':
                    replacement = `**${selectedText || 'bold text'}**`;
                    break;
                case 'italic':
                    replacement = `*${selectedText || 'italic text'}*`;
                    break;
                case 'strikethrough':
                    replacement = `~~${selectedText || 'strikethrough text'}~~`;
                    break;
                case 'heading':
                    replacement = `# ${selectedText || 'Heading'}`;
                    break;
                case 'quote':
                    replacement = `> ${selectedText || 'Quote'}`;
                    break;
                case 'code':
                    replacement = selectedText.includes('\n') ?
                        `\`\`\`\n${selectedText || 'code'}\n\`\`\`` :
                        `\`${selectedText || 'code'}\``;
                    break;
                case 'unordered-list':
                    replacement = `* ${selectedText || 'List item'}`;
                    break;
                case 'ordered-list':
                    replacement = `1. ${selectedText || 'List item'}`;
                    break;
                case 'link':
                    replacement = `[${selectedText || 'Link text'}](URL)`;
                    break;
                case 'image':
                    replacement = `![${selectedText || 'Alt text'}](Image URL)`;
                    break;
            }

            const newValue = contentTextarea.value.substring(0, start) + replacement + contentTextarea.value.substring(end);
            contentTextarea.value = newValue;
            contentTextarea.focus();

            // Update cursor position
            const newCursorPos = start + replacement.length;
            contentTextarea.setSelectionRange(newCursorPos, newCursorPos);

            updatePreview();
        }

        // View mode toggles
        document.getElementById('previewToggle').addEventListener('click', function() {
            isPreviewMode = !isPreviewMode;
            document.querySelector('.card').classList.toggle('preview-only', isPreviewMode);
            this.classList.toggle('active', isPreviewMode);
            updatePreview();
        });

        document.getElementById('splitToggle').addEventListener('click', function() {
            isSplitMode = !isSplitMode;
            if (isSplitMode) {
                document.getElementById('editorPanel').className = 'col-md-6';
                document.getElementById('previewPanel').className = 'col-md-6 border-start';
            } else {
                document.getElementById('editorPanel').className = 'col-12';
                document.getElementById('previewPanel').style.display = 'none';
            }
            this.classList.toggle('active', isSplitMode);
        });

        document.getElementById('fullscreenToggle').addEventListener('click', function() {
            isFullscreen = !isFullscreen;
            document.querySelector('.card').classList.toggle('fullscreen-mode', isFullscreen);
            this.classList.toggle('active', isFullscreen);
            this.innerHTML = isFullscreen ?
                '<i class="fas fa-compress"></i> Exit Fullscreen' :
                '<i class="fas fa-expand"></i> Fullscreen';
        });

        // Save functionality
        saveBtn.addEventListener('click', function() {
            const formData = new FormData(markdownForm);

            fetch('{{ route("markdowneditor.save") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Content saved successfully!', 'success');
                    } else {
                        showToast('Error saving content', 'error');
                    }
                })
                .catch(error => {
                    console.error('Save error:', error);
                    showToast('Error saving content', 'error');
                });
        });

        function showToast(message, type) {
            const statusMessage = document.getElementById('statusMessage');
            statusMessage.textContent = message;
            statusMessage.className = `toast-body ${type === 'success' ? 'text-success' : 'text-danger'}`;
            statusToast.show();
        }

        // Initial preview
        updatePreview();
    });
</script>
@endpush