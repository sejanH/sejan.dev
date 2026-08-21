/**
 * Global Editor State & References
 */
let globalEditorInstance = null;
let currentEditorMode = 'visual';

window.currentEditorMode = currentEditorMode;
window.globalEditorInstance = null;
window.editorInstance = null;

/**
 * Switch between Visual WYSIWYG mode and Raw HTML Source mode
 */
function switchEditorMode(mode) {
    currentEditorMode = mode;
    window.currentEditorMode = mode;

    const visualWrapper = document.getElementById('visualEditorWrapper');
    const sourceWrapper = document.getElementById('sourceEditorWrapper');
    const tabVisual = document.getElementById('tabVisualBtn');
    const tabSource = document.getElementById('tabSourceBtn');
    const rawEditor = document.getElementById('rawHtmlEditor');
    const contentInput = document.getElementById('content');
    const editor = window.editorInstance || window.globalEditorInstance || globalEditorInstance;

    if (mode === 'source') {
        let currentHtml = '';
        if (editor) {
            currentHtml = editor.getData();
        } else if (contentInput) {
            currentHtml = contentInput.value;
        }

        if (rawEditor) {
            rawEditor.value = currentHtml;
        }

        if (visualWrapper) visualWrapper.classList.add('hidden');
        if (sourceWrapper) sourceWrapper.classList.remove('hidden');

        if (tabVisual) {
            tabVisual.className = 'px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 font-medium flex items-center gap-1.5 transition cursor-pointer';
        }
        if (tabSource) {
            tabSource.className = 'px-3 py-1.5 rounded-lg bg-white text-emerald-700 shadow-xs font-bold flex items-center gap-1.5 transition cursor-pointer';
        }

        if (rawEditor) {
            rawEditor.focus();
        }
    } else {
        let sourceHtml = '';
        if (rawEditor) {
            sourceHtml = rawEditor.value;
        }

        if (editor) {
            try {
                editor.setData(sourceHtml);
            } catch (e) {
                console.warn('CKEditor setData warning:', e);
            }
        }
        if (contentInput) {
            contentInput.value = sourceHtml;
        }

        if (sourceWrapper) sourceWrapper.classList.add('hidden');
        if (visualWrapper) visualWrapper.classList.remove('hidden');

        if (tabSource) {
            tabSource.className = 'px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 font-medium flex items-center gap-1.5 transition cursor-pointer';
        }
        if (tabVisual) {
            tabVisual.className = 'px-3 py-1.5 rounded-lg bg-white text-emerald-700 shadow-xs font-bold flex items-center gap-1.5 transition cursor-pointer';
        }

        if (editor && editor.editing && editor.editing.view) {
            editor.editing.view.focus();
        }
    }
}
window.switchEditorMode = switchEditorMode;

/**
 * Open custom code snippet insertion modal
 */
function openCodeSnippetModal() {
    const modal = document.getElementById('codeSnippetModal');
    if (modal) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('snippetCodeInput')?.focus();
        }, 50);
    }
}
window.openCodeSnippetModal = openCodeSnippetModal;

/**
 * Close custom code snippet insertion modal
 */
