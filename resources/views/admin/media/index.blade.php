@extends('layouts.app')

@section('title', 'Media Library — WordPress Style')

@section('layout')
<div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row text-slate-900">
    @include('admin.partials.sidebar')

    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <header class="p-4 sm:p-6 border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Media Manager
                </h1>
                <p class="text-xs text-slate-500">
                    Upload, organize, and inspect images and assets for your blog articles.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <label for="directUploadInput" class="cursor-pointer px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Upload New Media</span>
                </label>
                <form id="uploadForm" action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" id="directUploadInput" name="files[]" multiple accept="image/*,application/pdf" onchange="document.getElementById('uploadForm').submit();" />
                </form>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-6 w-full">
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-800 flex items-center gap-3">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Drag & Drop Upload Zone -->
            <div
                id="dropZone"
                ondrop="handleDrop(event)"
                ondragover="event.preventDefault(); this.classList.add('border-emerald-500', 'bg-emerald-50/50');"
                ondragleave="this.classList.remove('border-emerald-500', 'bg-emerald-50/50');"
                class="rounded-3xl border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-white p-8 text-center transition cursor-pointer shadow-xs"
                onclick="document.getElementById('directUploadInput').click();"
            >
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 border border-emerald-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>
                <div class="font-bold text-sm text-slate-900">Drag & drop files here, or click to browse</div>
                <p class="text-xs text-slate-500 mt-1">Supports PNG, JPG, WebP, GIF, SVG, and PDF (Max 20MB per file)</p>
            </div>

            <!-- Filter & Search Bar -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.media.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ empty($type) ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition">
                        All Media ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.media.index', ['type' => 'images']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $type === 'images' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition">
                        Images Only
                    </a>
                </div>

                <form action="{{ route('admin.media.index') }}" method="GET" class="relative max-w-xs w-full">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Search media..." class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 shadow-2xs" />
                    <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </form>
            </div>

            <!-- Media Grid -->
            @if ($media->isEmpty())
                <div class="p-12 text-center rounded-3xl bg-white border border-slate-200 space-y-3 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="font-bold text-slate-900 text-sm">No media files found</div>
                    <p class="text-xs text-slate-500">Upload your first asset above or import media from WordPress.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($media as $item)
                        <div
                            onclick="inspectMedia({{ json_encode($item) }})"
                            class="group relative rounded-2xl overflow-hidden aspect-square bg-white border border-slate-200 hover:border-emerald-500 cursor-pointer transition shadow-2xs"
                        >
                            @if ($item->is_image)
                                <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200" loading="lazy" />
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center bg-slate-50 text-slate-600">
                                    <svg class="w-8 h-8 text-emerald-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-[10px] font-mono truncate w-full">{{ $item->original_name }}</span>
                                </div>
                            @endif

                            <div class="absolute inset-x-0 bottom-0 p-2 bg-gradient-to-t from-slate-900/90 via-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition flex items-center justify-between text-[10px] text-white">
                                <span class="truncate font-mono">{{ $item->formatted_size }}</span>
                                <span class="text-emerald-400 font-bold">Inspect</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4">
                    {{ $media->links() }}
                </div>
            @endif
        </div>
    </main>
</div>

<!-- Media Details Inspector Modal -->
<div id="inspectorModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl space-y-6 relative overflow-hidden text-slate-900">
        <button onclick="closeInspector()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-start">
            <!-- Preview Box -->
            <div class="rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 aspect-square flex items-center justify-center">
                <img id="inspectorImage" src="" alt="" class="w-full h-full object-contain" />
            </div>

            <!-- Details & Metadata Editor -->
            <div class="space-y-4 text-xs">
                <div>
                    <h3 id="inspectorTitle" class="font-bold text-slate-900 text-sm truncate"></h3>
                    <div id="inspectorMeta" class="text-slate-500 font-mono mt-0.5"></div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Direct File URL</label>
                    <div class="flex items-center gap-1.5">
                        <input id="inspectorUrl" type="text" readonly class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono text-[11px] select-all" />
                        <button onclick="copyInspectorUrl()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold shrink-0 transition border border-slate-200">
                            Copy
                        </button>
                    </div>
                </div>

                <form id="inspectorUpdateForm" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Alt Text (Accessibility & SEO)</label>
                        <input id="inspectorAlt" type="text" name="alt_text" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:border-emerald-500" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Caption</label>
                        <textarea id="inspectorCaption" name="caption" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:border-emerald-500"></textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-between">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition">
                            Save Metadata
                        </button>
                    </div>
                </form>

                <form id="inspectorDeleteForm" method="POST" onsubmit="return confirm('Permanently delete this media file?');" class="pt-2 border-t border-slate-200">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium transition">
                        &times; Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function handleDrop(e) {
        e.preventDefault();
        const input = document.getElementById('directUploadInput');
        input.files = e.dataTransfer.files;
        document.getElementById('uploadForm').submit();
    }

    function inspectMedia(media) {
        document.getElementById('inspectorTitle').innerText = media.original_name;
        document.getElementById('inspectorMeta').innerText = `${media.mime_type} • ${media.formatted_size} ${media.width ? `• ${media.width}x${media.height}` : ''}`;
        document.getElementById('inspectorImage').src = media.url;
        document.getElementById('inspectorUrl').value = media.url;
        document.getElementById('inspectorAlt').value = media.alt_text || '';
        document.getElementById('inspectorCaption').value = media.caption || '';
        document.getElementById('inspectorUpdateForm').action = `/admin/media/${media.id}`;
        document.getElementById('inspectorDeleteForm').action = `/admin/media/${media.id}`;

        document.getElementById('inspectorModal').classList.remove('hidden');
    }

    function closeInspector() {
        document.getElementById('inspectorModal').classList.add('hidden');
    }

    function copyInspectorUrl() {
        const urlInput = document.getElementById('inspectorUrl');
        navigator.clipboard.writeText(urlInput.value);
        alert('Media URL copied to clipboard!');
    }
</script>
@endsection
