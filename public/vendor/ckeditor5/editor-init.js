let globalEditorInstance = null;

function updateLiveStats(text) {
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
    const titleInput = document.getElementById('meta_title');
    const defaultTitle = document.getElementById('title')?.value || 'Article Title';
    const serpTitleEl = document.getElementById('serpPreviewTitle');
    if (serpTitleEl) {
        serpTitleEl.textContent = titleInput?.value?.trim() || `${defaultTitle} — sejan.dev`;
    }

    const descInput = document.getElementById('meta_description');
    const excerptInput = document.getElementById('excerpt');
    const serpDescEl = document.getElementById('serpPreviewDesc');
    if (serpDescEl) {
        const descText = descInput?.value?.trim() || excerptInput?.value?.trim() || 'Detailed engineering breakdown, architectural overview, and technical implementation guide.';
        serpDescEl.textContent = descText;
    }

    const slugInput = document.getElementById('slug');
    const serpSlugEl = document.getElementById('serpPreviewSlug');
    if (serpSlugEl) {
        serpSlugEl.textContent = slugInput?.value?.trim() || 'article-slug';
    }

    // Char counters
    const titleCountEl = document.getElementById('metaTitleCount');
    if (titleCountEl && titleInput) {
        titleCountEl.textContent = titleInput.value.length;
    }
    const descCountEl = document.getElementById('metaDescCount');
    if (descCountEl && descInput) {
        descCountEl.textContent = descInput.value.length;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const editorEl = document.querySelector('#editorContent');
    if (!editorEl) return;

    ClassicEditor
        .create(editorEl, {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'underline',
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
            }
        })
        .then(editor => {
            globalEditorInstance = editor;
            window.globalEditorInstance = editor;
            window.editorInstance = editor;

            // Sync on change to hidden content input & live stats
            editor.model.document.on('change:data', () => {
                const data = editor.getData();
                const contentInput = document.getElementById('content');
                if (contentInput) {
                    contentInput.value = data;
                }
                updateLiveStats(data);
            });

            // Set initial data
            const contentInput = document.getElementById('content');
            if (contentInput && contentInput.value) {
                editor.setData(contentInput.value);
                updateLiveStats(contentInput.value);
            }

            // Initial SERP preview
            updateSerpPreview();
        })
        .catch(error => {
            console.error('CKEditor initialization error:', error);
        });

    // Wire listeners for live SERP preview
    ['title', 'slug', 'excerpt', 'meta_title', 'meta_description'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updateSerpPreview);
        }
    });
});

/**
 * Global function called by the Media Picker modal to insert an image into CKEditor.
 */
function insertImageIntoEditor(mediaItem) {
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

    // Also update hidden form content input
    const contentInput = document.getElementById('content');
    if (contentInput) {
        contentInput.value = editor.getData();
    }
}
window.insertImageIntoEditor = insertImageIntoEditor;

/**
 * Insert formatted snippet helpers into CKEditor.
 */
function insertSnippetIntoEditor(type) {
    if (!globalEditorInstance) return;

    let html = '';
    if (type === 'note') {
        html = `<blockquote><p><strong>Note:</strong> Enter important context or implementation details here.</p></blockquote>`;
    } else if (type === 'tip') {
        html = `<blockquote><p><strong>Tip:</strong> Pro-tip or performance optimization recommendation.</p></blockquote>`;
    } else if (type === 'warning') {
        html = `<blockquote><p><strong>Warning:</strong> Potential pitfall or breaking change warning.</p></blockquote>`;
    } else if (type === 'code') {
        html = `<pre><code class="language-php">// PHP / Laravel snippet\n$result = Post::where('status', 'published')->get();</code></pre>`;
    }

    if (html) {
        const viewFragment = globalEditorInstance.data.processor.toView(html);
        const modelFragment = globalEditorInstance.data.toModel(viewFragment);
        globalEditorInstance.model.insertContent(modelFragment);
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
