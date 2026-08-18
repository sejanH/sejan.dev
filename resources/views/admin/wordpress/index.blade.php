@extends('layouts.app')

@section('title', 'WordPress Migration Control Center')

@section('layout')
<div class="min-h-screen bg-slate-950 flex flex-col lg:flex-row">
    @include('admin.partials.sidebar')

    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <header class="p-4 sm:p-6 border-b border-slate-800 bg-slate-900/40 backdrop-blur-md sticky top-0 z-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    WordPress Migration Engine
                </h1>
                <p class="text-xs text-slate-400">
                    Extract, clean, and ingest your legacy WordPress database into Laravel 12.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('admin.wordpress.test') }}" method="POST" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Test WP Database</span>
                    </button>
                </form>

                <form action="{{ route('admin.wordpress.migrate') }}" method="POST" class="inline" onsubmit="return confirm('Start full WordPress migration? This will import posts, media, taxonomies, and 301 redirects.');">
                    @csrf
                    <button
                        type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-semibold rounded-xl text-xs shadow-lg shadow-red-600/30 transition flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Run Full Migration</span>
                    </button>
                </form>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-8 max-w-7xl w-full mx-auto">
            <!-- Flash Status Messages -->
            @if (session('status'))
                <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-xs text-emerald-300 flex items-start gap-3 shadow-lg shadow-emerald-500/5">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <div class="font-bold text-emerald-200">Success</div>
                        <div class="mt-0.5 leading-relaxed">{{ session('status') }}</div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-2xl bg-rose-500/10 border border-rose-500/20 p-4 text-xs text-rose-300 flex items-start gap-3 shadow-lg shadow-rose-500/5">
                    <svg class="w-5 h-5 text-rose-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <div class="font-bold text-rose-200">Migration Notice</div>
                        <div class="mt-0.5 leading-relaxed">{{ session('error') }}</div>
                    </div>
                </div>
            @endif

            <!-- Live Stats Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="glass-card rounded-2xl p-5 border border-slate-800 space-y-1">
                    <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Migrated Posts</div>
                    <div class="text-3xl font-extrabold text-white">{{ $stats['wp_posts'] }}</div>
                    <div class="text-xs text-slate-400 font-mono">Out of {{ $stats['total_posts'] }} total posts</div>
                </div>

                <div class="glass-card rounded-2xl p-5 border border-slate-800 space-y-1">
                    <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Taxonomies</div>
                    <div class="text-3xl font-extrabold text-white">{{ $stats['total_categories'] }} / {{ $stats['total_tags'] }}</div>
                    <div class="text-xs text-slate-400 font-mono">Categories / Tags</div>
                </div>

                <div class="glass-card rounded-2xl p-5 border border-slate-800 space-y-1">
                    <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Active 301 Redirects</div>
                    <div class="text-3xl font-extrabold text-white">{{ $stats['total_redirects'] }}</div>
                    <div class="text-xs text-purple-400 font-mono">SEO permalinks secured</div>
                </div>

                <div class="glass-card rounded-2xl p-5 border border-slate-800 space-y-1">
                    <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Last Synced</div>
                    <div class="text-base font-bold text-slate-200 truncate">{{ $stats['last_migration_at'] }}</div>
                    <div class="text-xs text-emerald-400 font-mono">Pipeline ready</div>
                </div>
            </div>

            <!-- Configuration Form & CLI Reference -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Left: Migration Config Form (2 Cols) -->
                <div class="lg:col-span-2">
                    <div class="glass-panel rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl space-y-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                                <span>WordPress Connection Parameters</span>
                            </h2>
                            <span class="text-xs text-slate-400 font-mono">Dynamic Ingestion</span>
                        </div>

                        <form action="{{ route('admin.wordpress.settings') }}" method="POST" class="space-y-5">
                            @csrf

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                                    Ingestion Driver
                                </label>
                                <select name="driver" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-red-500">
                                    <option value="database" {{ $settings['driver'] === 'database' ? 'selected' : '' }}>Direct MySQL Database Connection (Fastest & Recommended)</option>
                                    <option value="rest_api" {{ $settings['driver'] === 'rest_api' ? 'selected' : '' }}>WordPress REST API (Remote)</option>
                                    <option value="xml" {{ $settings['driver'] === 'xml' ? 'selected' : '' }}>WXR XML Export File</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 mb-1">Database Host</label>
                                    <input type="text" name="host" value="{{ $settings['host'] }}" required class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 font-mono focus:outline-none focus:border-red-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 mb-1">Database Port</label>
                                    <input type="text" name="port" value="{{ $settings['port'] }}" required class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 font-mono focus:outline-none focus:border-red-500" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 mb-1">WP Database Name</label>
                                    <input type="text" name="database" value="{{ $settings['database'] }}" required class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 font-mono focus:outline-none focus:border-red-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 mb-1">Table Prefix</label>
                                    <input type="text" name="prefix" value="{{ $settings['prefix'] }}" required class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 font-mono focus:outline-none focus:border-red-500" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 mb-1">Database Username</label>
                                    <input type="text" name="username" value="{{ $settings['username'] }}" required class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 font-mono focus:outline-none focus:border-red-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 mb-1">Database Password</label>
                                    <input type="password" name="password" value="{{ $settings['password'] }}" placeholder="••••••••" class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-200 font-mono focus:outline-none focus:border-red-500" />
                                </div>
                            </div>

                            <div class="pt-2 flex items-center justify-between">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="download_media" value="1" {{ $settings['download_media'] ? 'checked' : '' }} class="rounded bg-slate-900 border-slate-700 text-red-600 focus:ring-0">
                                    <span class="text-xs text-slate-300">Download attached media and rewrite inline &lt;img&gt; URLs</span>
                                </label>

                                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-xl text-xs transition">
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right: Command Cheatsheet & Pipeline Details -->
                <div class="space-y-6">
                    <div class="glass-panel rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Terminal CLI Engine</span>
                        </h3>
                        <p class="text-xs text-slate-400">
                            You can also trigger migration via artisan commands in your terminal or background worker:
                        </p>
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 font-mono text-[11px] text-slate-300 space-y-2">
                            <div><span class="text-red-500">$</span> php artisan wp:migrate --test</div>
                            <div><span class="text-red-500">$</span> php artisan wp:migrate</div>
                            <div><span class="text-red-500">$</span> php artisan wp:migrate --taxonomies</div>
                            <div><span class="text-red-500">$</span> php artisan wp:migrate --posts</div>
                        </div>
                    </div>

                    <div class="glass-panel rounded-3xl p-6 border border-slate-800 shadow-xl space-y-3">
                        <h3 class="text-sm font-bold text-white">✨ Ingestion Guarantee</h3>
                        <ul class="space-y-2 text-xs text-slate-400">
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-400 font-bold">&check;</span>
                                <span>Gutenberg block annotations cleaned</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-400 font-bold">&check;</span>
                                <span>Yoast & RankMath SEO meta parsed</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-400 font-bold">&check;</span>
                                <span>Automatic 301 permalinks redirect map</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-400 font-bold">&check;</span>
                                <span>Preserves original publication timestamps</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
