@extends('layouts.app')

@section('title', 'Manage Blog Articles')

@section('layout')
<div class="min-h-screen bg-slate-950 flex flex-col lg:flex-row">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Header -->
        <header class="p-4 sm:p-6 border-b border-slate-800 bg-slate-900/40 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    Blog Articles
                </h1>
                <p class="text-xs text-slate-400">
                    Create, edit, publish, and manage your migrated and native blog posts.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.posts.create') }}"
                    class="px-4 py-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-semibold rounded-xl text-xs shadow-lg shadow-red-600/30 transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Write New Article</span>
                </a>
            </div>
        </header>

        <!-- Body -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-xs text-emerald-300 flex items-center gap-3">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Filter Bar -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.posts.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ empty($currentStatus) ? 'bg-red-600 text-white' : 'bg-slate-900 text-slate-400 hover:bg-slate-800' }} transition">
                        All ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'published' ? 'bg-red-600 text-white' : 'bg-slate-900 text-slate-400 hover:bg-slate-800' }} transition">
                        Published ({{ $publishedCount }})
                    </a>
                    <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $currentStatus === 'draft' ? 'bg-red-600 text-white' : 'bg-slate-900 text-slate-400 hover:bg-slate-800' }} transition">
                        Drafts ({{ $draftCount }})
                    </a>
                </div>

                <form action="{{ route('admin.posts.index') }}" method="GET" class="relative max-w-xs w-full">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search posts..."
                        class="w-full pl-9 pr-4 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-red-500"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>

            <!-- Posts Table -->
            <div class="glass-panel rounded-3xl border border-slate-800 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/80 border-b border-slate-800 text-[11px] uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="py-3.5 px-6">Title & Summary</th>
                                <th class="py-3.5 px-4">Categories</th>
                                <th class="py-3.5 px-4">Origin</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4">Views</th>
                                <th class="py-3.5 px-4">Date</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @forelse ($posts as $post)
                                <tr class="hover:bg-slate-900/40 transition">
                                    <td class="py-4 px-6 max-w-sm">
                                        <div class="font-bold text-white text-sm hover:text-red-400 transition">
                                            <a href="{{ route('admin.posts.edit', $post) }}">{{ $post->title }}</a>
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono mt-0.5 truncate">/posts/{{ $post->slug }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($post->categories as $cat)
                                                <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 text-[10px]">
                                                    {{ $cat->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($post->wp_id)
                                            <span class="px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-mono">
                                                WP #{{ $post->wp_id }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 text-[10px]">
                                                Native
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($post->status === 'published')
                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium text-[11px]">
                                                Published
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 font-medium text-[11px]">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 font-mono text-slate-400">
                                        {{ number_format($post->views_count) }}
                                    </td>
                                    <td class="py-4 px-4 text-slate-400 font-mono text-[11px]">
                                        {{ $post->published_at ? $post->published_at->format('Y-m-d') : 'Draft' }}
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-slate-400 hover:text-white transition">
                                            View
                                        </a>
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-red-400 hover:text-red-300 font-semibold transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Delete this article?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-400 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-500">
                                        No articles found. Write your first article or trigger WordPress migration!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($posts->hasPages())
                    <div class="p-4 border-t border-slate-800">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection
