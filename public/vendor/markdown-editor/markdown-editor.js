/**
 * Modern Developer Markdown & Split Live Preview Editor Engine
 */

let markdownEditorInstance = null;

class MarkdownEditor {
    constructor(options = {}) {
        this.container = document.querySelector(options.container || '#markdownEditor');
        this.textarea = document.querySelector(options.textarea || '#mdTextarea');
        this.preview = document.querySelector(options.preview || '#mdPreviewContent');
        this.hiddenContent = document.querySelector(options.hiddenContent || '#content');
        this.layout = options.defaultLayout || 'split';

        if (!this.container || !this.textarea || !this.preview) {
            console.warn('MarkdownEditor: required DOM elements missing');
            return;
        }

        this.initTurndown();
        this.initMarked();
        this.initContent();
        this.bindEvents();
        this.setLayout(this.layout);
        this.renderPreview();
    }

    initTurndown() {
        if (typeof TurndownService !== 'undefined') {
            this.turndown = new TurndownService({
                headingStyle: 'atx',
                codeBlockStyle: 'fenced',
                bulletListMarker: '-'
            });

            // Keep custom classes on pre/code for language tags
            this.turndown.addRule('fencedCodeBlock', {
                filter: function (node, options) {
                    return (
                        options.codeBlockStyle === 'fenced' &&
                        node.nodeName === 'PRE' &&
                        node.firstChild &&
                        node.firstChild.nodeName === 'CODE'
                    );
                },
                replacement: function (content, node, options) {
                    const code = node.firstChild;
                    let lang = '';
                    const classList = code.className || '';
                    const langMatch = classList.match(/language-(\S+)/);
                    if (langMatch) {
                        lang = langMatch[1];
                    } else if (node.getAttribute('data-language')) {
                        lang = node.getAttribute('data-language');
                    }
                    return '\n\n```' + lang + '\n' + code.textContent.trim() + '\n```\n\n';
                }
            });
        }
    }

    initMarked() {
        if (typeof marked !== 'undefined') {
            marked.setOptions({
                gfm: true,
                breaks: true,
                headerIds: true,
                mangle: false
            });
        }
    }

    initContent() {
        let initialText = this.hiddenContent ? this.hiddenContent.value : '';

        // If the initial text is HTML, convert to clean Markdown
        if (initialText && /<\/?[a-z][\s\S]*>/i.test(initialText)) {
            if (this.turndown) {
                try {
                    initialText = this.turndown.turndown(initialText);
                } catch (e) {
                    console.warn('Turndown conversion fallback:', e);
                }
            }
        }

        this.textarea.value = initialText;
    }