function closeCodeSnippetModal() {
    const modal = document.getElementById('codeSnippetModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
window.closeCodeSnippetModal = closeCodeSnippetModal;

/**
 * Insert code snippet from modal into active editor
 */
function insertCustomCodeSnippet() {
    const langSelect = document.getElementById('snippetLangSelect');
    const codeInput = document.getElementById('snippetCodeInput');
    const language = langSelect ? langSelect.value : 'plaintext';
    const code = codeInput ? codeInput.value : '';

    if (!code.trim()) {
        alert('Please enter or paste your code snippet.');
        return;
    }

    if (currentEditorMode === 'source') {
        const rawEditor = document.getElementById('rawHtmlEditor');
        if (rawEditor) {
            const escaped = code.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const htmlSnippet = `\n<pre><code class="language-${language}">${escaped}</code></pre>\n`;
            const start = rawEditor.selectionStart || 0;
            const end = rawEditor.selectionEnd || 0;
            rawEditor.value = rawEditor.value.substring(0, start) + htmlSnippet + rawEditor.value.substring(end);
            rawEditor.selectionStart = rawEditor.selectionEnd = start + htmlSnippet.length;
            rawEditor.dispatchEvent(new Event('input'));
        }
    } else {
        const editor = window.editorInstance || window.globalEditorInstance || globalEditorInstance;
        if (editor) {
            insertCodeBlockToEditor(editor, language, code);
        } else {
            // Fallback to raw textarea
            const contentInput = document.getElementById('content');
            if (contentInput) {
                const escaped = code.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                contentInput.value += `\n<pre><code class="language-${language}">${escaped}</code></pre>\n`;
            }
        }
    }

    if (codeInput) codeInput.value = '';
    closeCodeSnippetModal();
}
window.insertCustomCodeSnippet = insertCustomCodeSnippet;

/**
 * Insert quick formatted snippet helpers into CKEditor or Raw HTML Editor
 */
function insertSnippetIntoEditor(type) {
    if (currentEditorMode === 'source') {
        const rawEditor = document.getElementById('rawHtmlEditor');
        if (!rawEditor) return;

        let snippetHtml = '';
        if (type === 'terminal') {
            snippetHtml = '\n<pre><code class="language-bash"># Linux / Bash command\nsudo systemctl restart nginx\nphp artisan optimize:clear</code></pre>\n';
        } else if (type === 'error') {
            snippetHtml = '\n<pre><code class="language-error">[2026-08-21 17:32:00] production.ERROR: Exception caught\nStack trace:\n#0 /app/Http/Controllers/ExampleController.php(42): handleRequest()\n#1 {main}</code></pre>\n';
        } else if (type === 'code') {
            snippetHtml = '\n<pre><code class="language-php">&lt;?php\n\n// PHP / Laravel snippet\n$result = Post::where(\'status\', \'published\')->get();</code></pre>\n';
        } else if (type === 'note') {
            snippetHtml = '\n<blockquote><p><strong>Note:</strong> Enter important context or implementation details here.</p></blockquote>\n';
        } else if (type === 'tip') {
            snippetHtml = '\n<blockquote><p><strong>Tip:</strong> Pro-tip or performance optimization recommendation.</p></blockquote>\n';
        } else if (type === 'warning') {
            snippetHtml = '\n<blockquote><p><strong>Warning:</strong> Potential pitfall or breaking change warning.</p></blockquote>\n';
        }

        if (snippetHtml) {
            const start = rawEditor.selectionStart || 0;
            const end = rawEditor.selectionEnd || 0;
            rawEditor.value = rawEditor.value.substring(0, start) + snippetHtml + rawEditor.value.substring(end);
            rawEditor.selectionStart = rawEditor.selectionEnd = start + snippetHtml.length;
            rawEditor.dispatchEvent(new Event('input'));
        }
        return;
    }

    const editor = window.editorInstance || window.globalEditorInstance || globalEditorInstance;
    if (!editor) {
        console.warn('CKEditor instance not ready yet.');
        return;
    }

    if (editor.editing && editor.editing.view) {
        editor.editing.view.focus();
    }

    if (type === 'terminal') {
        insertCodeBlockToEditor(editor, 'bash', '# Linux / Bash command\nsudo systemctl restart nginx\nphp artisan optimize:clear');
        return;
    }

    if (type === 'error') {
        insertCodeBlockToEditor(editor, 'error', '[2026-08-21 17:32:00] production.ERROR: Exception caught\nStack trace:\n#0 /app/Http/Controllers/ExampleController.php(42): handleRequest()\n#1 {main}');
        return;
    }

    if (type === 'code') {
        insertCodeBlockToEditor(editor, 'php', '<?php\n\n// PHP / Laravel snippet\n$result = Post::where(\'status\', \'published\')->get();');
        return;
    }

    let html = '';
    if (type === 'note') {
        html = `<blockquote><p><strong>Note:</strong> Enter important context or implementation details here.</p></blockquote>`;
    } else if (type === 'tip') {
        html = `<blockquote><p><strong>Tip:</strong> Pro-tip or performance optimization recommendation.</p></blockquote>`;
    } else if (type === 'warning') {
        html = `<blockquote><p><strong>Warning:</strong> Potential pitfall or breaking change warning.</p></blockquote>`;
    }

    if (html) {
        try {
            const viewFragment = editor.data.processor.toView(html);
            const modelFragment = editor.data.toModel(viewFragment);
            editor.model.insertContent(modelFragment);
        } catch (e) {
            console.error('HTML snippet insertion error:', e);
        }
    }

    syncEditorContent(editor);
}
window.insertSnippetIntoEditor = insertSnippetIntoEditor;

/**
 * Helper to reliably insert codeBlock or convert existing selection/codeBlock in CKEditor 5
 */
function insertCodeBlockToEditor(editor, language, defaultText) {
    const model = editor.model;
    const selection = model.document.selection;

    // 1. If cursor is already inside an existing codeBlock, update language
    let selectedNode = selection.getSelectedElement() || (selection.getFirstPosition() ? selection.getFirstPosition().parent : null);
    let codeBlockParent = selectedNode;
    while (codeBlockParent && codeBlockParent.name !== 'codeBlock' && codeBlockParent.parent) {
        codeBlockParent = codeBlockParent.parent;
    }

    if (codeBlockParent && codeBlockParent.name === 'codeBlock') {
        model.change(writer => {
            writer.setAttribute('language', language, codeBlockParent);
        });
        syncEditorContent(editor);
        return;
    }

    // 2. If user highlighted text, preserve it
    let textToInsert = '';
    if (!selection.isCollapsed) {
        for (const range of selection.getRanges()) {
            for (const item of range.getItems()) {
                if (item.is && (item.is('$text') || item.is('textProxy'))) {
                    textToInsert += item.data;
                } else if (item.is && item.is('element') && item.name === 'paragraph') {
                    for (const child of item.getChildren()) {
                        if (child.is && (child.is('$text') || child.is('textProxy'))) {
                            textToInsert += child.data;
                        }
                    }
                    textToInsert += '\n';
                }
            }
        }
        textToInsert = textToInsert.trim();
    }

    if (!textToInsert) {
        textToInsert = defaultText;
    }

    // 3. Insert codeBlock
    try {
        model.change(writer => {
            const codeBlock = writer.createElement('codeBlock', { language: language });
            const textNode = writer.createText(textToInsert);
            writer.append(textNode, codeBlock);
            model.insertContent(codeBlock, selection);
        });
    } catch (e) {
        console.warn('Writer createElement codeBlock fallback:', e);
        try {
            editor.execute('codeBlock', { language: language });
            if (textToInsert) {
                model.change(writer => {
                    model.insertContent(writer.createText(textToInsert));
                });
            }
        } catch (err) {
            const escaped = textToInsert.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const rawHtml = `<pre><code class="language-${language}">${escaped}</code></pre>`;
            const viewFragment = editor.data.processor.toView(rawHtml);
            const modelFragment = editor.data.toModel(viewFragment);
            model.insertContent(modelFragment);
        }
    }

    syncEditorContent(editor);
}
window.insertCodeBlockToEditor = insertCodeBlockToEditor;

/**
 * Global function called by the Media Picker modal to insert an image into CKEditor.
 */
function insertImageIntoEditor(mediaItem) {
    if (currentEditorMode === 'source') {
        const rawEditor = document.getElementById('rawHtmlEditor');
        if (rawEditor && mediaItem) {
            const imgHtml = `\n<img src="${mediaItem.url}" alt="${mediaItem.alt_text || mediaItem.original_name || ''}" class="rounded-2xl max-w-full my-6 shadow-sm" />\n`;
            const start = rawEditor.selectionStart || 0;
            const end = rawEditor.selectionEnd || 0;
            rawEditor.value = rawEditor.value.substring(0, start) + imgHtml + rawEditor.value.substring(end);
            rawEditor.selectionStart = rawEditor.selectionEnd = start + imgHtml.length;
            rawEditor.dispatchEvent(new Event('input'));
        }
        return;
    }

    const editor = window.editorInstance || window.globalEditorInstance || globalEditorInstance;
    if (!editor || !mediaItem) return;

    try {
        editor.model.change(writer => {
            const imageElement = writer.createElement('imageBlock', {
                src: mediaItem.url,
                alt: mediaItem.alt_text || mediaItem.original_name || ''
            });
            editor.model.insertContent(imageElement, editor.model.document.selection);
        });
    } catch (e) {
        console.warn('imageBlock insert failed, trying HTML fallback:', e);
        try {
            const viewFragment = editor.data.processor.toView(`<img src="${mediaItem.url}" alt="${mediaItem.alt_text || ''}" />`);
            const modelFragment = editor.data.toModel(viewFragment);
            editor.model.insertContent(modelFragment);
        } catch (err) {
            console.error('Image insertion failed:', err);
        }
    }

    syncEditorContent(editor);
}
window.insertImageIntoEditor = insertImageIntoEditor;

function syncEditorContent(editor) {
    const data = editor.getData();
    const contentInput = document.getElementById('content');
    if (contentInput) {
        contentInput.value = data;
    }
    const rawEditor = document.getElementById('rawHtmlEditor');
    if (rawEditor && document.activeElement !== rawEditor) {
        rawEditor.value = data;
    }
    updateLiveStats(data);
}

function updateLiveStats(text) {
    if (!text) text = '';
    const cleanText = text.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
    const words = cleanText.length > 0 ? cleanText.split(/\s+/).length : 0;
    const chars = cleanText.length;
    const readingTime = Math.max(1, Math.ceil(words / 200));

    const wordEl = document.getElementById('metricWordCount');
    const charEl = document.getElementById('metricCharCount');
    const readEl = document.getElementById('metricReadingTime');

    if (wordEl) wordEl.textContent = words.toLocaleString();
    if (charEl) charEl.textContent = chars.toLocaleString();
    if (readEl) readEl.textContent = `${readingTime} min read`;
}

function updateSerpPreview() {
    const titleInput = document.getElementById('title');
    const metaTitleInput = document.getElementById('meta_title');
    const defaultTitle = (metaTitleInput && metaTitleInput.value.trim()) || (titleInput && titleInput.value.trim()) || 'Article Title';
    const serpTitleEl = document.getElementById('serpPreviewTitle');
    if (serpTitleEl) {
        serpTitleEl.textContent = `${defaultTitle} — sejan.dev`;
    }

    const descInput = document.getElementById('meta_description');
    const excerptInput = document.getElementById('excerpt');
    const serpDescEl = document.getElementById('serpPreviewDesc');
    if (serpDescEl) {
        const descText = (descInput && descInput.value.trim()) || (excerptInput && excerptInput.value.trim()) || 'Detailed engineering breakdown, architectural overview, and technical implementation guide.';
        serpDescEl.textContent = descText;
    }

    const slugInput = document.getElementById('slug');
    const serpSlugEl = document.getElementById('serpPreviewSlug');
    if (serpSlugEl) {
        serpSlugEl.textContent = (slugInput && slugInput.value.trim()) || 'article-slug';
    }

    const titleCountEl = document.getElementById('metaTitleCount');
    if (titleCountEl && metaTitleInput) {
        titleCountEl.textContent = metaTitleInput.value.length;
    }
    const descCountEl = document.getElementById('metaDescCount');
    if (descCountEl && descInput) {
        descCountEl.textContent = descInput.value.length;
    }
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

/**
 * Initialize CKEditor on DOMContentLoaded safely
 */
document.addEventListener('DOMContentLoaded', () => {
    const editorEl = document.querySelector('#editorContent');
    const contentInput = document.getElementById('content');
    const rawEditor = document.getElementById('rawHtmlEditor');

    // Setup Raw HTML editor event listeners immediately
    if (rawEditor) {
        rawEditor.addEventListener('input', () => {
            const val = rawEditor.value;
            if (contentInput) {
                contentInput.value = val;
            }
            updateLiveStats(val);
        });

        rawEditor.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                this.value = this.value.substring(0, start) + '    ' + this.value.substring(end);
                this.selectionStart = this.selectionEnd = start + 4;
                this.dispatchEvent(new Event('input'));
            }
        });
    }

    // Initialize SERP preview listeners
    ['title', 'slug', 'excerpt', 'meta_title', 'meta_description'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updateSerpPreview);
        }
    });
    updateSerpPreview();

    if (!editorEl || typeof ClassicEditor === 'undefined') {
        return;
    }

    ClassicEditor
        .create(editorEl, {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'strikethrough',
                    'code',
                    '|',
                    'bulletedList',
                    'numberedList',
                    'blockQuote',
                    'codeBlock',
                    'insertTable',
                    '|',
                    'link',
                    'mediaEmbed',
                    'horizontalLine',
                    '|',
                    'undo',
                    'redo'
                ],
                shouldNotGroupWhenFull: true
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                ]
            },
            codeBlock: {
                languages: [
                    { language: 'plaintext', label: 'Plain text' },
                    { language: 'bash', label: 'Bash / Linux Terminal ($)' },
                    { language: 'error', label: 'Error Log / Stack Trace (✖)' },
                    { language: 'php', label: 'PHP / Laravel (🐘)' },
                    { language: 'javascript', label: 'JavaScript (⚡)' },
                    { language: 'typescript', label: 'TypeScript (TS)' },
                    { language: 'python', label: 'Python (🐍)' },
                    { language: 'dockerfile', label: 'Dockerfile (🐳)' },
                    { language: 'nginx', label: 'Nginx Config (🟢)' },
                    { language: 'sql', label: 'SQL Query (🗄️)' },
                    { language: 'html', label: 'HTML (🌐)' },
                    { language: 'css', label: 'CSS (🎨)' },
                    { language: 'json', label: 'JSON (📋)' },
                    { language: 'yaml', label: 'YAML (📄)' }
                ]
            }
        })
        .then(editor => {
            globalEditorInstance = editor;
            window.globalEditorInstance = editor;
            window.editorInstance = editor;

            editor.model.document.on('change:data', () => {
                syncEditorContent(editor);
            });

            if (contentInput && contentInput.value) {
                editor.setData(contentInput.value);
                if (rawEditor) {
                    rawEditor.value = contentInput.value;
                }
                updateLiveStats(contentInput.value);
            }
        })
        .catch(error => {
            console.error('CKEditor initialization error:', error);
        });
});
