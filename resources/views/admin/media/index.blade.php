@extends('layouts.app')

@section('title', 'Media Manager — Assets & Library')

@section('layout')
<div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row text-slate-900">
    @include('admin.partials.sidebar')

    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Sticky Header -->
        <header class="p-4 sm:p-6 border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                            Media Manager
                        </h1>
                        <p class="text-xs text-slate-500">
                            Upload, organize, and manage images and assets for your blog articles.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="button"
                    onclick="document.getElementById('fileUploadInput').click();"
                    class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Upload Media</span>
                </button>
                <input
                    type="file"
                    id="fileUploadInput"
                    multiple
                    accept="image/*,application/pdf,application/zip"
                    class="hidden"
                    onchange="handleFilesSelected(this.files)"
                />
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-6 w-full max-w-7xl mx-auto">
            @if (session('status'))
                <div id="flashSessionAlert" class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-800 flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                    <button onclick="document.getElementById('flashSessionAlert').remove();" class="text-emerald-600 hover:text-emerald-900">&times;</button>
                </div>
            @endif

            <!-- KPI Stats Summary Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total Assets</div>
                        <div class="text-lg sm:text-xl font-extrabold text-slate-900 font-mono" id="statTotalCount">{{ $totalCount }}</div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Images</div>
                        <div class="text-lg sm:text-xl font-extrabold text-slate-900 font-mono" id="statImagesCount">{{ $imagesCount }}</div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Documents</div>
                        <div class="text-lg sm:text-xl font-extrabold text-slate-900 font-mono" id="statDocsCount">{{ $documentsCount }}</div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Storage Used</div>
                        <div class="text-lg sm:text-xl font-extrabold text-slate-900 font-mono">{{ $totalSizeFormatted }}</div>
                    </div>
                </div>
            </div>

            <!-- Drag & Drop Upload Zone -->
            <div
                id="dropZone"
                ondrop="handleDrop(event)"
                ondragover="handleDragOver(event)"
                ondragleave="handleDragLeave(event)"
                class="rounded-3xl border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-white p-6 sm:p-8 text-center transition cursor-pointer shadow-xs relative overflow-hidden group"
                onclick="document.getElementById('fileUploadInput').click();"
            >
                <div id="dropZoneContent" class="space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:scale-110 flex items-center justify-center mx-auto border border-emerald-100 transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                    <div class="font-bold text-sm text-slate-900">
                        Drag & drop images or files here, or <span class="text-emerald-600 underline">browse files</span>
                    </div>
                    <p class="text-xs text-slate-500">
                        Supports WebP, PNG, JPG, GIF, SVG, and PDF (Max 20MB per file) &bull; <strong class="text-emerald-700">Latest images will appear first</strong>
                    </p>
                </div>

                <!-- Live Upload Progress Overlay -->
                <div id="uploadProgressOverlay" class="hidden absolute inset-0 bg-white/95 backdrop-blur-xs flex flex-col items-center justify-center p-6 space-y-3 z-10">
                    <div class="w-10 h-10 rounded-full border-3 border-emerald-200 border-t-emerald-600 animate-spin"></div>
                    <div id="uploadProgressText" class="text-xs font-bold text-slate-800">Uploading media files...</div>
                    <div class="w-full max-w-xs bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div id="uploadProgressBar" class="bg-emerald-500 h-full rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Filter, Search, Sort & View Controls -->
            <div class="glass-panel p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Filter Pills -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <a
                        href="{{ route('admin.media.index', array_merge(request()->except('type', 'page'), [])) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ empty($type) ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600' }} transition"
                    >
                        All Media ({{ $totalCount }})
                    </a>
                    <a
                        href="{{ route('admin.media.index', array_merge(request()->except('type', 'page'), ['type' => 'images'])) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $type === 'images' || $type === 'image' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600' }} transition flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Images Only ({{ $imagesCount }})</span>
                    </a>
                    <a
                        href="{{ route('admin.media.index', array_merge(request()->except('type', 'page'), ['type' => 'documents'])) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $type === 'documents' || $type === 'document' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600' }} transition"
                    >
                        Documents ({{ $documentsCount }})
                    </a>
                </div>

                <!-- Right Side: Search, Sort Dropdown & View Mode Switcher -->
                <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap">
                    <!-- Search Input -->
                    <form action="{{ route('admin.media.index') }}" method="GET" class="relative flex-1 sm:w-64">
                        @if ($type)
                            <input type="hidden" name="type" value="{{ $type }}" />
                        @endif
                        @if ($sort && $sort !== 'latest')
                            <input type="hidden" name="sort" value="{{ $sort }}" />
                        @endif
                        <input
                            type="text"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Search by name, alt, caption..."
                            class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition"
                        />
                        <svg class="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        @if ($search)
                            <a
                                href="{{ route('admin.media.index', request()->except('q', 'page')) }}"
                                class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-700 text-xs"
                                title="Clear Search"
                            >&times;</a>
                        @endif
                    </form>

                    <!-- Sort Dropdown -->
                    <form action="{{ route('admin.media.index') }}" method="GET" id="sortForm" class="shrink-0">
                        @if ($type)
                            <input type="hidden" name="type" value="{{ $type }}" />
                        @endif
                        @if ($search)
                            <input type="hidden" name="q" value="{{ $search }}" />
                        @endif
                        <select
                            name="sort"
                            onchange="this.form.submit()"
                            class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-emerald-500 font-medium cursor-pointer"
                        >
                            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Newest Uploads First</option>
                            <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest Uploads First</option>
                            <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name (A to Z)</option>
                            <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name (Z to A)</option>
                            <option value="size_desc" {{ $sort === 'size_desc' ? 'selected' : '' }}>Largest Size</option>
                            <option value="size_asc" {{ $sort === 'size_asc' ? 'selected' : '' }}>Smallest Size</option>
                        </select>
                    </form>

                    <!-- View Toggle: Grid vs List -->
                    <div class="flex items-center bg-slate-100 p-0.5 rounded-xl border border-slate-200 shrink-0">
                        <button
                            type="button"
                            onclick="toggleViewMode('grid')"
                            id="viewBtnGrid"
                            title="Grid View"
                            class="p-1.5 rounded-lg text-slate-800 bg-white shadow-2xs transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            onclick="toggleViewMode('list')"
                            id="viewBtnList"
                            title="List View"
                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Media Content Container -->
            @if ($media->isEmpty())
                <div class="p-12 text-center rounded-3xl bg-white border border-slate-200 space-y-3 shadow-xs">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto border border-emerald-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="font-bold text-slate-900 text-sm">No media files found</div>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        @if ($search || $type)
                            No assets match your search criteria. <a href="{{ route('admin.media.index') }}" class="text-emerald-600 font-semibold underline">Reset filters</a>
                        @else
                            Upload your first media asset above or drag and drop images onto this page.
                        @endif
                    </p>
                </div>
            @else
                <!-- 1. GRID VIEW -->
                <div id="mediaGridView" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($media as $item)
                        <div
                            id="media-card-{{ $item->id }}"
                            class="media-card group relative rounded-2xl overflow-hidden aspect-square bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-md transition duration-200 cursor-pointer flex flex-col justify-between"
                            onclick="inspectMediaById({{ $item->id }})"
                        >
                            <!-- Image or Document Display -->
                            <div class="w-full h-full relative overflow-hidden bg-slate-100 flex items-center justify-center">
                                @if ($item->is_image)
                                    <img
                                        src="{{ $item->url }}"
                                        alt="{{ $item->alt_text }}"
                                        loading="lazy"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                    />
                                @else
                                    <div class="flex flex-col items-center justify-center p-3 text-center text-slate-500">
                                        <svg class="w-10 h-10 text-emerald-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-[10px] font-mono font-medium truncate w-full text-slate-700">{{ $item->original_name }}</span>
                                    </div>
                                @endif

                                <!-- Top Badges -->
                                <div class="absolute top-2 left-2 flex items-center gap-1 z-10">
                                    <span class="px-1.5 py-0.5 rounded-md bg-slate-900/80 backdrop-blur-xs text-white text-[9px] font-mono uppercase font-semibold">
                                        {{ pathinfo($item->filename, PATHINFO_EXTENSION) ?: 'file' }}
                                    </span>
                                    @if ($item->width && $item->height)
                                        <span class="px-1.5 py-0.5 rounded-md bg-slate-900/80 backdrop-blur-xs text-slate-200 text-[9px] font-mono hidden sm:inline-block">
                                            {{ $item->width }}&times;{{ $item->height }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Hover Overlay with Metadata & Quick Actions -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition duration-200 p-3 flex flex-col justify-between text-white z-10 pointer-events-none group-hover:pointer-events-auto">
                                <div class="flex justify-end gap-1.5">
                                    <button
                                        type="button"
                                        onclick="event.stopPropagation(); copyDirectUrl('{{ $item->url }}')"
                                        title="Copy Direct Link"
                                        class="p-1.5 rounded-lg bg-white/20 hover:bg-white text-white hover:text-slate-900 transition backdrop-blur-xs"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="space-y-1">
                                    <div class="font-bold text-[11px] truncate" title="{{ $item->original_name }}">
                                        {{ $item->original_name }}
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] text-slate-300 font-mono">
                                        <span>{{ $item->formatted_size }}</span>
                                        <span>{{ $item->created_at_human }}</span>
                                    </div>
                                    <div class="pt-1 flex items-center justify-between">
                                        <span class="text-emerald-400 text-[10px] font-bold">Click to Inspect &rarr;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 2. LIST / TABLE VIEW (Initially Hidden) -->
                <div id="mediaListView" class="hidden bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-2xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                                <tr>
                                    <th class="p-3.5 w-16">Preview</th>
                                    <th class="p-3.5">Asset Name</th>
                                    <th class="p-3.5">Alt Text / Caption</th>
                                    <th class="p-3.5">Dimensions</th>
                                    <th class="p-3.5">Size</th>
                                    <th class="p-3.5">Uploaded</th>
                                    <th class="p-3.5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-sans">
                                @foreach ($media as $item)
                                    <tr class="hover:bg-slate-50/80 transition cursor-pointer" onclick="inspectMediaById({{ $item->id }})">
                                        <td class="p-3.5">
                                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 flex items-center justify-center">
                                                @if ($item->is_image)
                                                    <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover" loading="lazy" />
                                                @else
                                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="p-3.5">
                                            <div class="font-bold text-slate-900 text-xs">{{ $item->original_name }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $item->mime_type }}</div>
                                        </td>
                                        <td class="p-3.5 max-w-xs truncate text-slate-600">
                                            {{ $item->alt_text ?: ($item->caption ?: '—') }}
                                        </td>
                                        <td class="p-3.5 font-mono text-slate-600">
                                            {{ $item->width ? "{$item->width} × {$item->height}" : '—' }}
                                        </td>
                                        <td class="p-3.5 font-mono text-slate-600 whitespace-nowrap">
                                            {{ $item->formatted_size }}
                                        </td>
                                        <td class="p-3.5 text-slate-500 whitespace-nowrap">
                                            <div>{{ $item->created_at_human }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $item->created_at_formatted }}</div>
                                        </td>
                                        <td class="p-3.5 text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button
                                                    type="button"
                                                    onclick="copyDirectUrl('{{ $item->url }}')"
                                                    class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                                    title="Copy URL"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    type="button"
                                                    onclick="inspectMediaById({{ $item->id }})"
                                                    class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold transition"
                                                    title="Inspect Asset"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div class="pt-2">
                    {{ $media->links() }}
                </div>
            @endif
        </div>
    </main>
