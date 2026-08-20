@extends('layouts.blog')

@section('title', 'Sejan · Blog — Technology, Architecture & Modern Development')
@section('meta_description', 'Engineering insights, software architecture breakdowns, PHP/Laravel patterns, Linux administration, and full-stack development.')
@section('canonical_url', route('home'))

@if ($posts->isNotEmpty() && $posts->first()->thumbnail_url)
    @section('preload_headers')
        <link rel="preload" as="image" href="{{ $posts->first()->thumbnail_url }}" fetchpriority="high">
    @endsection
@endif

@section('schema_json')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebSite',
            '@id' => route('home') . '#website',
            'url' => route('home'),
            'name' => 'Sejan · Blog',
            'description' => 'Technology, Software Architecture & Modern Engineering by Sejan',
            'publisher' => [
                '@type' => 'Person',
                '@id' => route('blog.about') . '#author',
                'name' => 'S. M. Mominul Haque (Sejan)',
                'url' => route('blog.about'),
                'sameAs' => [
                    'https://www.linkedin.com/in/s-m-mominul-haque-sejan-79b77b83/',
                    'https://twitter.com/sejanH',
                    'https://github.com/sejanH',
                ],
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('home') . '?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ],
        [
            '@type' => 'Blog',
            '@id' => route('home') . '#blog',
            'name' => 'Sejan · Blog',
            'url' => route('home'),
            'description' => 'Engineering insights, software architecture breakdowns, PHP/Laravel patterns, Linux administration, and full-stack development.',
            'inLanguage' => 'en-US',
            'publisher' => [
                '@type' => 'Person',
                'name' => 'S. M. Mominul Haque (Sejan)',
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => route('home') . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('content')
    <!-- Hero Section (Show on homepage when not searching) -->
    @if (empty($search) && empty($selectedCategorySlug) && (!request()->has('page') || request('page') == 1))
        <section class="relative min-h-[50vh] sm:min-h-[55vh] flex items-center justify-center overflow-hidden py-16 sm:py-20 border-b border-slate-200/80 bg-gradient-to-br from-emerald-50/80 via-white to-teal-50/50">
            <!-- Background Radial Glow -->
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-emerald-300/20 blur-3xl"></div>
                <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-purple-300/20 blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-emerald-200/15 blur-3xl"></div>
            </div>

            <!-- Hero Content -->
            <div class="relative z-10 text-center px-4 sm:px-6 max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100/80 border border-emerald-200/80 px-3.5 py-1 text-xs font-semibold text-emerald-800 mb-6 shadow-2xs">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Engineering &amp; Modern Development</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-slate-900 mb-6">
                    <span class="block">Welcome to the Future of</span>
                    <span class="block mt-2 text-4xl sm:text-6xl lg:text-7xl">
                        <span id="rotatingWord" class="text-gradient transition-all duration-300 inline-block font-extrabold">Innovation</span>
                    </span>
                </h1>

                <p class="text-base sm:text-lg lg:text-xl text-slate-600 mb-8 max-w-2xl mx-auto leading-relaxed">
                    Exploring the intersection of technology and creativity, one post at a time.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a
                        href="#posts"
                        class="px-7 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full font-semibold text-white shadow-md shadow-emerald-500/20 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-emerald-500/30 flex items-center gap-2"
                    >
                        <span>Start Exploring</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </a>
                    <a
                        href="{{ route('blog.about') }}"
                        class="px-7 py-3.5 border border-slate-300 rounded-full font-semibold text-slate-700 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:bg-slate-100 hover:border-slate-400 shadow-2xs"
                    >
                        Learn More
                    </a>
                </div>
            </div>
        </section>
    @endif

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Search Results Banner (Active Search) -->
        @if (!empty($search))
            <div class="mb-6 px-4 py-3 rounded-2xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-between gap-3 text-xs text-slate-700 shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Search results for: <strong class="text-slate-900 font-semibold">"{{ $search }}"</strong></span>
                </div>
                <a href="{{ route('home') }}" class="font-semibold text-emerald-700 hover:text-emerald-900 transition underline">
                    Clear Search
                </a>
            </div>
        @endif

        <!-- Category Navigation Ribbon -->
        <div id="posts" class="mb-8">
            <div class="relative w-full overflow-hidden">
                <nav
                    id="categoryScrollTrack"
                    class="flex items-center gap-2 overflow-x-auto py-2 px-1 scroll-smooth select-none"
                    style="-webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none;"
                    aria-label="Category Filter"
                >
                    <a
                        href="{{ route('home') }}#posts"
                        class="shrink-0 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 {{ empty($selectedCategorySlug) && empty($search) ? 'bg-slate-900 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50 shadow-2xs' }}"
                    >
                        All Articles
                    </a>

                    @foreach ($categories as $cat)
                        <a
                            href="{{ route('blog.category', $cat->slug) }}"
                            class="category-pill shrink-0 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-1.5 {{ $selectedCategorySlug === $cat->slug ? 'bg-emerald-600 text-white shadow-xs active-cat' : 'bg-white border border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50 shadow-2xs' }}"
                        >
                            <span>{{ $cat->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded-full font-medium {{ $selectedCategorySlug === $cat->slug ? 'bg-emerald-700/60 text-emerald-100' : 'bg-slate-100 text-slate-500' }}">
                                {{ $cat->published_posts_count }}
                            </span>
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Articles Grid -->
        @if ($posts->count() > 0)
            <div class="masonry-grid">
                @foreach ($posts as $post)
                    <article class="group glass-card overflow-hidden rounded-3xl flex flex-col hover-lift-enhanced transition-all duration-300">
                        <!-- Image with Category Pill Overlay -->
                        <div class="relative h-52 w-full overflow-hidden bg-slate-100">
                            @if ($post->featured_image)
                                <img
                                    src="{{ $post->thumbnail_url }}"
                                    alt="{{ $post->title }}"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    @if ($loop->first)
                                        loading="eager"
                                        fetchpriority="high"
                                        decoding="async"
                                    @else
                                        loading="lazy"
                                        decoding="async"
                                    @endif
                                />
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-100/70 via-slate-100 to-teal-100/70 text-xs font-bold uppercase tracking-wider text-emerald-700">
                                    {{ $post->categories->first()->name ?? 'Engineering' }}
                                </div>
                            @endif

                            @if ($post->categories->first())
                                <div class="absolute top-4 left-4">
                                    <span class="rounded-full bg-white/90 backdrop-blur-md px-3 py-1 text-xs font-semibold text-emerald-700 shadow-xs border border-white/60">
                                        {{ $post->categories->first()->name }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Post Card Body -->
                        <div class="flex flex-1 flex-col justify-between p-6">
                            <div>
                                <!-- Metadata (Date & Comments) -->
                                <div class="flex items-center gap-4 text-xs font-medium text-slate-500 mb-3">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                                    </span>

                                    @if ($post->comments_count !== null || $post->comments()->count() > 0)
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            {{ $post->comments_count ?? $post->comments()->count() }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Article Title -->
                                <a href="{{ route('blog.show', $post->slug) }}" class="block group/title">
                                    <h2 class="text-xl font-bold leading-snug text-slate-900 transition-colors group-hover/title:text-emerald-600 line-clamp-2">
                                        {{ $post->title }}
                                    </h2>
                                </a>

                                <!-- Article Excerpt -->
                                <p class="mt-3 text-sm text-slate-600 leading-relaxed line-clamp-3">
                                    {{ $post->excerpt }}
                                </p>
                            </div>

                            <!-- Read More CTA -->
                            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                                <a
                                    href="{{ route('blog.show', $post->slug) }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3.5 py-1.5 rounded-full transition-all hover:bg-emerald-100 hover:text-emerald-800"
                                >
                                    <span>Read More</span>
                                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>

                                <span class="text-xs text-slate-400 font-medium">
                                    {{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="glass-card rounded-3xl p-12 text-center my-8">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No posts found</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                    We couldn't find any articles matching your query. Try searching for something else or explore all articles.
                </p>
                <a href="{{ route('home') }}" class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-full hover:bg-emerald-700 transition">
                    View All Articles
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const track = document.getElementById('categoryScrollTrack');
        if (!track) return;

        // Smooth horizontal mouse wheel scroll
        track.addEventListener('wheel', function(e) {
            if (e.deltaY !== 0) {
                e.preventDefault();
                track.scrollLeft += e.deltaY;
            }
        }, { passive: false });

        // Auto-center active category pill
        const activePill = track.querySelector('.active-cat');
        if (activePill) {
            activePill.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }

        // Drag to scroll
        let isDown = false;
        let startX, scrollLeft;

        track.addEventListener('mousedown', (e) => {
            isDown = true;
            track.classList.add('cursor-grabbing');
            startX = e.pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        });

        track.addEventListener('mouseleave', () => {
            isDown = false;
            track.classList.remove('cursor-grabbing');
        });

        track.addEventListener('mouseup', () => {
            isDown = false;
            track.classList.remove('cursor-grabbing');
        });

        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - track.offsetLeft;
            const walk = (x - startX) * 1.5;
            track.scrollLeft = scrollLeft - walk;
        });
    });
</script>
@endpush
