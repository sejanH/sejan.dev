@extends('layouts.blog')

@section('title', 'Privacy Policy — Sejan · Blog')
@section('meta_description', 'Privacy Policy for Sejan.dev detailing data collection, cookie usage, Google AdSense, Google Analytics, and user privacy rights.')
@section('canonical_url', route('blog.privacy'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">
    <!-- Breadcrumb & Header -->
    <section class="glass-card rounded-3xl p-6 sm:p-8 text-center shadow-xs">
        <h1 class="text-2xl sm:text-4xl font-extrabold leading-tight text-slate-900 mb-3">
            Privacy Policy
        </h1>
        <div class="flex items-center justify-center gap-2 text-xs sm:text-sm font-medium text-emerald-700">
            <a href="{{ route('home') }}" class="hover:text-emerald-800 transition">Home</a>
            <span class="text-slate-400">›</span>
            <span class="text-slate-600">Privacy Policy</span>
        </div>
        <p class="text-xs text-slate-500 mt-3">Last Updated: {{ date('F d, Y') }}</p>
    </section>

    <!-- Content Card -->
    <section class="glass-card rounded-3xl p-8 sm:p-12 shadow-xs">
        <article class="article-prose space-y-6">
            <p>
                At <strong>Sejan.dev</strong> (accessible from <a href="{{ route('home') }}">https://sejan.dev</a> and <a href="{{ route('home') }}">https://blog.sejan.dev</a>), the privacy of our visitors is of utmost importance to us. This Privacy Policy document outlines the types of personal information that is received and collected by Sejan.dev and how it is used.
            </p>

            <h2>1. Information We Collect</h2>
            <p>
                When you visit or interact with our website, we may collect information in several ways:
            </p>
            <ul>
                <li><strong>Contact &amp; Feedback Data:</strong> If you submit a message through our Contact form, we collect your name, email address, message contents, and IP address solely to respond to your inquiry and prevent spam.</li>
                <li><strong>Comments Data:</strong> When leaving comments on articles, we collect the submitted name, email address, website (optional), and comment text.</li>
                <li><strong>Log Files:</strong> Like standard web servers, we use log files. These include IP addresses, browser types, Internet Service Providers (ISP), referring/exit pages, date/time stamps, and platform type to analyze trends and administer the site. Log files are not linked to personally identifiable information.</li>
            </ul>

            <h2>2. Cookies and Web Beacons</h2>
            <p>
                Sejan.dev uses cookies to store information about visitors' preferences, record user-specific information on which pages the user accesses, and customize web page content based on visitors' browser type or other information sent via their browser.
            </p>

            <h2>3. Google DoubleClick DART Cookie &amp; Google AdSense</h2>
            <p>
                Google is one of our third-party vendors on our site. It uses cookies, known as DART cookies, to serve ads to our site visitors based upon their visit to Sejan.dev and other sites on the internet.
            </p>
            <ul>
                <li>Third-party vendors, including Google, use cookies to serve ads based on a user's prior visits to your website or other websites.</li>
                <li>Google's use of advertising cookies enables it and its partners to serve ads to your users based on their visit to your sites and/or other sites on the Internet.</li>
                <li>Visitors may opt out of personalized advertising by visiting <a href="https://adssettings.google.com/" target="_blank" rel="noopener noreferrer">Google Ads Settings</a>.</li>
                <li>Alternatively, you can opt out of third-party vendor cookies for personalized advertising by visiting <a href="https://www.aboutads.info/choices/" target="_blank" rel="noopener noreferrer">aboutads.info</a>.</li>
            </ul>

            <h2>4. Google Analytics</h2>
            <p>
                We use Google Analytics (Google tag / gtag.js) to measure website traffic and understand visitor interactions anonymously. Google Analytics collects information such as how often users visit this site, what pages they visit, and what other sites they used prior to coming to this site. We do not combine the information collected through Google Analytics with personally identifiable information.
            </p>

            <h2>5. Third-Party Advertising Partners</h2>
            <p>
                Some of our advertising partners may use cookies and web beacons on our site. Our advertising partners include:
            </p>
            <ul>
                <li><strong>Google AdSense</strong> (<a href="https://policies.google.com/technologies/ads" target="_blank" rel="noopener noreferrer">Privacy Policy</a>)</li>
            </ul>
            <p>
                These third-party ad servers or ad networks use technology in their respective advertisements and links that appear on Sejan.dev, which are sent directly to your browser. They automatically receive your IP address when this occurs. Other technologies (such as cookies, JavaScript, or Web Beacons) may also be used by the third-party ad networks to measure the effectiveness of their advertisements and / or to personalize the advertising content that you see.
            </p>
            <p>
                <em>Note: Sejan.dev has no access to or control over these cookies that are used by third-party advertisers.</em>
            </p>

            <h2>6. GDPR Compliance &amp; Your Data Rights</h2>
            <p>If you are a resident of the European Economic Area (EEA), you have certain data protection rights:</p>
            <ul>
                <li>The right to access, update, or delete the information we have on you.</li>
                <li>The right of rectification if your information is inaccurate or incomplete.</li>
                <li>The right to object to our processing of your personal data.</li>
                <li>The right to data portability and withdrawal of consent.</li>
            </ul>

            <h2>7. CCPA / CPRA Privacy Rights (Do Not Sell My Personal Information)</h2>
            <p>Under the California Consumer Privacy Act (CCPA), California consumers have the right to request disclosure of categories and specific pieces of personal data collected, request deletion, and request that a business not sell the consumer's personal data. We do not sell personal data.</p>

            <h2>8. Children's Information</h2>
            <p>
                Protecting the privacy of young children is especially important. Sejan.dev does not knowingly collect any Personal Identifiable Information from children under the age of 13. If you think that your child provided this kind of information on our website, we strongly encourage you to contact us immediately and we will promptly remove such information from our records.
            </p>

            <h2>9. Contact Us</h2>
            <p>
                If you have any questions or require more information about our Privacy Policy, please contact us via our <a href="{{ route('blog.contact') }}">Contact Page</a> or email us at <code>sejan840@protonmail.com</code>.
            </p>
        </article>
    </section>
</div>
@endsection