</div>

<!-- ========================================== -->
<!-- ENHANCED MEDIA DETAILS INSPECTOR MODAL     -->
<!-- ========================================== -->
<div
    id="inspectorModal"
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-6"
    onclick="handleModalBackdropClick(event)"
>
    <div class="bg-white border border-slate-200 rounded-3xl max-w-4xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative text-slate-900">
        <!-- Modal Top Bar -->
        <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="navigateInspector(-1)"
                    id="btnInspectorPrev"
                    class="p-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Previous Asset (Left Arrow)"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    type="button"
                    onclick="navigateInspector(1)"
                    id="btnInspectorNext"
                    class="p-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Next Asset (Right Arrow)"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <span id="inspectorIndexCount" class="text-[11px] text-slate-500 font-mono ml-2"></span>
            </div>

            <div class="flex items-center gap-2">
                <a
                    id="inspectorOpenNewTab"
                    href="#"
                    target="_blank"
                    class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-slate-900 text-xs font-semibold flex items-center gap-1.5 transition shadow-2xs"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>View Full Size</span>
                </a>
                <button
                    type="button"
                    onclick="closeInspector()"
                    class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body Grid -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
            <!-- Left Preview Column (7 cols) -->
            <div class="md:col-span-7 flex flex-col space-y-3">
                <div class="rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 aspect-[4/3] sm:aspect-square flex items-center justify-center relative group select-none shadow-2xs">
                    <img
                        id="inspectorImage"
                        src=""
                        alt=""
                        class="w-full h-full object-contain p-2"
                    />
                    <div id="inspectorDocPlaceholder" class="hidden flex flex-col items-center justify-center p-6 text-center text-slate-600">
                        <svg class="w-16 h-16 text-emerald-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span id="inspectorDocName" class="text-xs font-mono font-medium truncate max-w-xs"></span>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-500 font-mono px-1">
                    <span id="inspectorDimensions"></span>
                    <span id="inspectorFileSize"></span>
                </div>
            </div>

            <!-- Right Metadata & Editor Column (5 cols) -->
            <div class="md:col-span-5 space-y-5 text-xs">
                <div>
                    <h3 id="inspectorTitle" class="font-bold text-slate-900 text-sm truncate" title=""></h3>
                    <div id="inspectorMeta" class="text-slate-500 font-mono mt-0.5 text-[11px]"></div>
                </div>

                <!-- Direct URL Box with 1-click copy -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Direct File URL</label>
                    <div class="flex items-center gap-1.5">
                        <input
                            id="inspectorUrl"
                            type="text"
                            readonly
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono text-[11px] select-all focus:outline-none"
                        />
                        <button
                            type="button"
                            onclick="copyInspectorUrl()"
                            id="btnCopyInspectorUrl"
                            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold shrink-0 transition border border-slate-200 flex items-center gap-1 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span>Copy</span>
                        </button>
                    </div>
                </div>

                <!-- Live Metadata Update Form -->
                <form id="inspectorUpdateForm" onsubmit="saveInspectorMetadata(event)" class="space-y-3.5">
                    @csrf
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600">Alt Text</label>
                            <span class="text-[10px] text-slate-400">SEO & Accessibility</span>
                        </div>
                        <input
                            id="inspectorAlt"
                            type="text"
                            name="alt_text"
                            placeholder="Descriptive image alternative text"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:border-emerald-500 transition text-xs"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Caption</label>
                        <textarea
                            id="inspectorCaption"
                            name="caption"
                            rows="2"
                            placeholder="Optional caption displayed under image in articles"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:border-emerald-500 transition text-xs"
                        ></textarea>
                    </div>

                    <div class="pt-1 flex items-center justify-between">
                        <button
                            type="submit"
                            id="btnSaveInspector"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition flex items-center gap-1.5 shadow-2xs cursor-pointer"
                        >
                            <span>Save Metadata</span>
                        </button>
                        <span id="inspectorSavedIndicator" class="text-xs text-emerald-600 font-semibold hidden flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Saved</span>
                        </span>
                    </div>
                </form>

                <!-- Delete Action -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button
                        type="button"
                        onclick="deleteInspectorMedia()"
                        class="text-rose-600 hover:text-rose-800 text-xs font-semibold flex items-center gap-1 transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Delete Asset Permanently</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Toast Container -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

