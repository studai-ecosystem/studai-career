@extends('layouts.marketing')

@section('title', 'Cookie Policy — StudAI Hire')

@section('content')
{{-- Hero --}}
<section class="bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-amber-100 px-6 py-3 rounded-full border border-amber-300 mb-8">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
            <span class="text-sm font-medium text-amber-800">Last Updated: December 1, 2024</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">Cookie Policy</h1>
        <p class="text-xl text-gray-600 leading-relaxed max-w-2xl mx-auto">
            This policy explains how StudAI Hire uses cookies and similar tracking technologies to improve your experience.
        </p>
    </div>
</section>

{{-- TOC --}}
<section class="py-8 bg-white border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Table of Contents</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            @foreach([
                ['What Are Cookies?', 'what-are-cookies'],
                ['Types of Cookies We Use', 'types-of-cookies'],
                ['Why We Use Cookies', 'why-we-use'],
                ['Third-Party Cookies', 'third-party'],
                ['Managing Your Preferences', 'managing-cookies'],
                ['Cookie Retention', 'retention'],
                ['Changes to This Policy', 'policy-changes'],
                ['Contact Us', 'contact'],
            ] as [$title, $anchor])
                <a href="#{{ $anchor }}" class="text-indigo-600 hover:text-indigo-800 transition-colors">{{ $title }}</a>
            @endforeach
        </div>
    </div>
</section>