    bindEvents() {
        // Real-time typing & rendering
        this.textarea.addEventListener('input', () => {
            this.renderPreview();
            this.updateLiveStats();
            this.syncHiddenContent();
        });

        // Tab Indentation & Keyboard Shortcuts
        this.textarea.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.textarea.selectionStart;
                const end = this.textarea.selectionEnd;
                this.textarea.value = this.textarea.value.substring(0, start) + '    ' + this.textarea.value.substring(end);
                this.textarea.selectionStart = this.textarea.selectionEnd = start + 4;
                this.renderPreview();
                this.syncHiddenContent();
            } else if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                this.wrapSelection('**', '**', 'bold text');
            } else if ((e.ctrlKey || e.metaKey) && e.key === 'i') {
                e.preventDefault();
                this.wrapSelection('*', '*', 'italic text');
            } else if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.insertLink();
            } else if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                e.preventDefault();
                this.wrapSelection('`', '`', 'code');
            }
        });

        // Live SERP Preview listeners
        ['title', 'slug', 'excerpt', 'meta_title', 'meta_description'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', () => updateSerpPreview());
            }
        });
        updateSerpPreview();
    }

    renderPreview() {
        const rawMarkdown = this.textarea.value;
        let html = '';

        if (typeof marked !== 'undefined') {
            html = marked.parse(rawMarkdown);
        } else {
            html = rawMarkdown.replace(/\n/g, '<br>');
        }

        this.preview.innerHTML = html;
    }

    syncHiddenContent() {
        if (!this.hiddenContent) return;
        const rawMarkdown = this.textarea.value;
        let html = '';
        if (typeof marked !== 'undefined') {
            html = marked.parse(rawMarkdown);
        } else {
            html = rawMarkdown;
        }
        this.hiddenContent.value = html;
    }

    updateLiveStats() {
        const rawText = this.textarea.value;
        const cleanText = rawText.replace(/<[^>]*>/g, ' ').replace(/[#*`_~\[\]()>-]/g, ' ').replace(/\s+/g, ' ').trim();
        const words = cleanText.length > 0 ? cleanText.split(/\s+/).length : 0;
        const chars = rawText.length;
        const readingTime = Math.max(1, Math.ceil(words / 200));

        const wordEl = document.getElementById('metricWordCount');
        const charEl = document.getElementById('metricCharCount');
        const readEl = document.getElementById('metricReadingTime');

        if (wordEl) wordEl.textContent = words.toLocaleString();
        if (charEl) charEl.textContent = chars.toLocaleString();
        if (readEl) readEl.textContent = `${readingTime} min read`;
    }

    setLayout(layout) {
        this.layout = layout;
        this.container.setAttribute('data-layout', layout);

        document.querySelectorAll('.md-layout-btn').forEach(btn => {
            if (btn.getAttribute('data-layout') === layout) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    toggleFullscreen() {
        this.container.classList.toggle('is-fullscreen');
        const isFull = this.container.classList.contains('is-fullscreen');
        const fullBtn = document.getElementById('mdFullscreenBtn');
        if (fullBtn) {
            fullBtn.classList.toggle('active', isFull);
        }
    }

    wrapSelection(before, after, defaultText = '') {
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;
        const selected = this.textarea.value.substring(start, end) || defaultText;

        const replacement = before + selected + after;
        this.textarea.value = this.textarea.value.substring(0, start) + replacement + this.textarea.value.substring(end);

        this.textarea.selectionStart = start + before.length;
        this.textarea.selectionEnd = start + before.length + selected.length;
        this.textarea.focus();

        this.renderPreview();
        this.syncHiddenContent();
        this.updateLiveStats();
    }

    insertAtCursor(text, cursorOffset = 0) {
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;

        this.textarea.value = this.textarea.value.substring(0, start) + text + this.textarea.value.substring(end);
        this.textarea.selectionStart = this.textarea.selectionEnd = start + text.length + cursorOffset;
        this.textarea.focus();

        this.renderPreview();
        this.syncHiddenContent();
        this.updateLiveStats();
    }

    insertHeading(level) {
        const prefix = '#'.repeat(level) + ' ';
        this.insertAtCursor('\n' + prefix + 'Heading Title\n');
    }

    insertCodeBlock(language = 'bash', defaultCode = '') {
        if (!defaultCode) {
            if (language === 'bash') defaultCode = '# Linux command\nsudo systemctl restart nginx';
            else if (language === 'php') defaultCode = '<?php\n\n// PHP snippet\n$posts = Post::published()->get();';
            else if (language === 'javascript') defaultCode = '// JavaScript snippet\nconst response = await fetch("/api/data");';
            else if (language === 'error') defaultCode = '[ERROR] System failure: connection refused';
            else defaultCode = 'code snippet here...';
        }

        const snippet = `\n\`\`\`${language}\n${defaultCode}\n\`\`\`\n`;
        this.insertAtCursor(snippet);
    }

    insertCallout(type = 'NOTE') {
        const snippet = `\n> **${type}:** Enter important callout message here.\n`;
        this.insertAtCursor(snippet);
    }

    insertTable() {
        const table = `\n| Column 1 | Column 2 | Column 3 |\n| :--- | :--- | :--- |\n| Item 1 | Value A | Status OK |\n| Item 2 | Value B | Status OK |\n`;
        this.insertAtCursor(table);
    }

    insertLink() {
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;
        const selected = this.textarea.value.substring(start, end) || 'link text';
        const snippet = `[${selected}](https://example.com)`;
        this.insertAtCursor(snippet);
    }

    insertImage(url, alt = 'image') {
        const snippet = `\n![${alt}](${url})\n`;
        this.insertAtCursor(snippet);
    }
}

// Global Image Insertion Callback for Media Picker
function insertImageIntoEditor(mediaItem) {
    if (!mediaItem || !markdownEditorInstance) return;
    const url = mediaItem.url || '';
    const alt = mediaItem.alt_text || mediaItem.original_name || 'image';
    markdownEditorInstance.insertImage(url, alt);
}
window.insertImageIntoEditor = insertImageIntoEditor;

// Code Snippet Modal Handlers
function openCodeSnippetModal() {
    const modal = document.getElementById('codeSnippetModal');
    if (modal) {
        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('snippetCodeInput')?.focus(), 50);
    }
}
window.openCodeSnippetModal = openCodeSnippetModal;

function closeCodeSnippetModal() {
    const modal = document.getElementById('codeSnippetModal');
    if (modal) modal.classList.add('hidden');
}
window.closeCodeSnippetModal = closeCodeSnippetModal;

function insertCustomCodeSnippet() {
    const langSelect = document.getElementById('snippetLangSelect');
    const codeInput = document.getElementById('snippetCodeInput');
    const language = langSelect ? langSelect.value : 'plaintext';
    const code = codeInput ? codeInput.value : '';

    if (!code.trim()) {
        alert('Please enter or paste your code snippet.');
        return;
    }

    if (markdownEditorInstance) {
        markdownEditorInstance.insertCodeBlock(language, code);
    }

    if (codeInput) codeInput.value = '';
    closeCodeSnippetModal();
}
window.insertCustomCodeSnippet = insertCustomCodeSnippet;

// SERP & Featured Image Handlers
function updateSerpPreview() {
    const titleInput = document.getElementById('title');
    const metaTitleInput = document.getElementById('meta_title');
    const defaultTitle = (metaTitleInput && metaTitleInput.value.trim()) || (titleInput && titleInput.value.trim()) || 'Article Title';
    const serpTitleEl = document.getElementById('serpPreviewTitle');
    if (serpTitleEl) serpTitleEl.textContent = `${defaultTitle} — sejan.dev`;

    const descInput = document.getElementById('meta_description');
    const excerptInput = document.getElementById('excerpt');
    const serpDescEl = document.getElementById('serpPreviewDesc');
    if (serpDescEl) {
        const descText = (descInput && descInput.value.trim()) || (excerptInput && excerptInput.value.trim()) || 'Detailed engineering breakdown, architectural overview, and technical implementation guide.';
        serpDescEl.textContent = descText;
    }

    const slugInput = document.getElementById('slug');
    const serpSlugEl = document.getElementById('serpPreviewSlug');
    if (serpSlugEl) serpSlugEl.textContent = (slugInput && slugInput.value.trim()) || 'article-slug';

    const titleCountEl = document.getElementById('metaTitleCount');
    if (titleCountEl && metaTitleInput) titleCountEl.textContent = metaTitleInput.value.length;

    const descCountEl = document.getElementById('metaDescCount');
    if (descCountEl && descInput) descCountEl.textContent = descInput.value.length;
}

function updateFeaturedPreview(url) {
    const previewContainer = document.getElementById('featuredImagePreview');
    const previewImg = document.getElementById('featuredImagePreviewImg');
    if (url && url.trim().length > 0) {
        if (previewImg) previewImg.src = url.trim();
        if (previewContainer) previewContainer.classList.remove('hidden');
    } else {
        if (previewContainer) previewContainer.classList.add('hidden');
    }
}
window.updateFeaturedPreview = updateFeaturedPreview;

function clearFeaturedImage() {
    const input = document.getElementById('featured_image');
    if (input) input.value = '';
    updateFeaturedPreview('');
}
window.clearFeaturedImage = clearFeaturedImage;

async function handleDirectFeaturedUpload(files) {
    if (!files || files.length === 0) return;
    const file = files[0];
    const formData = new FormData();
    formData.append('file', file);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const input = document.getElementById('featured_image');
    const origPlaceholder = input ? input.placeholder : '';
    if (input) input.placeholder = 'Uploading image...';

    try {
        const res = await fetch('/admin/media', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await res.json();
        if (res.ok && (data.url || (data.files && data.files.length > 0))) {
            const finalUrl = data.url || data.files[0].url;
            if (input) {
                input.value = finalUrl;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }
            updateFeaturedPreview(finalUrl);
        } else {
            alert(data.error || data.message || 'Failed to upload image.');
        }
    } catch (err) {
        console.error(err);
        alert('Upload request failed.');
    } finally {
        if (input) input.placeholder = origPlaceholder;
        const fileInput = document.getElementById('directFeaturedFileInput');
        if (fileInput) fileInput.value = '';
    }
}
window.handleDirectFeaturedUpload = handleDirectFeaturedUpload;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    markdownEditorInstance = new MarkdownEditor({
        container: '#markdownEditor',
        textarea: '#mdTextarea',
        preview: '#mdPreviewContent',
        hiddenContent: '#content',
        defaultLayout: 'split'
    });
    window.markdownEditorInstance = markdownEditorInstance;
});
