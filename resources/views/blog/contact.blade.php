@extends('layouts.blog')

@section('title', 'Contact Sejan — Software Engineer & Architect')
@section('meta_description', 'Get in touch with S. M. Mominul Haque (Sejan) for software architecture inquiries, engineering consulting, collaboration, or technical discussion.')
@section('canonical_url', route('blog.contact'))

@section('schema_json')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'ContactPage',
            '@id' => route('blog.contact') . '#webpage',
            'url' => route('blog.contact'),
            'name' => 'Contact Sejan — Software Engineer & Architect',
            'description' => 'Get in touch with S. M. Mominul Haque (Sejan) for software architecture inquiries, engineering consulting, collaboration, or technical discussion.',
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
                'url' => route('blog.about'),
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => route('blog.contact') . '#breadcrumb',
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
                    'name' => 'Contact',
                    'item' => route('blog.contact'),
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
            Contact Me
        </h1>
        <div class="flex items-center justify-center gap-2 text-xs sm:text-sm font-medium text-emerald-700">
            <a href="{{ route('home') }}" class="hover:text-emerald-800 transition">Home</a>
            <span class="text-slate-400">›</span>
            <span class="text-slate-600">Contact</span>
        </div>
    </section>

    <!-- Contact Form Card -->
    <section class="glass-card rounded-3xl p-6 sm:p-10 shadow-xs space-y-6">
        @if (session('success'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('blog.contact.send') }}" method="POST" class="space-y-5">
            @csrf
            @honeypot
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        required
                        value="{{ old('name') }}"
                        placeholder="Your full name"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none transition shadow-2xs"
                    />
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        required
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none transition shadow-2xs"
                    />
                </div>
            </div>

            <div>
                <label for="subject" class="block text-xs font-semibold text-slate-700 mb-1.5">Subject</label>
                <input
                    type="text"
                    name="subject"
                    id="subject"
                    value="{{ old('subject') }}"
                    placeholder="What is this about?"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none transition shadow-2xs"
                />
            </div>

            <div>
                <label for="message" class="block text-xs font-semibold text-slate-700 mb-1.5">Message <span class="text-red-500">*</span></label>
                <textarea
                    name="message"
                    id="message"
                    rows="5"
                    required
                    placeholder="Your message..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-500 focus:bg-white focus:outline-none transition shadow-2xs"
                >{{ old('message') }}</textarea>
            </div>

            <button
                type="submit"
                class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold text-sm rounded-full shadow-md shadow-emerald-500/20 hover:opacity-95 hover:scale-105 transition"
            >
                Send Message
            </button>
        </form>
    </section>
</div>
@endsection
