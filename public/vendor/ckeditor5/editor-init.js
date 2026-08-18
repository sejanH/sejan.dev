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
    if (!globalEditorInstance || !mediaItem) return;

    const imgAlt = mediaItem.alt_text ? ` alt="${mediaItem.alt_text}"` : '';
    const figCaption = mediaItem.caption ? `<figcaption class="text-xs text-center text-slate-400 mt-2">${mediaItem.caption}</figcaption>` : '';
    const imageHtml = `
        <figure class="my-6 rounded-2xl overflow-hidden">
            <img src="${mediaItem.url}"${imgAlt} class="w-full rounded-2xl shadow-lg" />
            ${figCaption}
        </figure>
    `;

    const viewFragment = globalEditorInstance.data.processor.toView(imageHtml);
    const modelFragment = globalEditorInstance.data.toModel(viewFragment);
    globalEditorInstance.model.insertContent(modelFragment);

    // Also update hidden form content input
    const contentInput = document.getElementById('content');
    if (contentInput) {
        contentInput.value = globalEditorInstance.getData();
    }
}

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

function clearFeaturedImage() {
    const input = document.getElementById('featured_image');
    if (input) input.value = '';
    updateFeaturedPreview('');
}
