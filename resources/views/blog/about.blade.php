@extends('layouts.blog')

@section('title', 'About — sejan.dev')
@section('meta_description', 'About Sejan and this personal engineering publication.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 space-y-12">
    <!-- Hero Profile -->
    <div class="p-8 sm:p-12 rounded-3xl bg-slate-900/50 border border-slate-800 flex flex-col sm:flex-row items-center gap-8">
        <div class="w-28 h-28 rounded-3xl bg-gradient-to-br from-red-600 via-rose-600 to-red-800 text-white font-extrabold text-3xl flex items-center justify-center flex-shrink-0 shadow-2xl shadow-red-600/30 border border-red-500/30">
            S
        </div>
        <div class="space-y-2 text-center sm:text-left">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20">
                Software Architect & Engineer
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                Hey, I'm Sejan 👋
            </h1>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                Welcome to my personal tech publication where I write about full-stack engineering, Laravel ecosystem advancements, backend performance optimization, and clean architectural design patterns.
            </p>
        </div>
    </div>

    <!-- Platform Purpose & Architecture -->
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-white tracking-tight">
            About this Platform
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs sm:text-sm text-slate-300 leading-relaxed">
            <div class="p-6 rounded-2xl bg-slate-900/30 border border-slate-800 space-y-2">
                <div class="font-bold text-white text-base flex items-center gap-2">
                    <span class="text-red-500">🚀</span> Modern Laravel 12
                </div>
                <p class="text-slate-400">
                    Engineered from the ground up using Laravel 12 skeleton, featuring high-speed Eloquent queries, database-backed sessions, and responsive Tailwind CSS views.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-900/30 border border-slate-800 space-y-2">
                <div class="font-bold text-white text-base flex items-center gap-2">
                    <span class="text-emerald-400">🔄</span> Automated WP Migration
                </div>
                <p class="text-slate-400">
                    Seamlessly migrates posts, media attachments, taxonomy hierarchies, and historical Gutenberg blocks with complete 301 redirect protection to maintain SEO equity.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
