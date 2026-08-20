<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-900 antialiased selection:bg-emerald-500 selection:text-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Sejan · Blog — Technology, Architecture & Modern Development')</title>
    <meta name="description" content="@yield('meta_description', 'Exploring the intersection of technology, software architecture, and modern engineering.')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
    <meta name="author" content="S. M. Mominul Haque (Sejan)">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    <meta name="theme-color" content="#10b981">
    @yield('preload_headers')

    <!-- Open Graph (Facebook / LinkedIn) -->
    <meta property="og:site_name" content="Sejan · Blog">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', View::yieldContent('title', 'Sejan · Blog'))">
    <meta property="og:description" content="@yield('og_description', View::yieldContent('meta_description', 'Exploring the intersection of technology, software architecture, and modern engineering.'))">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('favicon.ico'))">
    <meta property="og:image:alt" content="@yield('og_title', View::yieldContent('title', 'Sejan · Blog'))">
    @yield('og_article_tags')

    <!-- Twitter / X Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@sejanH">
    <meta name="twitter:creator" content="@sejanH">
    <meta name="twitter:title" content="@yield('og_title', View::yieldContent('title', 'Sejan · Blog'))">
    <meta name="twitter:description" content="@yield('og_description', View::yieldContent('meta_description', 'Exploring the intersection of technology, software architecture, and modern engineering.'))">
    <meta name="twitter:image" content="@yield('og_image', asset('favicon.ico'))">

    <!-- Structured Data (JSON-LD) -->
    @if (View::hasSection('schema_json'))
        @yield('schema_json')
    @else
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Sejan · Blog',
            'url' => url('/'),
            'description' => 'Technology, Software Architecture & Modern Engineering by Sejan',
            'publisher' => [
                '@type' => 'Person',
                'name' => 'S. M. Mominul Haque (Sejan)',
                'url' => url('/about'),
                'sameAs' => [
                    'https://www.linkedin.com/in/s-m-mominul-haque-sejan-79b77b83/',
                    'https://twitter.com/sejanH',
                    'https://github.com/sejanH',
                ],
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif

    <!-- RSS & Atom Auto-Discovery & Sitemap -->
    <link rel="alternate" type="application/rss+xml" title="Sejan · Blog (RSS 2.0)" href="{{ route('feed.rss') }}">
    <link rel="alternate" type="application/atom+xml" title="Sejan · Blog (Atom 1.0)" href="{{ route('feed.atom') }}">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap.xml') }}">

    <!-- DNS-Prefetch for External Origins (Non-blocking background resolution) -->
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://pl30906445.effectivecpmnetwork.com">
    <link rel="dns-prefetch" href="https://www.highperformanceformat.com">

    <!-- Fonts (Non-Render-Blocking Asynchronous Load with Instant System Fallback) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap">
    </noscript>

    <!-- Dynamic Local Vite Asset Bundle (Light Theme, Zero CDN) -->
    @vite(['resources/css/blog.css', 'resources/js/blog.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 flex flex-col antialiased">
    <!-- Top Reading Progress Bar (for single post view) -->
    <div id="scrollProgress" class="fixed top-0 left-0 h-1 bg-gradient-to-r from-emerald-400 via-teal-500 to-purple-500 z-50 transition-all duration-75" style="width: 0%;"></div>

    <!-- Sticky Navigation Header -->
    <header id="mainHeader" class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-shadow duration-300 transform-gpu">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="group flex items-center gap-3 rounded-2xl px-2 py-1.5 transition-all duration-300 hover:bg-slate-100/80">
                <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-sm font-bold text-white shadow-md shadow-emerald-500/20 transition-all duration-300 group-hover:scale-105 group-hover:rotate-3">
                    <span class="relative">sz</span>
                </div>
                <div>
                    <p className="text-base font-bold text-slate-900 transition-colors group-hover:text-emerald-600">
                        <span class="text-base font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Sejan</span>
                    </p>
                    <p class="text-xs text-slate-500 font-medium">Blog</p>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="text-sm font-medium px-3.5 py-2 rounded-xl transition-all duration-200 {{ request()->routeIs('home') && !request()->has('q') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    Home
                </a>
                <a href="{{ route('blog.about') }}" class="text-sm font-medium px-3.5 py-2 rounded-xl transition-all duration-200 {{ request()->routeIs('blog.about') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    About
                </a>
                <a href="{{ route('blog.contact') }}" class="text-sm font-medium px-3.5 py-2 rounded-xl transition-all duration-200 {{ request()->routeIs('blog.contact') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    Contact
                </a>
            </nav>

            <!-- Search & Actions -->
            <div class="flex items-center gap-3">
                <form action="{{ route('home') }}" method="GET" class="hidden sm:flex items-center gap-2 rounded-full bg-white border border-slate-200 px-3.5 py-1.5 shadow-2xs transition-all duration-300 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/20">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search posts..."
                        class="w-36 lg:w-48 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none"
                    />
                    <button
                        type="submit"
                        class="rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 px-3 py-1 text-xs font-semibold text-white shadow-2xs transition-all duration-300 hover:opacity-90 hover:scale-105"
                    >
                        Search
                    </button>
                </form>

                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" type="button" class="md:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors" aria-label="Toggle navigation menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 py-3 space-y-2">
            <form action="{{ route('home') }}" method="GET" class="flex items-center gap-2 rounded-xl bg-slate-50 border border-slate-200 px-3 py-2 mb-3">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search posts..."
                    class="w-full bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none"
                />
                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1 text-xs font-semibold text-white">Search</button>
            </form>
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Home</a>
            <a href="{{ route('blog.about') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">About</a>
            <a href="{{ route('blog.contact') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Contact</a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="relative mt-10 border-t border-slate-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Brand & Newsletter -->
                <div class="space-y-3">
                    <a href="{{ route('home') }}" class="inline-block">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 text-xs font-bold text-white shadow-sm">
                                sz
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Sejan</p>
                                <p class="text-xs text-slate-500 font-medium">Blog</p>
                            </div>
                        </div>
                    </a>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        Exploring the intersection of technology and creativity. Join our newsletter for the latest insights and updates.
                    </p>

                    <!-- Newsletter Signup Form -->
                    <form onsubmit="event.preventDefault(); document.getElementById('newsSuccess').classList.remove('hidden'); this.reset();" class="space-y-2">
                        <label for="newsletter" class="text-xs font-medium text-slate-800">
                            Subscribe to our newsletter
                        </label>
                        <div class="flex gap-2">
                            <input
                                id="newsletter"
                                type="email"
                                placeholder="Enter your email"
                                required
                                class="flex-1 rounded-lg bg-slate-50 px-3 py-1.5 text-xs text-slate-900 placeholder:text-slate-400 border border-slate-200 focus:border-emerald-500 focus:bg-white focus:outline-none transition-all"
                            />
                            <button
                                type="submit"
                                class="rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all duration-300 hover:opacity-95 hover:scale-105"
                            >
                                Join
                            </button>
                        </div>
                        <p id="newsSuccess" class="hidden text-xs text-emerald-600 font-medium">
                            Successfully subscribed! 🎉
                        </p>
                    </form>
                </div>

                <!-- Quick Links -->
                <div class="space-y-3 lg:pl-8">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-900">Quick Links</h3>
                    <ul class="space-y-1.5">
                        <li>
                            <a href="{{ route('home') }}" class="text-xs text-slate-600 transition-colors hover:text-emerald-600 hover:translate-x-1 inline-block">Home</a>
                        </li>
                        <li>
                            <a href="{{ route('blog.about') }}" class="text-xs text-slate-600 transition-colors hover:text-emerald-600 hover:translate-x-1 inline-block">About</a>
                        </li>
                        <li>
                            <a href="{{ route('blog.contact') }}" class="text-xs text-slate-600 transition-colors hover:text-emerald-600 hover:translate-x-1 inline-block">Contact</a>
                        </li>
                        <li>
                            <a href="{{ route('feed.rss') }}" target="_blank" class="text-xs text-slate-600 transition-colors hover:text-emerald-600 hover:translate-x-1 inline-flex items-center gap-1">
                                <span>RSS Feed</span>
                                <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20C5 20 4 19 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1Z"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sitemap.xml') }}" target="_blank" class="text-xs text-slate-600 transition-colors hover:text-emerald-600 hover:translate-x-1 inline-block">Sitemap</a>
                        </li>
                    </ul>
                </div>

                <!-- Social Connect Links -->
                <div class="space-y-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-900">Connect</h3>
                    <div class="flex gap-2">
                        <a href="https://github.com/sejanH" target="_blank" rel="noopener" aria-label="GitHub" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 border border-slate-200 text-slate-600 transition-all duration-300 hover:border-emerald-400 hover:text-emerald-600 hover:bg-white hover:scale-110 shadow-2xs">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/in/s-m-mominul-haque-sejan-79b77b83/" target="_blank" rel="noopener" aria-label="LinkedIn" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 border border-slate-200 text-slate-600 transition-all duration-300 hover:border-emerald-400 hover:text-emerald-600 hover:bg-white hover:scale-110 shadow-2xs">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                        <a href="https://twitter.com/sejanH" target="_blank" rel="noopener" aria-label="Twitter / X" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 border border-slate-200 text-slate-600 transition-all duration-300 hover:border-emerald-400 hover:text-emerald-600 hover:bg-white hover:scale-110 shadow-2xs">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.253 5.622 5.91-5.622Zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                    </div>
                    <p class="text-xs text-slate-500">
                        Stay connected and follow our journey across platforms.
                    </p>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="mt-6 pt-5 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-slate-500">
                <p>© {{ date('Y') }} <a href="https://sejan.dev" class="hover:text-emerald-600 transition-colors">Sejan.dev</a>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Global Advertising & Tracking Scripts -->
    @include('blog.partials.ads-scripts')

    @stack('scripts')
</body>
</html>
