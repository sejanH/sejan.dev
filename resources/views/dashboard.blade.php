@extends('layouts.app')

@section('title', 'Analytics & Control Center Dashboard')

@section('layout')
<div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row text-slate-900">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Navigation / Header -->
        <header class="p-4 sm:p-6 border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Analytics & Overview
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live System
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Publication metrics, readership analytics, comment moderation, and system diagnostics.
                </p>
            </div>

            <!-- Top Actions -->
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.posts.create') }}"
                    class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-95 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Write Article</span>
                </a>

                <a
                    href="{{ route('admin.wordpress.index') }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition flex items-center gap-1.5"
                    title="Open WordPress Ingestion Pipeline"
                >
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span class="hidden sm:inline">WP Pipeline</span>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-red-50 text-slate-700 hover:text-red-700 border border-slate-200 hover:border-red-200 text-xs font-medium transition flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Body -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8 w-full">
            <!-- Flash Message Banner -->
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-emerald-900">Action Complete</div>
                            <div class="text-xs text-emerald-700">{{ session('status') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Top Stat Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Stat 1: Total Published Posts -->
                <div class="glass-card rounded-2xl p-5 border border-slate-200 bg-white relative overflow-hidden group hover:border-emerald-300 transition shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Published Posts</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $stats['published_posts'] }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="text-emerald-700 font-semibold">{{ $stats['draft_posts'] }} Drafts</span>
                        <span>&bull;</span>
                        <span>{{ $stats['total_posts'] }} Total Articles</span>
                    </div>
                </div>

                <!-- Stat 2: Total Readership Views -->
                <div class="glass-card rounded-2xl p-5 border border-slate-200 bg-white relative overflow-hidden group hover:border-blue-300 transition shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Readership</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($stats['total_views']) }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="text-blue-700 font-semibold">Post Views</span>
                        <span>&bull;</span>
                        <span>Lifetime Reads</span>
                    </div>
                </div>

                <!-- Stat 3: Community Comments -->
                <div class="glass-card rounded-2xl p-5 border border-slate-200 bg-white relative overflow-hidden group hover:border-amber-300 transition shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Comments</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $stats['total_comments'] }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                        @if ($stats['pending_comments'] > 0)
                            <span class="text-amber-700 font-semibold">{{ $stats['pending_comments'] }} Pending</span>
                        @else
                            <span class="text-emerald-700 font-semibold">All Approved</span>
                        @endif
                        <span>&bull;</span>
                        <span>Honeypot Active</span>
                    </div>
                </div>

                <!-- Stat 4: SEO & Infrastructure -->
                <div class="glass-card rounded-2xl p-5 border border-slate-200 bg-white relative overflow-hidden group hover:border-purple-300 transition shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">SEO & Assets</span>
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $stats['total_redirects'] }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="text-purple-700 font-semibold">301 Rules</span>
                        <span>&bull;</span>
                        <span>{{ $stats['total_media'] }} Media Files</span>
                    </div>
                </div>
            </div>

            <!-- Interactive Analytics Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Main Chart: Readership & Publication Growth (2 Columns) -->
                <div class="lg:col-span-2 glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                                <span>Publication & Readership Trends</span>
                            </h2>
                            <p class="text-xs text-slate-500">Monthly breakdown of published engineering posts and total visitor reads.</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-medium">
                            <span class="flex items-center gap-1.5 text-emerald-700">
                                <span class="w-3 h-3 rounded-md bg-emerald-500 inline-block"></span>
                                Views
                            </span>
                            <span class="flex items-center gap-1.5 text-teal-700">
                                <span class="w-3 h-3 rounded-md bg-teal-400 inline-block"></span>
                                Articles
                            </span>
                        </div>
                    </div>

                    <!-- Canvas -->
                    <div class="relative h-72 sm:h-80 w-full">
                        <canvas id="growthTrendChart"></canvas>
                    </div>
                </div>

                <!-- Secondary Chart: Taxonomy Distribution (1 Column) -->
                <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                            <span>Topic Distribution</span>
                        </h2>
                        <p class="text-xs text-slate-500">Content distribution across primary taxonomy categories.</p>
                    </div>

                    <!-- Canvas -->
                    <div class="relative h-60 sm:h-64 w-full flex items-center justify-center">
                        <canvas id="categoryDistributionChart"></canvas>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Total Taxonomies:</span>
                        <span class="font-semibold text-slate-800">{{ $stats['total_categories'] }} Categories &bull; {{ $stats['total_tags'] }} Tags</span>
                    </div>
                </div>
            </div>

            <!-- Lower Grid: Top Articles & System Diagnostics -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Left: Top Performing Articles (2 Columns) -->
                <div class="lg:col-span-2 glass-panel rounded-3xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                <span>Top Performing Articles</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Most viewed publications and architectural posts</p>
                        </div>
                        <a href="{{ route('admin.posts.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition">
                            View All Articles &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="py-3 px-5">Article Title</th>
                                    <th class="py-3 px-4">Category</th>
                                    <th class="py-3 px-4 text-right">Views</th>
                                    <th class="py-3 px-5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($topPosts as $post)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3.5 px-5">
                                            <a href="{{ route('admin.posts.edit', $post) }}" class="font-semibold text-slate-900 hover:text-emerald-700 transition line-clamp-1">
                                                {{ $post->title }}
                                            </a>
                                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">/posts/{{ $post->slug }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if ($post->categories->first())
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-medium">
                                                    {{ $post->categories->first()->name }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">Uncategorized</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <span class="font-mono font-bold text-slate-900">{{ number_format($post->views_count) }}</span>
                                            <span class="text-[10px] text-slate-400 block">reads</span>
                                        </td>
                                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="View Public Post">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('admin.posts.edit', $post) }}" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition" title="Edit Article">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400">
                                            No publications found yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right: System Diagnostics & Security Status (1 Column) -->
                <div class="space-y-6">
                    <!-- Security Box -->
                    <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Security & Authentication</h3>
                                <p class="text-[11px] text-amber-700 font-medium">Public registration locked</p>
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3 border border-slate-200 text-xs space-y-1.5 font-mono">
                            <div class="text-slate-500 flex justify-between">
                                <span>Active Admin:</span>
                                <span class="text-slate-900 font-semibold">{{ $user->email }}</span>
                            </div>
                            <div class="text-slate-500 flex justify-between">
                                <span>Role:</span>
                                <span class="text-emerald-700 font-bold uppercase">{{ $user->role ?? 'admin' }}</span>
                            </div>
                            <div class="text-slate-500 flex justify-between">
                                <span>Total Admins:</span>
                                <span class="text-slate-900 font-semibold">{{ $stats['total_admins'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- System Diagnostics -->
                    <div class="glass-panel rounded-3xl p-6 border border-slate-200 bg-white shadow-xs space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            <span>System Environment</span>
                        </h3>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="text-slate-500">PHP Version</span>
                                <span class="text-slate-900 font-semibold">{{ $systemInfo['php_version'] }}</span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="text-slate-500">Database Connection</span>
                                <span class="text-slate-900 font-mono">{{ $systemInfo['db_driver'] }} ({{ $systemInfo['db_name'] }})</span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="text-slate-500">Session Driver</span>
                                <span class="text-emerald-700 font-semibold">database</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Migration Pipeline</span>
                                <a href="{{ route('admin.wordpress.index') }}" class="text-emerald-600 hover:text-emerald-800 font-semibold underline">
                                    Resource Available &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Chart.js Analytics Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Chart === 'undefined') {
        console.warn('Chart.js not loaded yet');
        return;
    }

    const months = @json($chartMonths);
    const postsData = @json($chartPosts);
    const viewsData = @json($chartViews);
    const categoryLabels = @json($categoryChart['labels']);
    const categoryData = @json($categoryChart['data']);

    // 1. Publication & Readership Growth Trend Chart
    const trendCtx = document.getElementById('growthTrendChart');
    if (trendCtx) {
        new window.Chart(trendCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Readership Views',
                        data: viewsData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y',
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Articles Published',
                        data: postsData,
                        borderColor: '#0d9488',
                        backgroundColor: 'rgba(13, 148, 136, 0.8)',
                        type: 'bar',
                        borderRadius: 6,
                        yAxisID: 'y1',
                        barThickness: 18,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 12,
                        boxPadding: 6,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11,
                                family: "'Plus Jakarta Sans', sans-serif"
                            }
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: {
                            color: '#f1f5f9',
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11,
                            },
                            precision: 0
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            color: '#0d9488',
                            font: {
                                size: 11,
                            },
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // 2. Topic / Category Distribution Doughnut Chart
    const catCtx = document.getElementById('categoryDistributionChart');
    if (catCtx) {
        new window.Chart(catCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: [
                        '#10b981', // Emerald
                        '#06b6d4', // Cyan
                        '#8b5cf6', // Purple
                        '#f59e0b', // Amber
                        '#3b82f6', // Blue
                        '#ec4899', // Pink
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 12,
                            color: '#475569',
                            font: {
                                size: 11,
                                family: "'Plus Jakarta Sans', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#e2e8f0',
                        padding: 10,
                        cornerRadius: 10,
                    }
                },
                cutout: '68%',
            }
        });
    }
});
</script>
@endsection
