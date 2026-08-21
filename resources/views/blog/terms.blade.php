@extends('layouts.blog')

@section('title', 'Terms of Service & Disclaimer — Sejan · Blog')
@section('meta_description', 'Terms of Service, disclaimers, and terms of use for Sejan.dev technical articles and tutorials.')
@section('canonical_url', route('blog.terms'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">
    <!-- Breadcrumb & Header -->
    <section class="glass-card rounded-3xl p-6 sm:p-8 text-center shadow-xs">
        <h1 class="text-2xl sm:text-4xl font-extrabold leading-tight text-slate-900 mb-3">
            Terms of Service &amp; Disclaimer
        </h1>
        <div class="flex items-center justify-center gap-2 text-xs sm:text-sm font-medium text-emerald-700">
            <a href="{{ route('home') }}" class="hover:text-emerald-800 transition">Home</a>
            <span class="text-slate-400">›</span>
            <span class="text-slate-600">Terms &amp; Disclaimer</span>
        </div>
        <p class="text-xs text-slate-500 mt-3">Last Updated: {{ date('F d, Y') }}</p>
    </section>

    <!-- Content Card -->
    <section class="glass-card rounded-3xl p-8 sm:p-12 shadow-xs">
        <article class="article-prose space-y-6">
            <p>
                Welcome to <strong>Sejan.dev</strong>. By accessing or using this website, you agree to comply with and be bound by the following terms and conditions of use. If you disagree with any part of these terms, please discontinue use of this website.
            </p>

            <h2>1. Technical Tutorials and Code Disclaimers</h2>
            <p>
                All tutorials, code snippets, scripts, architecture diagrams, and technical recommendations provided on Sejan.dev are for <strong>educational and informational purposes only</strong>.
            </p>
            <ul>
                <li>While we strive to provide accurate, tested, and up-to-date technical solutions, technology and software dependencies evolve rapidly.</li>
                <li>Code samples and system administration commands are executed at your own discretion and risk.</li>
                <li>Always test commands and scripts in a staging or development environment before applying them to production systems.</li>
            </ul>

            <h2>2. Limitation of Liability</h2>
            <p>
                In no event shall Sejan.dev or its authors be liable for any direct, indirect, incidental, consequential, special, or exemplary damages (including, but not limited to, procurement of substitute goods or services, loss of use, data, or profits, or business interruption) arising in any way out of the use of this software, code, or information.
            </p>

            <h2>3. Intellectual Property and Content Usage</h2>
            <p>
                The content, design, and original code on this website are owned by S. M. Mominul Haque (Sejan) unless otherwise indicated.
            </p>
            <ul>
                <li>You are welcome to link to any article on this website.</li>
                <li>Code snippets may be freely used and adapted in your personal and commercial software projects under standard open-source fair-use principles.</li>
                <li>Full article republication or scraping without attribution and explicit canonical link is prohibited.</li>
            </ul>

            <h2>4. Comments and User Conduct</h2>
            <p>
                Visitors may post comments and feedback on articles. We reserve the right to review, edit, or remove any comment that is defamatory, abusive, offensive, contains unauthorized advertising, spam, or violates any laws.
            </p>

            <h2>5. External Links</h2>
            <p>
                Our blog contains links to external websites that are not operated or controlled by us. We have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.
            </p>

            <h2>6. Changes to Terms</h2>
            <p>
                We reserve the right to modify these terms at any time. Changes will be posted directly on this page with an updated revision date.
            </p>

            <h2>7. Contact Information</h2>
            <p>
                If you have any questions regarding these terms, please reach out via our <a href="{{ route('blog.contact') }}">Contact Page</a> or by emailing <code>admin@sejan.dev</code>.
            </p>
        </article>
    </section>
</div>
@endsection
