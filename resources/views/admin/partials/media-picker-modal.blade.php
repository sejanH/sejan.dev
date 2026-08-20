<!-- Reusable WordPress-Style Media Picker Modal -->
<div id="mediaPickerModal" style="display: none;" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs items-center justify-center p-3 sm:p-6 text-slate-900" onclick="if(event.target === this) closeMediaPicker()">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-5xl w-full h-[86vh] flex flex-col shadow-2xl overflow-hidden relative" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/70 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Media Library Browser</h3>
                    <p class="text-[11px] text-slate-500">Select an existing asset or upload new media (Latest first)</p>
                </div>
            </div>

            <!-- Tabs: Library / Upload -->
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="switchMediaTab('library')"
                    id="tabBtnLibrary"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white transition shadow-2xs cursor-pointer"
                >
                    Media Library
                </button>
                <button
                    type="button"
                    onclick="switchMediaTab('upload')"
                    id="tabBtnUpload"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:text-slate-900 transition cursor-pointer"
                >
                    Upload Files
                </button>

                <button type="button" onclick="closeMediaPicker()" class="ml-2 text-slate-400 hover:text-slate-700 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body Content -->
        <div class="flex-1 overflow-hidden flex flex-col min-h-0">
            <!-- TAB 1: Library Grid -->
            <div id="mediaTabLibrary" class="flex-1 overflow-hidden flex flex-col min-h-0">
                <!-- Search & Filters Toolbar -->
                <div class="p-3.5 border-b border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="setPickerTypeFilter('images')"
                            id="pickerFilterImages"
                            class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white shadow-2xs transition cursor-pointer"
                        >
                            Images Only
                        </button>
                        <button
                            type="button"
                            onclick="setPickerTypeFilter('all')"
                            id="pickerFilterAll"
                            class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:text-slate-900 transition cursor-pointer"
                        >
                            All Files
                        </button>
                    </div>

                    <!-- Search Input & Pagination Header -->
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-64">
                            <input
                                type="text"
                                id="mediaPickerSearch"
                                oninput="debounceSearchMedia()"
                                placeholder="Search files..."
                                class="w-full pl-8 pr-4 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500"
                            />
                            <svg class="w-3.5 h-3.5 absolute left-2.5 top-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div id="mediaLoadingSpinner" class="text-xs text-slate-500 font-mono hidden flex items-center gap-1 shrink-0">
                            <svg class="animate-spin w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Scrollable Grid Area -->
                <div id="mediaPickerGridContainer" class="flex-1 p-4 overflow-y-auto min-h-0 bg-slate-50/50">
                    <div id="mediaPickerGrid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-7 gap-3">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>

                <!-- Modal Pagination Bar -->
                <div id="pickerPaginationBar" class="p-2.5 px-4 border-t border-slate-200 bg-white flex items-center justify-between text-xs shrink-0">
                    <div id="pickerPaginationInfo" class="text-slate-500 font-mono text-[11px]">
                        Loading items...
                    </div>
                    <div class="flex items-center gap-1.5" id="pickerPaginationButtons">
                        <button
                            type="button"
                            id="pickerBtnPrevPage"
                            onclick="changePickerPage(currentPickerPage - 1)"
                            disabled
                            class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition font-medium text-[11px] cursor-pointer"
                        >
                            &larr; Prev
                        </button>
                        <div id="pickerPageNumbers" class="flex items-center gap-1"></div>
                        <button
                            type="button"
                            id="pickerBtnNextPage"
                            onclick="changePickerPage(currentPickerPage + 1)"
                            disabled
                            class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition font-medium text-[11px] cursor-pointer"
                        >
                            Next &rarr;
                        </button>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Upload in Modal -->
            <div id="mediaTabUpload" class="flex-1 p-8 hidden flex flex-col items-center justify-center text-center bg-slate-50/50">
                <div
                    id="modalDropZone"
                    ondrop="handleModalDrop(event)"
                    ondragover="event.preventDefault(); this.classList.add('border-emerald-500', 'bg-emerald-50/50');"
                    ondragleave="this.classList.remove('border-emerald-500', 'bg-emerald-50/50');"
                    class="w-full max-w-md p-10 rounded-3xl border-2 border-dashed border-slate-300 bg-white flex flex-col items-center justify-center space-y-3 cursor-pointer hover:border-emerald-500 transition relative shadow-xs"
                    onclick="document.getElementById('modalFileInput').click();"
                >
                    <div id="modalUploadIconContainer" class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </div>
                    <div id="modalUploadText" class="font-bold text-slate-900 text-sm">Select files to upload</div>
                    <p id="modalUploadSubtext" class="text-xs text-slate-500">or drag and drop files directly here (PNG, JPG, WebP, GIF, SVG, PDF)</p>
                    <input type="file" id="modalFileInput" multiple accept="image/*,application/pdf" class="hidden" onchange="uploadModalFiles(this.files)" />
                </div>
            </div>
        </div>

        <!-- Footer with Selected Item Details & Confirm Button -->
        <div class="p-3 sm:p-4 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
            <!-- Selected Media Info -->
            <div id="mediaPickerSelectionInfo" class="flex items-center gap-3 min-w-0 flex-1">
                <div id="pickerSelectionThumb" class="w-10 h-10 rounded-xl overflow-hidden bg-slate-200 border border-slate-300 shrink-0 hidden flex items-center justify-center">
                    <img id="pickerFooterThumbImg" src="" alt="" class="w-full h-full object-cover" />
                </div>
                <div class="min-w-0">
                    <div id="pickerFooterFileName" class="text-xs font-bold text-slate-800 truncate">No image selected</div>
                    <div id="pickerFooterMeta" class="text-[10px] text-slate-500 font-mono">Click any image above to select (Double click to insert)</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5 shrink-0 justify-end">
                <button
                    type="button"
                    onclick="closeMediaPicker()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/60 transition cursor-pointer"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    id="btnConfirmMediaSelect"
                    disabled
                    onclick="confirmMediaSelection()"
                    class="px-5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white shadow-xs transition cursor-pointer flex items-center gap-1.5"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span id="pickerConfirmBtnText">Insert Selected</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPickerTarget = null;
    let selectedMediaItem = null;
    let mediaSearchTimeout = null;
    let currentPickerType = 'images'; // Default to images first
    let currentPickerPage = 1;
    let lastPickerPage = 1;
    let currentPickerQuery = '';

    function openMediaPicker(target) {
        currentPickerTarget = target;
        selectedMediaItem = null;
        currentPickerPage = 1;
        currentPickerQuery = '';
        const searchInput = document.getElementById('mediaPickerSearch');
        if (searchInput) searchInput.value = '';

        const confirmText = document.getElementById('pickerConfirmBtnText');
        if (confirmText) {
            confirmText.innerText = target === 'featured_image' ? 'Set Featured Image' : 'Insert into Article';
        }

        updateSelectionUi();
        switchMediaTab('library');
        loadMediaLibrary('', null, 1);
        
        const modal = document.getElementById('mediaPickerModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
        }
    }

    function closeMediaPicker() {
        const modal = document.getElementById('mediaPickerModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
        }
        currentPickerTarget = null;
        selectedMediaItem = null;
    }

    // Keyboard Escape to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('mediaPickerModal');
            if (modal && modal.style.display !== 'none' && !modal.classList.contains('hidden')) {
                closeMediaPicker();
            }
        }
    });

    function setPickerTypeFilter(type) {
        currentPickerType = type;
        currentPickerPage = 1;
        const btnImg = document.getElementById('pickerFilterImages');
        const btnAll = document.getElementById('pickerFilterAll');

        if (type === 'images') {
            btnImg.className = 'px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white shadow-2xs transition';
            btnAll.className = 'px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:text-slate-900 transition';
        } else {
            btnImg.className = 'px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:text-slate-900 transition';
            btnAll.className = 'px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white shadow-2xs transition';
        }

        const q = document.getElementById('mediaPickerSearch').value;
        loadMediaLibrary(q, null, 1);
    }

    function switchMediaTab(tab) {
        const tabLib = document.getElementById('mediaTabLibrary');
        const tabUpload = document.getElementById('mediaTabUpload');
        const btnLib = document.getElementById('tabBtnLibrary');
        const btnUpload = document.getElementById('tabBtnUpload');

        if (tab === 'library') {
            tabLib.classList.remove('hidden');
            tabUpload.classList.add('hidden');
            btnLib.className = 'px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white transition shadow-2xs';
            btnUpload.className = 'px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:text-slate-900 transition';
        } else {
            tabLib.classList.add('hidden');
            tabUpload.classList.remove('hidden');
            btnLib.className = 'px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:text-slate-900 transition';
            btnUpload.className = 'px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white shadow-2xs transition';
        }
    }

    function debounceSearchMedia() {
        clearTimeout(mediaSearchTimeout);
        mediaSearchTimeout = setTimeout(() => {
            currentPickerPage = 1;
            const q = document.getElementById('mediaPickerSearch').value;
            currentPickerQuery = q;
            loadMediaLibrary(q, null, 1);
        }, 250);
    }

    function changePickerPage(page) {
        if (page < 1 || page > lastPickerPage) return;
        currentPickerPage = page;
        loadMediaLibrary(currentPickerQuery, null, page);
        const container = document.getElementById('mediaPickerGridContainer');
        if (container) container.scrollTop = 0;
    }

    async function loadMediaLibrary(query = '', autoSelectId = null, page = 1) {
        const spinner = document.getElementById('mediaLoadingSpinner');
        const grid = document.getElementById('mediaPickerGrid');
        currentPickerQuery = query;
        currentPickerPage = page;

        if (spinner) spinner.classList.remove('hidden');

        try {
            const url = new URL('/admin/media', window.location.origin);
            url.searchParams.set('json', '1');
            url.searchParams.set('sort', 'latest'); // Always show newest first
            url.searchParams.set('page', page.toString());
            url.searchParams.set('per_page', '36');

            if (currentPickerType === 'images') {
                url.searchParams.set('type', 'images');
            }
            if (query) {
                url.searchParams.set('q', query);
            }

            const res = await fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();

            currentPickerPage = data.current_page || 1;
            lastPickerPage = data.last_page || 1;
            const totalItems = data.total || 0;

            renderPickerPagination(currentPickerPage, lastPickerPage, totalItems);

            grid.innerHTML = '';
            const items = data.data || [];
            if (items.length === 0) {
                grid.innerHTML = '<div class="col-span-full py-16 text-center text-slate-400 text-xs">No media files found</div>';
                return;
            }

            items.forEach((item) => {
                const el = document.createElement('div');
                const isSelected = (autoSelectId && item.id === autoSelectId) || (selectedMediaItem?.id === item.id);
                el.className = `group relative rounded-2xl overflow-hidden aspect-square bg-white border-2 ${
                    isSelected
                        ? 'border-emerald-500 ring-2 ring-emerald-500/30'
                        : 'border-slate-200 hover:border-emerald-400'
                } cursor-pointer transition shadow-2xs select-none`;
                
                el.onclick = () => selectMediaItem(item, el);
                el.ondblclick = () => {
                    selectMediaItem(item, el);
                    confirmMediaSelection();
                };

                const ext = item.mime_type ? item.mime_type.split('/')[1] : 'file';

                if (item.is_image) {
                    el.innerHTML = `
                        <div class="w-full h-full bg-slate-100">
                            <img src="${item.url}" alt="${item.alt_text || ''}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200" loading="lazy" />
                        </div>
                        <div class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded-md bg-slate-900/80 text-white text-[8px] font-mono font-bold uppercase backdrop-blur-xs">
                            ${ext}
                        </div>
                        <div class="check-badge absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-emerald-600 text-white items-center justify-center shadow-xs ${isSelected ? 'flex' : 'hidden'}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="absolute inset-x-0 bottom-0 p-1.5 bg-gradient-to-t from-slate-950/80 to-transparent opacity-0 group-hover:opacity-100 transition text-[9px] text-white truncate font-medium">
                            ${item.original_name}
                        </div>
                    `;
                } else {
                    el.innerHTML = `
                        <div class="w-full h-full flex flex-col items-center justify-center p-2 text-center bg-slate-50 text-slate-500">
                            <svg class="w-6 h-6 text-emerald-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="truncate w-full font-mono text-[9px] font-semibold">${item.original_name}</span>
                        </div>
                        <div class="check-badge absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-emerald-600 text-white items-center justify-center shadow-xs ${isSelected ? 'flex' : 'hidden'}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    `;
                }

                grid.appendChild(el);

                if (isSelected) {
                    selectMediaItem(item, el);
                }
            });
        } catch (err) {
            console.error('Failed to load media list', err);
            grid.innerHTML = '<div class="col-span-full py-12 text-center text-rose-500 text-xs">Failed to load media library.</div>';
        } finally {
            if (spinner) spinner.classList.add('hidden');
        }
    }

    function renderPickerPagination(currentPage, totalPages, totalCount) {
        const infoEl = document.getElementById('pickerPaginationInfo');
        const prevBtn = document.getElementById('pickerBtnPrevPage');
        const nextBtn = document.getElementById('pickerBtnNextPage');
        const pagesContainer = document.getElementById('pickerPageNumbers');

        if (infoEl) {
            infoEl.innerText = `Page ${currentPage} of ${totalPages} (${totalCount} files)`;
        }

        if (prevBtn) {
            prevBtn.disabled = currentPage <= 1;
        }

        if (nextBtn) {
            nextBtn.disabled = currentPage >= totalPages;
        }

        if (pagesContainer) {
            pagesContainer.innerHTML = '';
            if (totalPages <= 1) return;

            const maxVisible = 5;
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `w-6 h-6 rounded-lg text-[11px] font-medium transition ${
                    i === currentPage
                        ? 'bg-emerald-600 text-white font-bold shadow-2xs'
                        : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100'
                }`;
                btn.innerText = i;
                btn.onclick = () => changePickerPage(i);
                pagesContainer.appendChild(btn);
            }
        }
    }

    function selectMediaItem(item, element) {
        selectedMediaItem = Object.assign({}, item);

        document.querySelectorAll('#mediaPickerGrid > div').forEach(div => {
            div.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500/30');
            div.classList.add('border-slate-200');
            const badge = div.querySelector('.check-badge');
            if (badge) badge.classList.add('hidden');
        });

        if (element) {
            element.classList.remove('border-slate-200');
            element.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-500/30');
            const badge = element.querySelector('.check-badge');
            if (badge) badge.classList.remove('hidden');
            if (badge) badge.classList.add('flex');
        }

        updateSelectionUi();
    }

    function updateSelectionUi() {
        const btn = document.getElementById('btnConfirmMediaSelect');
        const thumbContainer = document.getElementById('pickerSelectionThumb');
        const thumbImg = document.getElementById('pickerFooterThumbImg');
        const nameEl = document.getElementById('pickerFooterFileName');
        const metaEl = document.getElementById('pickerFooterMeta');

        if (selectedMediaItem) {
            btn.disabled = false;
            nameEl.innerText = selectedMediaItem.original_name || selectedMediaItem.filename;
            
            let metaText = `${selectedMediaItem.formatted_size || ''}`;
            if (selectedMediaItem.width && selectedMediaItem.height) {
                metaText += ` • ${selectedMediaItem.width} × ${selectedMediaItem.height} px`;
            }
            if (selectedMediaItem.mime_type) {
                metaText += ` • ${selectedMediaItem.mime_type}`;
            }
            metaEl.innerText = metaText;

            if (selectedMediaItem.is_image) {
                thumbImg.src = selectedMediaItem.url;
                thumbContainer.classList.remove('hidden');
            } else {
                thumbContainer.classList.add('hidden');
            }
        } else {
            btn.disabled = true;
            nameEl.innerText = 'No image selected';
            metaEl.innerText = 'Click any image above to select (Double click to insert)';
            thumbContainer.classList.add('hidden');
        }
    }

    function updateFeaturedPreview(url) {
        const container = document.getElementById('featuredImagePreview');
        const img = document.getElementById('featuredImagePreviewImg');
        if (!container || !img) return;

        if (url && url.trim().length > 0) {
            img.src = url.trim();
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function confirmMediaSelection() {
        if (!selectedMediaItem) return;

        if (currentPickerTarget === 'featured_image') {
            const input = document.getElementById('featured_image');
            if (input) {
                input.value = selectedMediaItem.url;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                updateFeaturedPreview(selectedMediaItem.url);
            }
        } else if (currentPickerTarget === 'editor') {
            if (typeof window.insertImageIntoEditor === 'function') {
                window.insertImageIntoEditor(selectedMediaItem);
            } else if (window.editorInstance || window.globalEditorInstance) {
                const editor = window.editorInstance || window.globalEditorInstance;
                editor.model.change(writer => {
                    const imageElement = writer.createElement('imageBlock', {
                        src: selectedMediaItem.url,
                        alt: selectedMediaItem.alt_text || selectedMediaItem.original_name
                    });
                    editor.model.insertContent(imageElement, editor.model.document.selection);
                });
            }
        }

        closeMediaPicker();
    }

    function handleModalDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        const dropZone = document.getElementById('modalDropZone');
        if (dropZone) dropZone.classList.remove('border-emerald-500', 'bg-emerald-50/50');
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            uploadModalFiles(e.dataTransfer.files);
        }
    }

    async function uploadModalFiles(files) {
        if (!files || files.length === 0) return;

        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const textEl = document.getElementById('modalUploadText');
        const subtextEl = document.getElementById('modalUploadSubtext');
        const origText = textEl ? textEl.innerText : '';
        const origSubtext = subtextEl ? subtextEl.innerText : '';

        if (textEl) textEl.innerText = 'Uploading files...';
        if (subtextEl) subtextEl.innerText = 'Please wait while your media is stored';

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

            if (res.ok && data.success) {
                switchMediaTab('library');
                const uploadedItem = (data.files && data.files.length > 0) ? data.files[0] : null;
                await loadMediaLibrary('', uploadedItem ? uploadedItem.id : null, 1);
                if (uploadedItem) {
                    selectedMediaItem = uploadedItem;
                    updateSelectionUi();
                }
            } else {
                alert(data.error || data.message || 'File upload failed. Check format and size limits.');
            }
        } catch (err) {
            console.error(err);
            alert('Upload request failed.');
        } finally {
            if (textEl) textEl.innerText = origText;
            if (subtextEl) subtextEl.innerText = origSubtext;
            const input = document.getElementById('modalFileInput');
            if (input) input.value = '';
        }
    }
</script>


