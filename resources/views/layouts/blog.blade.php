<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100 antialiased selection:bg-red-500 selection:text-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'sejan.dev — Modern Tech & Architecture Blog')</title>
    <meta name="description" content="@yield('meta_description', 'Engineering insights, modern Laravel, architecture, and technology thoughts.')">
    <meta property="og:title" content="@yield('title', 'sejan.dev — Modern Tech Blog')">
    <meta property="og:description" content="@yield('meta_description', 'Engineering insights, modern Laravel, architecture, and technology thoughts.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Isolated Public Blog Frontend Assets (No CDN, Ultra-Fast Local Vite Bundle) -->
    @vite(['resources/css/blog.css', 'resources/js/blog.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex flex-col antialiased">
    <!-- Top Progress Bar (for single post) -->
    <div id="scrollProgress" class="fixed top-0 left-0 h-1 bg-gradient-to-r from-red-500 via-rose-500 to-amber-500 z-50 transition-all duration-75" style="width: 0%;"></div>

    <!-- Navigation Header -->
    <header class="border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-xl sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg shadow-red-500/20 group-hover:scale-105 transition">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-tight text-white group-hover:text-red-400 transition">
                        sejan<span class="text-red-500">.dev</span>
                    </span>
                    <span class="hidden sm:inline-block ml-2 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                        Blog
                    </span>
                </div>
            </a>

            <!-- Search Bar in Header -->
            <div class="hidden md:block flex-1 max-w-xs">
                <form action="{{ route('home') }}" method="GET" class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search articles..."
                        class="w-full pl-9 pr-4 py-1.5 bg-slate-900/80 border border-slate-800 focus:border-red-500 focus:ring-1 focus:ring-red-500/30 rounded-xl text-xs text-slate-200 placeholder-slate-500 focus:outline-none transition"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>

            <!-- Header Nav Links -->
            <nav class="flex items-center gap-2 sm:gap-4">
                <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium {{ request()->routeIs('home') ? 'text-red-400 bg-red-500/10' : 'text-slate-300 hover:text-white' }} transition">
                    Articles
                </a>
                <a href="{{ route('blog.about') }}" class="px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium {{ request()->routeIs('blog.about') ? 'text-red-400 bg-red-500/10' : 'text-slate-300 hover:text-white' }} transition">
                    About
                </a>

                <div class="h-4 w-px bg-slate-800 mx-1"></div>

                @auth
                    <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>Admin Panel</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white text-xs font-semibold shadow-md shadow-red-600/20 transition">
                        Admin Login
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Page Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand Bio -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-red-600 flex items-center justify-center text-white font-bold text-xs">
                            S
                        </div>
                        <span class="font-bold text-white text-lg">sejan<span class="text-red-500">.dev</span></span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                        Modern publishing platform built on Laravel 12 with automated WordPress migration engine, 301 SEO redirects preservation, and clean architecture.
                    </p>
                </div>

                <!-- Navigation -->
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Explore</h3>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">All Articles</a></li>
                        <li><a href="{{ route('blog.about') }}" class="hover:text-white transition">About Author</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Admin Portal</a></li>
                    </ul>
                </div>

                <!-- Stack & Engine Info -->
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Platform Specs</h3>
                    <div class="space-y-1.5 text-xs text-slate-400 font-mono">
                        <div>Framework: <span class="text-red-400">Laravel v12</span></div>
                        <div>Runtime: <span class="text-slate-300">PHP 8.2+</span></div>
                        <div>Migration: <span class="text-emerald-400">WordPress Ingest</span></div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <div>&copy; {{ date('Y') }} sejan.dev. All rights reserved.</div>
                <div>Engineered with precision on Laravel 12.</div>
            </div>
        </div>
    </footer>

    <script>
        // Scroll progress indicator
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            const bar = document.getElementById('scrollProgress');
            if (bar) {
                bar.style.width = scrolled + "%";
            }
        });
    </script>
</body>
</html>
