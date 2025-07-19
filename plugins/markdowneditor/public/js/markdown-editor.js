/**
 * Markdown Editor Component
 * Converts regular textareas into markdown editors
 */
class MarkdownEditor {
    constructor(element, options = {}) {
        this.element = element;
        this.options = {
            theme: 'default',
            toolbar: ['bold', 'italic', 'strikethrough', '|', 'heading', 'quote', 'code', '|', 'unordered-list', 'ordered-list', '|', 'link', 'image', '|', 'preview'],
            preview: true,
            splitView: true,
            autosave: false,
            autosaveInterval: 30000,
            placeholder: 'Enter your markdown here...',
            ...options
        };

        this.init();
    }

    init() {
        this.createWrapper();
        this.createToolbar();
        this.createEditor();
        this.createPreview();
        this.bindEvents();

        if (this.options.autosave) {
            this.enableAutosave();
        }
    }

    createWrapper() {
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'markdown-editor-wrapper';
        this.wrapper.innerHTML = `
            <div class="markdown-editor-container">
                <div class="markdown-editor-toolbar"></div>
                <div class="markdown-editor-content">
                    <div class="markdown-editor-input"></div>
                    <div class="markdown-editor-preview" style="display: ${this.options.splitView ? 'block' : 'none'}"></div>
                </div>
            </div>
        `;

        this.element.parentNode.insertBefore(this.wrapper, this.element);
        this.element.style.display = 'none';
    }

