@extends('layouts.app')

@section('title', 'WordPress Migration Dashboard')

@section('layout')
<div class="min-h-screen bg-slate-950 flex flex-col lg:flex-row">
    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Navigation / Header -->
        <header class="p-4 sm:p-6 border-b border-slate-800 bg-slate-900/40 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                        Migration Command Center
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Admin Active
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-400 mt-0.5">
                    WordPress blog ingestion, asset transformations, and SEO redirect preservation.
                </p>
            </div>

            <!-- Top Actions -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800/60 border border-slate-700/60 text-xs font-mono text-slate-300">
                    <span class="text-slate-400">Driver:</span>
                    <span class="text-red-400 font-semibold uppercase">{{ $systemInfo['wp_driver'] }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-red-600/20 text-slate-300 hover:text-red-400 border border-slate-700 text-xs font-medium transition flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Body -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8 max-w-7xl w-full mx-auto">
            <!-- Flash Message Banner -->
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-sm text-emerald-300 flex items-center justify-between gap-3 shadow-lg shadow-emerald-500/5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-emerald-200">Action Complete</div>
                            <div class="text-xs text-emerald-300/80">{{ session('status') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Top Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Stat 1: Migrated Posts -->
                <div class="glass-card rounded-2xl p-5 border border-slate-800 relative overflow-hidden group hover:border-slate-700 transition">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-red-500/5 rounded-full blur-xl group-hover:bg-red-500/10 transition"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Migrated Posts</span>
                        <div class="w-8 h-8 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center border border-red-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-white tracking-tight">{{ $stats['migrated_posts'] }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-400">
                        <span class="text-emerald-400 font-medium">Ready to sync</span>
                        <span>&bull;</span>
                        <span>Posts & Pages</span>
                    </div>
                </div>

                <!-- Stat 2: Media Assets -->
                <div class="glass-card rounded-2xl p-5 border border-slate-800 relative overflow-hidden group hover:border-slate-700 transition">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/5 rounded-full blur-xl group-hover:bg-blue-500/10 transition"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Media Library</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-white tracking-tight">{{ $stats['migrated_media'] }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-400">
                        <span class="text-blue-400 font-medium">Auto WebP</span>
                        <span>&bull;</span>
                        <span>storage/app/public</span>
                    </div>
                </div>

                <!-- Stat 3: Taxonomies -->
                <div class="glass-card rounded-2xl p-5 border border-slate-800 relative overflow-hidden group hover:border-slate-700 transition">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Taxonomies</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-white tracking-tight">{{ $stats['migrated_categories'] }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-400">
                        <span class="text-emerald-400 font-medium">Hierarchy Intact</span>
                        <span>&bull;</span>
                        <span>Categories & Tags</span>
                    </div>
                </div>

                <!-- Stat 4: 301 Redirects -->
                <div class="glass-card rounded-2xl p-5 border border-slate-800 relative overflow-hidden group hover:border-slate-700 transition">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/5 rounded-full blur-xl group-hover:bg-purple-500/10 transition"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">301 SEO Redirects</span>
                        <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center border border-purple-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-white tracking-tight">{{ $stats['active_redirects'] }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-400">
                        <span class="text-purple-400 font-medium">Zero Link Rot</span>
                        <span>&bull;</span>
                        <span>Legacy WP Slugs</span>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Left: WordPress Migration Engine Control (2 Columns) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Settings Panel -->
                    <div id="migration-settings" class="glass-panel rounded-3xl p-6 border border-slate-800 shadow-xl space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                    </svg>
                                    <span>WordPress Ingestion Pipeline</span>
                                </h2>
                                <p class="text-xs text-slate-400 mt-1">Configured parameters for reading and converting WordPress data.</p>
                            </div>
                            <span class="px-3 py-1 rounded-xl bg-slate-800 border border-slate-700 text-xs font-mono text-slate-300">
                                config/wordpress.php
                            </span>
                        </div>

                        <!-- Config Parameters Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-1">
                                <div class="text-[11px] font-semibold uppercase text-slate-400">Migration Driver</div>
                                <div class="text-sm font-mono text-white flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    <span>{{ $systemInfo['wp_driver'] }}</span>
                                    <span class="text-[11px] text-slate-400">(Direct DB)</span>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-1">
                                <div class="text-[11px] font-semibold uppercase text-slate-400">Target WP Database</div>
                                <div class="text-sm font-mono text-white truncate">
                                    {{ $systemInfo['wp_db_database'] }}
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-1">
                                <div class="text-[11px] font-semibold uppercase text-slate-400">Media Sync Pipeline</div>
                                <div class="text-sm font-mono text-emerald-400 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Automatic WebP Ingestion</span>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-1">
                                <div class="text-[11px] font-semibold uppercase text-slate-400">Public Registration</div>
                                <div class="text-sm font-mono text-rose-400 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    <span>Strictly Disabled (Admin Only)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Artisan CLI Box -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Artisan Migration Commands</span>
                                <span class="text-[11px] text-slate-400 font-mono">Terminal CLI</span>
                            </div>
                            <div class="bg-slate-950 rounded-2xl p-4 border border-slate-800 font-mono text-xs text-slate-300 space-y-2.5">
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-2">
                                        <span class="text-red-500 font-bold">$</span>
                                        <span class="text-slate-100">php artisan wp:migrate</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">Full migration</span>
                                </div>
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-2">
                                        <span class="text-red-500 font-bold">$</span>
                                        <span class="text-slate-100">php artisan wp:migrate --posts</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">Posts & pages only</span>
                                </div>
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-2">
                                        <span class="text-red-500 font-bold">$</span>
                                        <span class="text-slate-100">php artisan wp:migrate --media</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">Download uploads</span>
                                </div>
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-2">
                                        <span class="text-red-500 font-bold">$</span>
                                        <span class="text-slate-100">php artisan wp:redirects:generate</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">Build 301 maps</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Security & System Diagnostics (1 Column) -->
                <div class="space-y-6">
                    <!-- Security Box -->
                    <div class="glass-panel rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white">Security & Access Policy</h3>
                                <p class="text-[11px] text-amber-300 font-medium">Public registration is locked</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 leading-relaxed">
                            Registration routes (<code class="text-slate-200 bg-slate-900 px-1 py-0.5 rounded">/register</code>) are permanently disabled. Only administrator accounts initialized via database seeders can access the dashboard.
                        </p>

                        <div class="rounded-xl bg-slate-900/80 p-3 border border-slate-800 text-xs space-y-1.5 font-mono">
                            <div class="text-slate-400 flex justify-between">
                                <span>Active Admin:</span>
                                <span class="text-slate-200">{{ $user->email }}</span>
                            </div>
                            <div class="text-slate-400 flex justify-between">
                                <span>Role:</span>
                                <span class="text-emerald-400 font-bold uppercase">{{ $user->role ?? 'admin' }}</span>
                            </div>
                            <div class="text-slate-400 flex justify-between">
                                <span>Total Admins:</span>
                                <span class="text-slate-200">{{ $stats['total_admins'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- System Diagnostics -->
                    <div class="glass-panel rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            <span>System Environment</span>
                        </h3>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-800/80">
                                <span class="text-slate-400">Framework</span>
                                <span class="text-slate-200 font-semibold">Laravel v{{ $systemInfo['laravel_version'] }}</span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b border-slate-800/80">
                                <span class="text-slate-400">PHP Version</span>
                                <span class="text-slate-200 font-semibold">{{ $systemInfo['php_version'] }}</span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b border-slate-800/80">
                                <span class="text-slate-400">Database Connection</span>
                                <span class="text-slate-200 font-mono">{{ $systemInfo['db_driver'] }} ({{ $systemInfo['db_name'] }})</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Session Driver</span>
                                <span class="text-emerald-400 font-semibold">database</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity Log -->
                    <div class="glass-panel rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Activity Timeline</span>
                        </h3>

                        <div class="space-y-3">
                            @foreach ($recentActivities as $activity)
                                <div class="flex items-start gap-3 text-xs">
                                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 @if($activity['type'] === 'success') bg-emerald-400 @elseif($activity['type'] === 'info') bg-blue-400 @else bg-amber-400 @endif"></div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-semibold text-slate-200">{{ $activity['title'] }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 leading-snug">{{ $activity['description'] }}</div>
                                        <div class="text-[10px] text-slate-400 mt-1 font-mono">{{ $activity['time'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
