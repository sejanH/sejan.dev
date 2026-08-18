@extends('layouts.blog')

@section('title', 'About Sejan — Software Engineer & Architect')
@section('meta_description', 'Learn more about S. M. Mominul Haque (Sejan), his engineering background, software architecture philosophy, and technology publication.')
@section('canonical_url', route('blog.about'))

@section('schema_json')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => ['AboutPage', 'ProfilePage'],
            '@id' => route('blog.about') . '#webpage',
            'url' => route('blog.about'),
            'name' => 'About Sejan — Software Engineer & Architect',
            'description' => 'Learn more about S. M. Mominul Haque (Sejan), his engineering background, software architecture philosophy, and technology publication.',
            'isPartOf' => [
                '@type' => 'WebSite',
                '@id' => route('home') . '#website',
                'name' => 'Sejan · Blog',
                'url' => route('home'),
            ],
            'mainEntity' => [
                '@type' => 'Person',
                '@id' => route('blog.about') . '#author',
                'name' => 'S. M. Mominul Haque (Sejan)',
                'alternateName' => 'Sejan',
                'url' => route('blog.about'),
                'jobTitle' => 'Senior Software Engineer & Architect',
                'knowsAbout' => ['PHP', 'Laravel', 'Software Architecture', 'Linux', 'MySQL', 'JavaScript', 'Cloud Infrastructure'],
                'sameAs' => [
                    'https://www.linkedin.com/in/s-m-mominul-haque-sejan-79b77b83/',
                    'https://twitter.com/sejanH',
                    'https://github.com/sejanH',
                ],
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => route('blog.about') . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'About',
                    'item' => route('blog.about'),
                ],
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">
    <!-- Breadcrumb & Header -->
    <section class="glass-card rounded-3xl p-6 sm:p-8 text-center shadow-xs">
        <h1 class="text-2xl sm:text-4xl font-extrabold leading-tight text-slate-900 mb-3">
            About Sejan
        </h1>
        <div class="flex items-center justify-center gap-2 text-xs sm:text-sm font-medium text-emerald-700">
            <a href="{{ route('home') }}" class="hover:text-emerald-800 transition">Home</a>
            <span class="text-slate-400">›</span>
            <span class="text-slate-600">About</span>
        </div>
    </section>

    <!-- Bio Card -->
    <section class="glass-card rounded-3xl p-8 sm:p-10 shadow-xs flex flex-col sm:flex-row items-center gap-8">
        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-gradient-to-br from-emerald-400 to-teal-600 text-white font-extrabold text-3xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/20">
            sz
        </div>
        <div class="space-y-3 text-center sm:text-left">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span>Software Engineer &amp; Architect</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                Hey, I'm Sejan 👋
            </h2>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                Welcome to my personal tech publication where I write about full-stack software development, modern Laravel, Linux administration, Node.js, and clean engineering practices.
            </p>
        </div>
    </section>

    <!-- Platform Purpose & Architecture Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="glass-card rounded-3xl p-7 shadow-xs space-y-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 font-bold text-lg">
                ⚡
            </div>
            <h3 class="text-lg font-bold text-slate-900">Modern Architecture</h3>
            <p class="text-sm text-slate-600 leading-relaxed">
                Engineered with high performance in mind, featuring lightning-fast responses, zero bloated CDN scripts, and clean Tailwind CSS design.
            </p>
        </div>

        <div class="glass-card rounded-3xl p-7 shadow-xs space-y-3">
            <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 font-bold text-lg">
                💡
            </div>
            <h3 class="text-lg font-bold text-slate-900">Continuous Sharing</h3>
            <p class="text-sm text-slate-600 leading-relaxed">
                Documenting real-world bug fixes, server troubleshooting guides, and tutorials from everyday engineering challenges.
            </p>
        </div>
    </div>
</div>
@endsection