{{-- Content --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

        {{-- Intro --}}
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-8">
            <p class="text-lg text-gray-700 leading-relaxed mb-4">
                StudAI Hire ("we", "us", "our") uses cookies and similar tracking technologies to enhance your browsing experience, analyse site traffic, personalise content, and serve targeted advertisements. This Cookie Policy explains what cookies are, how we use them, and how you can control them.
            </p>
            <p class="text-gray-600 leading-relaxed">
                By continuing to use our website, you consent to the use of cookies in accordance with this policy unless you have adjusted your browser settings to refuse cookies.
            </p>
        </div>

        {{-- 1. What Are Cookies --}}
        <div id="what-are-cookies">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">1. What Are Cookies?</h2>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4">
                <p class="text-gray-600 mb-4">Cookies are small text files placed on your device (computer, smartphone, or tablet) when you visit a website. They allow the website to recognise your device and store information about your preferences or past actions.</p>
                <p class="text-gray-600">Similar technologies include web beacons, pixel tags, local storage, and session storage — all of which work similarly to cookies and are covered by this policy.</p>
            </div>
        </div>

        {{-- 2. Types of Cookies --}}
        <div id="types-of-cookies">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">2. Types of Cookies We Use</h2>
            <div class="space-y-4">
                @foreach([
                    ['bg-green-50', 'border-green-200', 'text-green-700', 'bg-green-100', 'Essential Cookies', 'Always Active', 'Required for the website to function. They enable core features like security, network management, and accessibility. You cannot opt out of these cookies.', ['Session management and authentication', 'Security tokens and CSRF protection', 'Load balancing and server routing', 'Remembering your cookie preferences']],
                    ['bg-blue-50', 'border-blue-200', 'text-blue-700', 'bg-blue-100', 'Performance & Analytics Cookies', 'Optional', 'Help us understand how visitors interact with our website by collecting and reporting information anonymously.', ['Google Analytics — page views, bounce rate, session duration', 'Error logging and performance monitoring', 'A/B testing and feature experiments', 'Heatmaps and user journey analysis']],
                    ['bg-purple-50', 'border-purple-200', 'text-purple-700', 'bg-purple-100', 'Functional Cookies', 'Optional', 'Enable enhanced functionality and personalisation. They may be set by us or third-party providers whose services we use.', ['Language and region preferences', 'Saved search filters and job preferences', 'Dashboard layout and theme settings', 'Recently viewed jobs and companies']],
                    ['bg-pink-50', 'border-pink-200', 'text-pink-700', 'bg-pink-100', 'Marketing Cookies', 'Optional', 'Used to track visitors across websites to display relevant advertisements and measure campaign effectiveness.', ['LinkedIn Insight Tag', 'Google Ads conversion tracking', 'Facebook Pixel', 'Retargeting and lookalike audiences']],
                ] as [$bg, $border, $text, $iconBg, $title, $badge, $desc, $examples])
                <div class="{{ $bg }} rounded-2xl border {{ $border }} p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                        <span class="{{ $iconBg }} {{ $text }} text-xs font-semibold px-3 py-1 rounded-full">{{ $badge }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3">{{ $desc }}</p>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                        @foreach($examples as $ex)<li>{{ $ex }}</li>@endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>

        {{-- 3. Why We Use Cookies --}}
        <div id="why-we-use">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">3. Why We Use Cookies</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach([
                    ['🔐', 'Security', 'Protect your account and prevent fraud'],
                    ['⚡', 'Performance', 'Ensure pages load quickly and reliably'],
                    ['🎯', 'Personalisation', 'Remember your preferences and settings'],
                    ['📊', 'Analytics', 'Understand how you use our platform'],
                    ['💼', 'Job Matching', 'Improve AI recommendations for your profile'],
                    ['📣', 'Marketing', 'Show you relevant job opportunities and offers'],
                ] as [$icon, $title, $desc])
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-start gap-4">
                    <span class="text-2xl">{{ $icon }}</span>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-1">{{ $title }}</h4>
                        <p class="text-sm text-gray-600">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- 4. Third-Party Cookies --}}
        <div id="third-party">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">4. Third-Party Cookies</h2>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4">
                <p class="text-gray-600 mb-4">Some cookies are placed by third-party services that appear on our pages. We use the following third-party services:</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-amber-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Service</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Purpose</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr><td class="px-4 py-3 text-gray-700">Google Analytics</td><td class="px-4 py-3 text-gray-600">Site analytics</td><td class="px-4 py-3 text-gray-600">Performance</td></tr>
                            <tr class="bg-gray-50"><td class="px-4 py-3 text-gray-700">Google Ads</td><td class="px-4 py-3 text-gray-600">Ad conversion tracking</td><td class="px-4 py-3 text-gray-600">Marketing</td></tr>
                            <tr><td class="px-4 py-3 text-gray-700">LinkedIn Insight</td><td class="px-4 py-3 text-gray-600">Professional ad targeting</td><td class="px-4 py-3 text-gray-600">Marketing</td></tr>
                            <tr class="bg-gray-50"><td class="px-4 py-3 text-gray-700">Facebook Pixel</td><td class="px-4 py-3 text-gray-600">Remarketing</td><td class="px-4 py-3 text-gray-600">Marketing</td></tr>
                            <tr><td class="px-4 py-3 text-gray-700">Razorpay</td><td class="px-4 py-3 text-gray-600">Payment processing</td><td class="px-4 py-3 text-gray-600">Essential</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 5. Managing Cookies --}}
        <div id="managing-cookies">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">5. Managing Your Cookie Preferences</h2>
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Our Cookie Consent Tool</h3>
                    <p class="text-gray-600 mb-4">You can manage your cookie preferences at any time using our consent banner. Click the button below to update your preferences:</p>
                    <button onclick="localStorage.removeItem('cookie_consent'); location.reload();"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
                            style="background:linear-gradient(135deg,#6366f1,#7c3aed)">
                        ⚙️ Manage Cookie Preferences
                    </button>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Browser Settings</h3>
                    <p class="text-gray-600 mb-3">Most browsers allow you to control cookies through their settings. Here's how to access cookie settings in popular browsers:</p>
                    <ul class="list-disc list-inside text-gray-600 space-y-2 text-sm">
                        <li><strong>Chrome</strong>: Settings → Privacy and Security → Cookies</li>
                        <li><strong>Firefox</strong>: Settings → Privacy & Security → Cookies and Site Data</li>
                        <li><strong>Safari</strong>: Preferences → Privacy → Manage Website Data</li>
                        <li><strong>Edge</strong>: Settings → Cookies and Site Permissions</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- 6. Retention --}}
        <div id="retention">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">6. Cookie Retention Periods</h2>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-amber-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Cookie Type</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr><td class="px-6 py-4 text-gray-700">Session cookies</td><td class="px-6 py-4 text-gray-600">Deleted when you close your browser</td></tr>
                        <tr class="bg-gray-50"><td class="px-6 py-4 text-gray-700">Authentication cookies</td><td class="px-6 py-4 text-gray-600">30 days (or until logout)</td></tr>
                        <tr><td class="px-6 py-4 text-gray-700">Preference cookies</td><td class="px-6 py-4 text-gray-600">1 year</td></tr>
                        <tr class="bg-gray-50"><td class="px-6 py-4 text-gray-700">Analytics cookies</td><td class="px-6 py-4 text-gray-600">2 years</td></tr>
                        <tr><td class="px-6 py-4 text-gray-700">Marketing cookies</td><td class="px-6 py-4 text-gray-600">90 days</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 7. Changes --}}
        <div id="policy-changes">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">7. Changes to This Policy</h2>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <p class="text-gray-600">We may update this Cookie Policy from time to time. When we make changes, we will update the "Last Updated" date at the top and notify you via a banner on our website. Your continued use of the site after changes constitutes acceptance of the updated policy.</p>
            </div>
        </div>

        {{-- 8. Contact --}}
        <div id="contact">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">8. Contact Us</h2>
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl border border-amber-200 p-8">
                <p class="text-gray-600 mb-6">If you have any questions about our use of cookies, please contact us:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Privacy Team</h3>
                        <div class="space-y-2 text-gray-600">
                            <p>Email: privacy@studai.careers</p>
                            <p>Phone: +91-80-4567-8900</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Postal Address</h3>
                        <div class="text-gray-600 text-sm space-y-1">
                            <p>StudAI Hire Private Limited</p>
                            <p>WeWork Prestige Atlanta</p>
                            <p>Koramangala, Bengaluru 560034</p>
                            <p>India</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('styles')
<style>html { scroll-behavior: smooth; }</style>
@endpush
