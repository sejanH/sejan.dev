@extends('layouts.blog')

@section('title', 'Sejan · Blog — Technology, Architecture & Modern Development')
@section('meta_description', 'Engineering insights, software architecture breakdowns, PHP/Laravel patterns, Linux administration, and full-stack development.')
@section('canonical_url', route('home'))

@if ($posts->isNotEmpty() && ($posts->first()->featured_image || $posts->first()->thumbnail_url))
    @section('preload_headers')
        <link
            rel="preload"
            as="image"
            href="{{ $posts->first()->thumbnail_url ?: $posts->first()->featured_image }}"
            imagesrcset="{{ $posts->first()->thumbnail_url }} 400w, {{ $posts->first()->featured_image }} 1200w"
            imagesizes="(max-width: 640px) 100vw, (max-width: 1024px) 60vw, 650px"
            fetchpriority="high"
        >
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
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Compact Editorial Intro (Homepage First Page Only) -->
        @if (empty($search) && empty($selectedCategorySlug) && (!request()->has('page') || request('page') == 1))
            <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200/80 pb-5">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/60 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-800 mb-2 shadow-2xs">
                        <span class="flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        <span>Engineering · Architecture · DevOps</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-slate-900">
                        Technical Notes &amp; Engineering Breakdowns
                    </h1>
                    <p class="mt-1.5 text-xs sm:text-sm text-slate-600 max-w-2xl leading-relaxed">
                        Production insights on Linux administration, Laravel 12 architecture, cloud infrastructure, and backend performance.
                    </p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('blog.about') }}" class="text-[11px] font-semibold text-slate-700 bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 px-3.5 py-1.5 rounded-full shadow-2xs transition">
                        About Author
                    </a>
                    <a href="{{ route('feed.rss') }}" target="_blank" rel="noopener" class="text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 px-2.5 py-1.5 rounded-full shadow-2xs transition inline-flex items-center gap-1.5" title="RSS Feed">
                        <svg class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20C5 20 4 19 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1Z"/>
                        </svg>
                        <span>RSS</span>
                    </a>
                </div>
            </div>

            <!-- Featured Lead Article Showcase -->
            @if ($posts->isNotEmpty())
                @php $featuredLead = $posts->first(); @endphp
                <div class="mb-8">
                    <article class="group glass-card overflow-hidden rounded-2xl grid grid-cols-1 lg:grid-cols-12 gap-0 items-stretch border border-slate-200/80 hover-lift-enhanced transition-all duration-300">
                        <!-- Featured Image Column -->
                        <div class="lg:col-span-7 relative min-h-[190px] sm:min-h-[250px] lg:min-h-[280px] overflow-hidden bg-slate-100">
                            @if ($featuredLead->featured_image)
                                <img
                                    src="{{ $featuredLead->thumbnail_url ?: $featuredLead->featured_image }}"
                                    srcset="{{ $featuredLead->thumbnail_url }} 400w, {{ $featuredLead->featured_image }} 1200w"
                                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 60vw, 650px"
                                    alt="{{ $featuredLead->title }}"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="eager"
                                    fetchpriority="high"
                                    decoding="async"
                                />
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-100 via-slate-100 to-teal-100 text-xs font-bold uppercase tracking-wider text-emerald-700 min-h-[220px]">
                                    {{ $featuredLead->categories->first()->name ?? 'Featured Article' }}
                                </div>
                            @endif

                            @if ($featuredLead->categories->first())
                                <div class="absolute top-3.5 left-3.5">
                                    <span class="rounded-full bg-white/95 backdrop-blur-md px-3 py-0.5 text-[11px] font-bold text-emerald-800 shadow-xs border border-white/80">
                                        {{ $featuredLead->categories->first()->name }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Content Column -->
                        <div class="lg:col-span-5 p-4.5 sm:p-6 lg:p-6.5 flex flex-col justify-between bg-white">
                            <div>
                                <!-- Meta Row -->
                                <div class="flex items-center gap-2.5 text-[11px] font-medium text-slate-500 mb-2">
                                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-150">
                                        Featured
                                    </span>
                                    <span>•</span>
                                    <span>{{ $featuredLead->published_at ? $featuredLead->published_at->format('M d, Y') : $featuredLead->created_at->format('M d, Y') }}</span>
                                    <span>•</span>
                                    <span>{{ ceil(str_word_count(strip_tags($featuredLead->content)) / 200) }} min read</span>
                                </div>

                                <!-- Title -->
                                <a href="{{ route('blog.show', $featuredLead->slug) }}" class="block group/lead">
                                    <h2 class="text-lg sm:text-xl font-bold leading-snug text-slate-900 group-hover/lead:text-emerald-600 transition-colors">
                                        {{ $featuredLead->title }}
                                    </h2>
                                </a>

                                <!-- Excerpt -->
                                <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-3">
                                    {{ $featuredLead->excerpt }}
                                </p>
                            </div>

                            <!-- Read Button & Tags -->
                            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <a
                                    href="{{ route('blog.show', $featuredLead->slug) }}"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-white bg-slate-900 hover:bg-emerald-600 px-3.5 py-2 rounded-full transition-colors shadow-xs"
                                >
                                    <span>Read Article</span>
                                    <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>

                                @if ($featuredLead->tags->isNotEmpty())
                                    <div class="hidden sm:flex items-center gap-1 overflow-hidden">
                                        @foreach ($featuredLead->tags->take(2) as $tag)
                                            <span class="text-[10px] text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200">
                                                #{{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @endif
        @endif

        <!-- Search Results Banner (Active Search) -->
        @if (!empty($search))
            <div class="mb-5 px-3.5 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-between gap-3 text-xs text-slate-700 shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div id="posts" class="mb-6">
            <div class="relative w-full overflow-hidden">
                <nav
                    id="categoryScrollTrack"
                    class="flex items-center gap-1.5 overflow-x-auto py-1.5 px-0.5 scroll-smooth select-none"
                    style="-webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none;"
                    aria-label="Category Filter"
                >
                    <a
                        href="{{ route('home') }}#posts"
                        class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 {{ empty($selectedCategorySlug) && empty($search) ? 'bg-slate-900 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50 shadow-2xs' }}"
                    >
                        All Articles
                    </a>

                    @foreach ($categories as $cat)
                        <a
                            href="{{ route('blog.category', $cat->slug) }}"
                            class="category-pill shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-1.5 {{ $selectedCategorySlug === $cat->slug ? 'bg-emerald-600 text-white shadow-xs active-cat' : 'bg-white border border-slate-200 text-slate-600 hover:border-slate-300 hover:text-slate-900 hover:bg-slate-50 shadow-2xs' }}"
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

        @php
            // On the homepage first page without search/category filter, skip the lead article since it's showcased above
            $isLeadShowcased = empty($search) && empty($selectedCategorySlug) && (!request()->has('page') || request('page') == 1);
            $gridPosts = $isLeadShowcased ? $posts->slice(1) : $posts;
        @endphp

        <!-- Articles Grid -->
        @if ($gridPosts->count() > 0)
            <div class="masonry-grid">
                @foreach ($gridPosts as $post)
                    <article class="group glass-card overflow-hidden rounded-2xl flex flex-col hover-lift-enhanced transition-all duration-300">
                        <!-- Image with Category Pill Overlay -->
                        <div class="relative h-40 sm:h-44 w-full overflow-hidden bg-slate-100">
                            @if ($post->featured_image)
                                <img
                                    src="{{ $post->thumbnail_url }}"
                                    alt="{{ $post->title }}"
                                    width="300"
                                    height="176"
                                    class="h-full w-full object-cover aspect-[300/176] transition-transform duration-500 group-hover:scale-105"
                                    @if ($loop->iteration <= 3)
                                        loading="eager"
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
                                <div class="absolute top-3 left-3">
                                    <span class="rounded-full bg-white/90 backdrop-blur-md px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 shadow-xs border border-white/60">
                                        {{ $post->categories->first()->name }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Post Card Body -->
                        <div class="flex flex-1 flex-col justify-between p-4 sm:p-5">
                            <div>
                                <!-- Metadata (Date & Comments) -->
                                <div class="flex items-center gap-3 text-[11px] font-medium text-slate-500 mb-2">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                                    </span>

                                    @if ($post->comments_count !== null || $post->comments()->count() > 0)
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            {{ $post->comments_count ?? $post->comments()->count() }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Article Title -->
                                <a href="{{ route('blog.show', $post->slug) }}" class="block group/title">
                                    <h2 class="text-sm sm:text-base font-bold leading-snug text-slate-900 transition-colors group-hover/title:text-emerald-600 line-clamp-2">
                                        {{ $post->title }}
                                    </h2>
                                </a>

                                <!-- Article Excerpt -->
                                <p class="mt-1.5 text-xs text-slate-600 leading-relaxed line-clamp-2 sm:line-clamp-3">
                                    {{ $post->excerpt }}
                                </p>
                            </div>

                            <!-- Read More CTA -->
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <a
                                    href="{{ route('blog.show', $post->slug) }}"
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full transition-all hover:bg-emerald-100 hover:text-emerald-800"
                                >
                                    <span>Read More</span>
                                    <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>

                                <span class="text-[11px] text-slate-400 font-medium">
                                    {{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="glass-card rounded-2xl p-10 text-center my-8">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3.5 border border-emerald-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1.5">No posts found</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mb-5">
                    We couldn't find any articles matching your query. Try searching for something else or explore all articles.
                </p>
                <a href="{{ route('home') }}" class="px-5 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-full hover:bg-emerald-700 transition">
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