<script>
    // All loaded media array for seamless inspector browsing
    const mediaItemsList = @json($media->items());
    let currentInspectorIndex = -1;

    // View mode toggle
    function toggleViewMode(mode) {
        const gridEl = document.getElementById('mediaGridView');
        const listEl = document.getElementById('mediaListView');
        const btnGrid = document.getElementById('viewBtnGrid');
        const btnList = document.getElementById('viewBtnList');

        if (!gridEl || !listEl) return;

        if (mode === 'list') {
            gridEl.classList.add('hidden');
            listEl.classList.remove('hidden');
            btnList.className = 'p-1.5 rounded-lg text-slate-800 bg-white shadow-2xs transition';
            btnGrid.className = 'p-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition';
            localStorage.setItem('admin_media_view_mode', 'list');
        } else {
            gridEl.classList.remove('hidden');
            listEl.classList.add('hidden');
            btnGrid.className = 'p-1.5 rounded-lg text-slate-800 bg-white shadow-2xs transition';
            btnList.className = 'p-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition';
            localStorage.setItem('admin_media_view_mode', 'grid');
        }
    }

    // Initialize saved view mode
    document.addEventListener('DOMContentLoaded', () => {
        const savedView = localStorage.getItem('admin_media_view_mode');
        if (savedView === 'list') {
            toggleViewMode('list');
        }
    });

    // Drag & Drop Upload Handlers
    function handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        const dropZone = document.getElementById('dropZone');
        dropZone.classList.add('border-emerald-500', 'bg-emerald-50/50', 'ring-4', 'ring-emerald-500/10');
    }

    function handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        const dropZone = document.getElementById('dropZone');
        dropZone.classList.remove('border-emerald-500', 'bg-emerald-50/50', 'ring-4', 'ring-emerald-500/10');
    }

    function handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        handleDragLeave(e);
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            uploadMediaFiles(e.dataTransfer.files);
        }
    }

    function handleFilesSelected(files) {
        if (files && files.length > 0) {
            uploadMediaFiles(files);
        }
    }

    async function uploadMediaFiles(files) {
        if (!files || files.length === 0) return;

        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        const overlay = document.getElementById('uploadProgressOverlay');
        const progressBar = document.getElementById('uploadProgressBar');
        const progressText = document.getElementById('uploadProgressText');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (overlay) overlay.classList.remove('hidden');
        if (progressBar) progressBar.style.width = '20%';
        if (progressText) progressText.innerText = `Uploading ${files.length} file(s)...`;

        try {
            if (progressBar) progressBar.style.width = '60%';

            const res = await fetch('{{ route('admin.media.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (progressBar) progressBar.style.width = '95%';
            const data = await res.json();

            if (res.ok && data.success) {
                if (progressBar) progressBar.style.width = '100%';
                showToast(data.message || 'Media files uploaded successfully!', 'success');
                // Reload page to show latest images ordered first in fresh server view
                setTimeout(() => {
                    window.location.reload();
                }, 600);
            } else {
                showToast(data.error || data.message || 'Upload failed. Check file formats and size limit.', 'error');
            }
        } catch (err) {
            console.error('Upload Error:', err);
            showToast('An error occurred during file upload.', 'error');
        } finally {
            setTimeout(() => {
                if (overlay) overlay.classList.add('hidden');
                if (progressBar) progressBar.style.width = '0%';
                const input = document.getElementById('fileUploadInput');
                if (input) input.value = '';
            }, 600);
        }
    }

    // Modal Inspector Functions
    function inspectMediaById(id) {
        const idx = mediaItemsList.findIndex(item => item.id === id);
        if (idx !== -1) {
            currentInspectorIndex = idx;
            renderInspectorModal(mediaItemsList[idx]);
        }
    }

    function renderInspectorModal(media) {
        if (!media) return;

        document.getElementById('inspectorTitle').innerText = media.original_name;
        document.getElementById('inspectorTitle').title = media.original_name;
        document.getElementById('inspectorMeta').innerText = `${media.mime_type} • ${media.formatted_size} • Uploaded ${media.created_at_human || ''}`;

        const imgEl = document.getElementById('inspectorImage');
        const docEl = document.getElementById('inspectorDocPlaceholder');
        const docName = document.getElementById('inspectorDocName');

        if (media.is_image) {
            imgEl.src = media.url;
            imgEl.alt = media.alt_text || '';
            imgEl.classList.remove('hidden');
            docEl.classList.add('hidden');
        } else {
            imgEl.classList.add('hidden');
            docEl.classList.remove('hidden');
            docName.innerText = media.original_name;
        }

        document.getElementById('inspectorDimensions').innerText = media.width ? `${media.width} × ${media.height} px` : 'Document';
        document.getElementById('inspectorFileSize').innerText = media.formatted_size;
        document.getElementById('inspectorUrl').value = media.url;
        document.getElementById('inspectorAlt').value = media.alt_text || '';
        document.getElementById('inspectorCaption').value = media.caption || '';
        document.getElementById('inspectorOpenNewTab').href = media.url;

        // Nav counts
        document.getElementById('inspectorIndexCount').innerText = `${currentInspectorIndex + 1} of ${mediaItemsList.length}`;
        document.getElementById('btnInspectorPrev').disabled = currentInspectorIndex <= 0;
        document.getElementById('btnInspectorNext').disabled = currentInspectorIndex >= mediaItemsList.length - 1;

        // Hide saved indicator
        document.getElementById('inspectorSavedIndicator').classList.add('hidden');

        document.getElementById('inspectorModal').classList.remove('hidden');
    }

    function navigateInspector(direction) {
        const newIndex = currentInspectorIndex + direction;
        if (newIndex >= 0 && newIndex < mediaItemsList.length) {
            currentInspectorIndex = newIndex;
            renderInspectorModal(mediaItemsList[currentInspectorIndex]);
        }
    }

    function closeInspector() {
        document.getElementById('inspectorModal').classList.add('hidden');
    }

    function handleModalBackdropClick(e) {
        if (e.target.id === 'inspectorModal') {
            closeInspector();
        }
    }

    // Keyboard navigation in inspector modal
    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('inspectorModal');
        if (modal && !modal.classList.contains('hidden')) {
            if (e.key === 'Escape') {
                closeInspector();
            } else if (e.key === 'ArrowLeft') {
                navigateInspector(-1);
            } else if (e.key === 'ArrowRight') {
                navigateInspector(1);
            }
        }
    });

    // Copy direct link with toast
    function copyDirectUrl(url) {
        if (!url) return;
        navigator.clipboard.writeText(url).then(() => {
            showToast('Direct URL copied to clipboard!', 'success');
        }).catch(() => {
            prompt('Copy URL:', url);
        });
    }

    function copyInspectorUrl() {
        const urlInput = document.getElementById('inspectorUrl');
        copyDirectUrl(urlInput.value);
    }

    // AJAX Save Metadata in Inspector
    async function saveInspectorMetadata(e) {
        e.preventDefault();
        const media = mediaItemsList[currentInspectorIndex];
        if (!media) return;

        const altText = document.getElementById('inspectorAlt').value;
        const caption = document.getElementById('inspectorCaption').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const btnSave = document.getElementById('btnSaveInspector');
        const savedIndicator = document.getElementById('inspectorSavedIndicator');

        btnSave.disabled = true;
        btnSave.innerText = 'Saving...';

        try {
            const res = await fetch(`/admin/media/${media.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    alt_text: altText,
                    caption: caption
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                // Update local in-memory state
                media.alt_text = altText;
                media.caption = caption;
                savedIndicator.classList.remove('hidden');
                showToast('Media metadata updated!', 'success');
            } else {
                showToast(data.message || 'Failed to save metadata', 'error');
            }
        } catch (err) {
            console.error('Update Error:', err);
            showToast('Error updating metadata', 'error');
        } finally {
            btnSave.disabled = false;
            btnSave.innerHTML = '<span>Save Metadata</span>';
        }
    }

    // AJAX Delete Media
    async function deleteInspectorMedia() {
        const media = mediaItemsList[currentInspectorIndex];
        if (!media) return;

        if (!confirm(`Are you sure you want to permanently delete "${media.original_name}"? This cannot be undone.`)) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const res = await fetch(`/admin/media/${media.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                closeInspector();
                showToast('Media deleted permanently.', 'success');
                setTimeout(() => window.location.reload(), 500);
            } else {
                showToast(data.message || 'Failed to delete asset', 'error');
            }
        } catch (err) {
            console.error('Delete Error:', err);
            showToast('Error deleting asset', 'error');
        }
    }

    // Simple Floating Toast Notification Helper
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto px-4 py-2.5 rounded-2xl text-xs font-semibold shadow-lg flex items-center gap-2 transition-all transform duration-300 translate-y-2 opacity-0 ${
            type === 'success'
                ? 'bg-slate-900 text-white border border-slate-700'
                : 'bg-rose-600 text-white shadow-rose-600/30'
        }`;

        toast.innerHTML = `
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>${message}</span>
        `;

        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        });

        // Auto remove after 3s
        setTimeout(() => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@endsection