    createToolbar() {
        this.toolbar = this.wrapper.querySelector('.markdown-editor-toolbar');

        const toolbarButtons = {
            'bold': { icon: 'fas fa-bold', title: 'Bold', action: () => this.insertMarkdown('**', '**', 'bold text') },
            'italic': { icon: 'fas fa-italic', title: 'Italic', action: () => this.insertMarkdown('*', '*', 'italic text') },
            'strikethrough': { icon: 'fas fa-strikethrough', title: 'Strikethrough', action: () => this.insertMarkdown('~~', '~~', 'strikethrough') },
            'heading': { icon: 'fas fa-heading', title: 'Heading', action: () => this.insertMarkdown('# ', '', 'Heading') },
            'quote': { icon: 'fas fa-quote-left', title: 'Quote', action: () => this.insertMarkdown('> ', '', 'Quote') },
            'code': { icon: 'fas fa-code', title: 'Code', action: () => this.insertCode() },
            'unordered-list': { icon: 'fas fa-list-ul', title: 'Bullet List', action: () => this.insertMarkdown('* ', '', 'List item') },
            'ordered-list': { icon: 'fas fa-list-ol', title: 'Numbered List', action: () => this.insertMarkdown('1. ', '', 'List item') },
            'link': { icon: 'fas fa-link', title: 'Link', action: () => this.insertLink() },
            'image': { icon: 'fas fa-image', title: 'Image', action: () => this.insertImage() },
            'preview': { icon: 'fas fa-eye', title: 'Toggle Preview', action: () => this.togglePreview() }
        };

        this.options.toolbar.forEach(item => {
            if (item === '|') {
                const separator = document.createElement('span');
                separator.className = 'toolbar-separator';
                this.toolbar.appendChild(separator);
            } else if (toolbarButtons[item]) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'toolbar-button';
                button.title = toolbarButtons[item].title;
                button.innerHTML = `<i class="${toolbarButtons[item].icon}"></i>`;
                button.addEventListener('click', toolbarButtons[item].action);
                this.toolbar.appendChild(button);
            }
        });
    }

    createEditor() {
        this.editorContainer = this.wrapper.querySelector('.markdown-editor-input');
        this.textarea = document.createElement('textarea');
        this.textarea.className = 'markdown-textarea';
        this.textarea.placeholder = this.options.placeholder;
        this.textarea.value = this.element.value;

        this.editorContainer.appendChild(this.textarea);
    }

    createPreview() {
        this.previewContainer = this.wrapper.querySelector('.markdown-editor-preview');
        this.previewContainer.innerHTML = '<div class="preview-content">Preview will appear here...</div>';
        this.previewContent = this.previewContainer.querySelector('.preview-content');
    }

    bindEvents() {
        this.textarea.addEventListener('input', () => {
            this.element.value = this.textarea.value;
            this.updatePreview();
        });

        this.textarea.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                e.preventDefault();
                this.insertTab();
            }
        });
    }

    insertMarkdown(prefix, suffix = '', placeholder = '') {
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;
        const selectedText = this.textarea.value.substring(start, end);
        const replacement = prefix + (selectedText || placeholder) + suffix;

        this.textarea.setRangeText(replacement, start, end, 'end');
        this.textarea.focus();
        this.updateValue();
    }

    insertCode() {
        const selectedText = this.getSelectedText();
        if (selectedText.includes('\n')) {
            this.insertMarkdown('```\n', '\n```', 'code block');
        } else {
            this.insertMarkdown('`', '`', 'code');
        }
    }

    insertLink() {
        const selectedText = this.getSelectedText();
        const url = prompt('Enter URL:');
        if (url) {
            this.insertMarkdown(`[${selectedText || 'Link text'}](`, ')', url);
        }
    }

    insertImage() {
        const selectedText = this.getSelectedText();
        const url = prompt('Enter image URL:');
        if (url) {
            this.insertMarkdown(`![${selectedText || 'Alt text'}](`, ')', url);
        }
    }

    insertTab() {
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;
        this.textarea.setRangeText('    ', start, end, 'end');
        this.updateValue();
    }

    getSelectedText() {
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;
        return this.textarea.value.substring(start, end);
    }

    updateValue() {
        this.element.value = this.textarea.value;
        this.updatePreview();
    }

    updatePreview() {
        if (!this.options.preview) return;

        const content = this.textarea.value;
        if (!content.trim()) {
            this.previewContent.innerHTML = '<p class="text-muted">Preview will appear here...</p>';
            return;
        }

        // Simple markdown to HTML conversion
        let html = this.parseMarkdown(content);
        this.previewContent.innerHTML = html;
    }

    parseMarkdown(content) {
        let html = content;

        // Headers
        html = html.replace(/^# (.+)$/gm, '<h1>$1</h1>');
        html = html.replace(/^## (.+)$/gm, '<h2>$1</h2>');
        html = html.replace(/^### (.+)$/gm, '<h3>$1</h3>');

        // Bold and italic
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
        html = html.replace(/~~(.+?)~~/g, '<del>$1</del>');

        // Code
        html = html.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');
        html = html.replace(/`(.+?)`/g, '<code>$1</code>');

        // Links
        html = html.replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2">$1</a>');

        // Images
        html = html.replace(/!\[(.+?)\]\((.+?)\)/g, '<img src="$2" alt="$1">');

        // Lists
        html = html.replace(/^\* (.+)$/gm, '<li>$1</li>');
        html = html.replace(/^(\d+)\. (.+)$/gm, '<li>$2</li>');

        // Wrap consecutive list items
        html = html.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');

        // Blockquotes
        html = html.replace(/^> (.+)$/gm, '<blockquote>$1</blockquote>');

        // Paragraphs
        html = html.split('\n\n').map(p => p.trim() ? `<p>${p.replace(/\n/g, '<br>')}</p>` : '').join('');

        return html;
    }

    togglePreview() {
        const isVisible = this.previewContainer.style.display !== 'none';
        this.previewContainer.style.display = isVisible ? 'none' : 'block';

        if (!isVisible) {
            this.updatePreview();
        }
    }

    enableAutosave() {
        setInterval(() => {
            if (this.textarea.value !== this.lastSavedValue) {
                this.save();
            }
        }, this.options.autosaveInterval);
    }

    save() {
        this.lastSavedValue = this.textarea.value;
        // Trigger save event
        this.element.dispatchEvent(new CustomEvent('markdownsave', {
            detail: { content: this.textarea.value }
        }));
    }

    getValue() {
        return this.textarea.value;
    }

    setValue(value) {
        this.textarea.value = value;
        this.element.value = value;
        this.updatePreview();
    }

    destroy() {
        this.wrapper.remove();
        this.element.style.display = '';
    }
}

// Auto-initialize markdown editors
document.addEventListener('DOMContentLoaded', function () {
    // Initialize based on classes
    document.querySelectorAll('.markdown-editor, .md-editor').forEach(element => {
        if (!element.classList.contains('no-markdown')) {
            new MarkdownEditor(element);
        }
    });

    // Initialize based on data attributes
    document.querySelectorAll('[data-markdown-editor]').forEach(element => {
        const options = element.dataset.markdownEditor ? JSON.parse(element.dataset.markdownEditor) : {};
        new MarkdownEditor(element, options);
    });
});

// Make MarkdownEditor available globally
window.MarkdownEditor = MarkdownEditor;
