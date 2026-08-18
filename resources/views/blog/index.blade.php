@extends('layouts.blog')

@section('title', 'sejan.dev — Modern Software Engineering & Architecture Blog')
@section('meta_description', 'Articles, architectural tutorials, Laravel best practices, and WordPress to modern stack migrations.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-12">
    <!-- Search / Filter State Header (If Filtered) -->
    @if ($search || $selectedCategorySlug)
        <div class="p-6 rounded-3xl bg-slate-900/60 border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Filtering Results</span>
                <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">
                    @if($search) Search results for &ldquo;<span class="text-red-400">{{ $search }}</span>&rdquo; @endif
                    @if($selectedCategorySlug) in Category <span class="text-red-400">#{{ $selectedCategorySlug }}</span> @endif
                </h1>
            </div>
            <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-200 transition">
                Clear Filters
            </a>
        </div>
    @endif

    <!-- Hero / Featured Post (Only when on home page root without search) -->
    @if ($featuredPost && !$search && !$selectedCategorySlug)
        <div class="relative rounded-3xl overflow-hidden border border-slate-800 bg-slate-900/40 p-6 sm:p-10 lg:p-12 hover:border-slate-700 transition duration-300 group">
            <div class="absolute -right-20 -top-20 w-96 h-96 bg-red-600/10 rounded-full blur-3xl group-hover:bg-red-600/15 transition pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <div class="lg:col-span-7 space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                            🌟 Featured Article
                        </span>
                        @if($featuredPost->categories->isNotEmpty())
                            @foreach($featuredPost->categories->take(2) as $cat)
                                <a href="{{ route('blog.category', $cat->slug) }}" class="px-3 py-1 rounded-full text-xs font-medium bg-slate-800/80 text-slate-300 hover:text-white border border-slate-700/60 transition">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        @endif
                        <span class="text-xs text-slate-400 font-mono">
                            {{ $featuredPost->reading_time }} min read
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight group-hover:text-red-400 transition">
                        <a href="{{ route('blog.show', $featuredPost->slug) }}">
                            {{ $featuredPost->title }}
                        </a>
                    </h1>

                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed line-clamp-3">
                        {{ $featuredPost->excerpt }}
                    </p>

                    <div class="pt-2 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-red-400">
                                {{ strtoupper(substr($featuredPost->user->name ?? 'Admin', 0, 2)) }}
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white">{{ $featuredPost->user->name ?? 'Admin' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    {{ $featuredPost->published_at ? $featuredPost->published_at->format('M d, Y') : 'Recently Published' }}
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('blog.show', $featuredPost->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-400 group-hover:text-red-300 transition">
                            <span>Read Full Story</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                @if($featuredPost->featured_image)
                    <div class="lg:col-span-5">
                        <a href="{{ route('blog.show', $featuredPost->slug) }}" class="block rounded-2xl overflow-hidden aspect-[16/10] bg-slate-800 border border-slate-700/60 shadow-xl group-hover:scale-[1.02] transition duration-300">
                            <img src="{{ $featuredPost->featured_image }}" alt="{{ $featuredPost->title }}" class="w-full h-full object-cover" />
                        </a>
                    </div>
                @else
                    <div class="lg:col-span-5">
                        <div class="rounded-2xl aspect-[16/10] bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 border border-slate-800 flex items-center justify-center p-8 text-center">
                            <div class="space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-400 flex items-center justify-center mx-auto border border-red-500/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                                <div class="text-xs font-mono text-slate-400">sejan.dev engineering</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Category Filter Pills -->
    @if ($categories->isNotEmpty())
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap {{ !$selectedCategorySlug ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800' }} transition">
                All Articles
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('blog.category', $cat->slug) }}" class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap {{ $selectedCategorySlug === $cat->slug ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800' }} transition flex items-center gap-2">
                    <span>{{ $cat->name }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $selectedCategorySlug === $cat->slug ? 'bg-red-700 text-white' : 'bg-slate-800 text-slate-400' }}">
                        {{ $cat->published_posts_count }}
                    </span>
                </a>
            @endforeach
        </div>
    @endif

    <!-- Main Grid of Articles -->
    <div>
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                Latest Publications
            </h2>
            <div class="text-xs text-slate-400 font-mono">
                Showing {{ $posts->count() }} of {{ $posts->total() }} articles
            </div>
        </div>

        @if ($posts->isEmpty())
            <div class="p-12 text-center rounded-3xl bg-slate-900/40 border border-slate-800 space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto text-slate-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white">No articles found</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                    No articles match your search or filter criteria. Try browsing all categories or perform a migration from WordPress.
                </p>
                <a href="{{ route('home') }}" class="inline-block px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-xs font-semibold text-white transition">
                    View All Articles
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach ($posts as $post)
                    <article class="flex flex-col rounded-3xl bg-slate-900/40 border border-slate-800/80 overflow-hidden hover:border-slate-700 hover:bg-slate-900/70 transition duration-200 group">
                        @if ($post->featured_image)
                            <a href="{{ route('blog.show', $post->slug) }}" class="aspect-[16/10] overflow-hidden bg-slate-800">
                                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                            </a>
                        @endif

                        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-3">
                                <!-- Category & Reading Time -->
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($post->categories->take(2) as $cat)
                                            <a href="{{ route('blog.category', $cat->slug) }}" class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition">
                                                {{ $cat->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $post->reading_time }} min read</span>
                                </div>

                                <!-- Post Title -->
                                <h3 class="font-bold text-lg text-white group-hover:text-red-400 transition leading-snug line-clamp-2">
                                    <a href="{{ route('blog.show', $post->slug) }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                <!-- Post Excerpt -->
                                <p class="text-xs text-slate-400 leading-relaxed line-clamp-3">
                                    {{ $post->excerpt }}
                                </p>
                            </div>

                            <!-- Footer Meta -->
                            <div class="pt-4 border-t border-slate-800/60 flex items-center justify-between text-xs text-slate-400">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-300">{{ $post->user->name ?? 'Admin' }}</span>
                                    <span>&bull;</span>
                                    <span class="font-mono text-[11px]">
                                        {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Draft' }}
                                    </span>
                                </div>
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-red-400 hover:text-red-300 font-semibold group-hover:translate-x-0.5 transition">
                                    &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-8">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    <!-- Tags Cloud Section -->
    @if ($popularTags->isNotEmpty())
        <div class="p-8 rounded-3xl bg-slate-900/30 border border-slate-800/80 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span>Popular Topics & Tags</span>
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($popularTags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-red-500/10 hover:text-red-400 text-slate-300 border border-slate-800 text-xs font-mono transition">
                        #{{ $tag->name }} ({{ $tag->published_posts_count }})
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
