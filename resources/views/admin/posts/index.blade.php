@extends('layouts.app')

@section('title', 'Manage Blog Articles')

@section('layout')
<div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row text-slate-900">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Header -->
        <header class="p-4 sm:p-6 border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Blog Articles
                </h1>
                <p class="text-xs text-slate-500">
                    Create, edit, publish, and manage your migrated and native blog posts.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.posts.create') }}"
                    class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Write New Article</span>
                </a>
            </div>
        </header>

        <!-- Main Content Body -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 w-full">
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-800 flex items-center gap-3">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Filter Bar -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.posts.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ empty($currentStatus) ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition">
                        All ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'published' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition">
                        Published ({{ $publishedCount }})
                    </a>
                    <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'draft' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition">
                        Drafts ({{ $draftCount }})
                    </a>
                    <a href="{{ route('admin.posts.index', ['status' => 'trashed']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'trashed' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }} transition flex items-center gap-1.5">
                        <span>Trash</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'trashed' ? 'bg-rose-700 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $trashCount }}</span>
                    </a>
                </div>

                <form action="{{ route('admin.posts.index') }}" method="GET" class="relative max-w-xs w-full">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search posts..."
                        class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition shadow-2xs"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>

            <!-- Posts Table -->
            <div class="glass-panel rounded-3xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3.5 px-6">Title & Summary</th>
                                <th class="py-3.5 px-4">Categories</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4">Views</th>
                                <th class="py-3.5 px-4">Date</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($posts as $post)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-4 px-6 max-w-sm">
                                        <div class="font-bold text-slate-900 text-sm hover:text-emerald-600 transition">
                                            <a href="{{ route('admin.posts.edit', $post) }}">{{ $post->title }}</a>
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5 truncate">/posts/{{ $post->slug }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($post->categories as $cat)
                                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] border border-slate-200">
                                                    {{ $cat->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($post->trashed())
                                            <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-medium text-[11px]">
                                                Trashed
                                            </span>
                                        @elseif($post->status === 'published')
                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium text-[11px]">
                                                Published
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-medium text-[11px]">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 font-mono text-slate-600">
                                        {{ number_format($post->views_count) }}
                                    </td>
                                    <td class="py-4 px-4 text-slate-500 font-mono text-[11px]">
                                        {{ $post->published_at ? $post->published_at->format('Y-m-d') : 'Draft' }}
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        @if ($post->trashed())
                                            <div class="inline-flex items-center gap-2">
                                                <!-- Restore Article Form -->
                                                <form action="{{ route('admin.posts.restore', $post->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button
                                                        type="submit"
                                                        class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-semibold text-xs transition flex items-center gap-1.5 shadow-2xs"
                                                        title="Restore Article from Trash"
                                                    >
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                        <span>Restore</span>
                                                    </button>
                                                </form>

                                                <!-- Permanent Force Delete Form -->
                                                <form
                                                    action="{{ route('admin.posts.forceDelete', $post->id) }}"
                                                    method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('WARNING: Are you sure you want to permanently destroy \'{{ addslashes($post->title) }}\'? This CANNOT be undone.');"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs transition flex items-center gap-1.5 shadow-2xs"
                                                        title="Permanently Delete Article"
                                                    >
                                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        <span>Delete Forever</span>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-2">
                                                <!-- View Live Article -->
                                                <a
                                                    href="{{ route('blog.show', $post->slug) }}"
                                                    target="_blank"
                                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium border border-slate-200 transition flex items-center gap-1.5 shadow-2xs"
                                                    title="View Public Post"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                    <span>View</span>
                                                </a>

                                                <!-- Edit Article (Prominent & Clear) -->
                                                <a
                                                    href="{{ route('admin.posts.edit', $post) }}"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-semibold text-xs transition flex items-center gap-1.5 shadow-2xs"
                                                    title="Edit Article"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span>Edit</span>
                                                </a>

                                                <!-- Visual Separation Divider -->
                                                <span class="h-4 w-px bg-slate-200 mx-0.5"></span>

                                                <!-- Delete Article (Isolated Destructive Action) -->
                                                <form
                                                    action="{{ route('admin.posts.destroy', $post) }}"
                                                    method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('Move \'{{ addslashes($post->title) }}\' to trash?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-900 border border-rose-200/80 hover:border-rose-300 transition flex items-center shadow-2xs"
                                                        title="Move to Trash"
                                                    >
                                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">
                                        No articles found. Write your first article or trigger WordPress migration!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($posts->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection
