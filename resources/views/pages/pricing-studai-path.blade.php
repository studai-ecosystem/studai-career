{{--
    StudAI Hire — Pricing Page
    Simple, transparent pricing for job seekers and employers
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- SEO Meta Tags --}}
    <title>Pricing — Simple Plans for Job Seekers & Employers | StudAI Hire</title>
    <meta name="description" content="Start free. Upgrade as you grow. Transparent pricing for job seekers and employers. No hidden fees. Cancel anytime.">
    <meta name="keywords" content="StudAI Hire pricing, career OS pricing, ATS pricing, job search subscription">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="Simple Pricing. Powerful Results. | StudAI Hire">
    <meta property="og:description" content="Start free. Scale as you grow. No hidden fees.">
    <meta property="og:type" content="website">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Plan card hover lift animation */
        .plan-card {
            transition: transform 0.28s cubic-bezier(.34,1.56,.64,1), box-shadow 0.28s ease;
        }
        .plan-card:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 20px 48px rgba(99,102,241,.15), 0 8px 16px rgba(0,0,0,.08);
        }
        /* Colored outline button variants */
        .btn-free {
            display:inline-flex;align-items:center;justify-content:center;
            gap:0.5rem;font-weight:600;border-radius:0.5rem;
            transition:all 0.2s ease;cursor:pointer;
            padding:0.625rem 1.25rem;font-size:0.875rem;
            background:linear-gradient(135deg,#374151,#1f2937);
            color:#fff;border:none;width:100%;margin-bottom:2rem;
        }
        .btn-free:hover { background:linear-gradient(135deg,#4b5563,#374151); transform:scale(0.98); box-shadow:0 4px 14px rgba(31,41,55,.35); }
        .btn-executive {
            display:inline-flex;align-items:center;justify-content:center;
            gap:0.5rem;font-weight:600;border-radius:0.5rem;
            transition:all 0.2s ease;cursor:pointer;
            padding:0.625rem 1.25rem;font-size:0.875rem;
            background:linear-gradient(135deg,#c9941a,#e3b62f);
            color:#fff;border:none;width:100%;margin-bottom:2rem;
        }
        .btn-executive:hover { background:linear-gradient(135deg,#b3830f,#c9941a); transform:scale(0.98); box-shadow:0 4px 14px rgba(227,182,47,.4); }
        .btn-starter {
            display:inline-flex;align-items:center;justify-content:center;
            gap:0.5rem;font-weight:600;border-radius:0.5rem;
            transition:all 0.2s ease;cursor:pointer;
            padding:0.625rem 1.25rem;font-size:0.875rem;
            background:linear-gradient(135deg,#374151,#1f2937);
            color:#fff;border:none;width:100%;margin-bottom:2rem;
        }
        .btn-starter:hover { background:linear-gradient(135deg,#4b5563,#374151); transform:scale(0.98); box-shadow:0 4px 14px rgba(31,41,55,.35); }
        .btn-enterprise {
            display:inline-flex;align-items:center;justify-content:center;
            gap:0.5rem;font-weight:600;border-radius:0.5rem;
            transition:all 0.2s ease;cursor:pointer;
            padding:0.625rem 1.25rem;font-size:0.875rem;
            background:linear-gradient(135deg,#0f172a,#1e293b);
            color:#fff;border:none;width:100%;margin-bottom:2rem;
        }
        .btn-enterprise:hover { background:linear-gradient(135deg,#1e293b,#0f172a); transform:scale(0.98); box-shadow:0 4px 14px rgba(15,23,42,.4); }
        /* Colored plan card themes */
        .card-free   { background: linear-gradient(145deg, #f0fdf4, #dcfce7, #d1fae5); border-color: #86efac; }
        .card-pro    { background: linear-gradient(145deg, #eff6ff, #dbeafe, #e0e7ff); border-color: #93c5fd; }
        .card-exec   { background: linear-gradient(145deg, #fffdf5, #fbf2d6, #fdf6e3); border-color: #e3b62f; }
        .card-starter{ background: linear-gradient(145deg, #fff7ed, #ffedd5, #fef3c7); border-color: #fbbf24; }
        .card-growth { background: linear-gradient(145deg, #eff6ff, #dbeafe, #e0e7ff); border-color: #3b82f6; }
        .card-enterprise{ background: linear-gradient(145deg, #07121d, #0c1c2c, #14283d); border-color: #2f5fb0; }
        .card-enterprise h3, .card-enterprise p, .card-enterprise span { color: #e2e8f0 !important; }
        .card-enterprise .price-label { color: #a9c4ec !important; }
        .plan-icon { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:12px;flex-shrink:0; }
        .icon-free    { background:linear-gradient(135deg,#22c55e,#16a34a); }
        .icon-pro     { background:linear-gradient(135deg,#3a6bc0,#2f5fb0); }
        .icon-exec    { background:linear-gradient(135deg,#e3b62f,#c9941a); }
        .icon-starter { background:linear-gradient(135deg,#f59e0b,#ea580c); }
        .icon-growth  { background:linear-gradient(135deg,#3a6bc0,#2f5fb0); }
        .icon-ent     { background:linear-gradient(135deg,#2f5fb0,#1c344d); }
        .price-free   { background:linear-gradient(135deg,#16a34a,#15803d); -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text; }
        .price-pro    { background:linear-gradient(135deg,#2f5fb0,#1f3f7a); -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text; }
        .price-exec   { background:linear-gradient(135deg,#c9941a,#b3830f); -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text; }
        .price-starter{ background:linear-gradient(135deg,#b45309,#d97706); -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text; }
        .price-growth { background:linear-gradient(135deg,#2f5fb0,#3a6bc0); -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text; }
        .check-free   { color:#16a34a !important; }
        .check-pro    { color:#2f5fb0 !important; }
        .check-exec   { color:#c9941a !important; }
        .check-starter{ color:#d97706 !important; }
        .check-growth { color:#2f5fb0 !important; }
        .check-ent    { color:#a9c4ec !important; }
        .card-enterprise li { color:#cbd5e1 !important; }
        /* Fade-in-up animation for plan cards */
        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(24px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .plan-card { animation: fadeInUp 0.5s ease both; }
        .plan-card:nth-child(1) { animation-delay:0.05s; }
        .plan-card:nth-child(2) { animation-delay:0.15s; }
        .plan-card:nth-child(3) { animation-delay:0.25s; }
        /* Badge pulse on popular card */
        .popular-badge { animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(26,115,232,.4);} 50%{box-shadow:0 0 0 8px rgba(26,115,232,0);} }
        /* "Best for" tag under plan name */
        .best-for { display:inline-flex;align-items:center;gap:6px;font-size:0.7rem;font-weight:600;letter-spacing:.02em;text-transform:uppercase;padding:4px 10px;border-radius:999px;margin-top:8px; }
        .best-for-free { background:rgba(22,163,74,.12); color:#15803d; }
        .best-for-pro  { background:rgba(47,95,176,.12); color:#2f5fb0; }
        .best-for-exec { background:rgba(227,182,47,.16); color:#b3830f; }
        /* Credit allotment pill */
        .credit-pill { display:flex;align-items:center;gap:8px;font-size:0.8rem;font-weight:600;padding:8px 12px;border-radius:10px;margin-bottom:18px; }
        .credit-pill-free { background:rgba(22,163,74,.10); color:#15803d; border:1px solid rgba(22,163,74,.25); }
        .credit-pill-pro  { background:rgba(47,95,176,.10); color:#2f5fb0; border:1px solid rgba(47,95,176,.25); }
        .credit-pill-exec { background:rgba(227,182,47,.12); color:#b3830f; border:1px solid rgba(227,182,47,.30); }
        /* Value highlight strip */
        .value-strip { display:flex;align-items:flex-start;gap:8px;font-size:0.8rem;font-weight:500;line-height:1.35;padding:10px 12px;border-radius:10px;margin-bottom:18px;background:rgba(255,255,255,.55); }
        .value-strip-free { color:#166534; }
        .value-strip-pro  { color:#1e40af; }
        .value-strip-exec { color:#8a6d12; }
        /* Comparison table */
        .compare-table { width:100%;border-collapse:separate;border-spacing:0;font-size:0.875rem; }
        .compare-table th, .compare-table td { padding:14px 16px;text-align:center;border-bottom:1px solid #e5e7eb; }
        .compare-table th:first-child, .compare-table td:first-child { text-align:left;font-weight:600;color:#374151; }
        .compare-table thead th { font-size:0.95rem;font-weight:700;color:#1f2937; }
        .compare-table thead th.col-pro { color:#2f5fb0; }
        .compare-table tbody tr:hover { background:#f8fafc; }
        .compare-table .col-pro { background:rgba(37,99,235,.04); }
        .compare-yes { color:#16a34a;font-weight:700; }
        .compare-no  { color:#cbd5e1; }
    </style>
</head>
@php
    $defaultTab = 'seekers';
    if(auth()->check() && auth()->user()->isEmployer()) $defaultTab = 'employers';
@endphp
<body class="font-sans antialiased bg-white text-ink-primary" x-data="{ tab: '{{ $defaultTab }}' }">
    {{-- Navigation --}}
    @include('partials.nav-marketing')

    <main>
        {{-- Hero --}}
        <section class="pt-32 pb-12 bg-canvas-subtle">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-ink-primary mb-6">
                    Simple Pricing. <span class="text-gradient">Powerful Results.</span>
                </h1>
                <p class="text-xl text-ink-secondary max-w-2xl mx-auto mb-8">
                    Start free. Scale as you grow. No hidden fees.
                </p>
                
                {{-- Toggle: hide for authenticated job seekers --}}
                @if(!auth()->check() || !auth()->user()->isJobSeeker())
                <div class="inline-flex items-center bg-surface-100 rounded-full p-1 mb-12">
                    <button 
                        @click="tab = 'seekers'" 
                        :class="tab === 'seekers' ? 'bg-white shadow-sm text-ink-primary' : 'text-ink-secondary'"
                        class="px-6 py-2 rounded-full text-sm font-medium transition-all">
                        For Job Seekers
                    </button>
                    <button 
                        @click="tab = 'employers'" 
                        :class="tab === 'employers' ? 'bg-white shadow-sm text-ink-primary' : 'text-ink-secondary'"
                        class="px-6 py-2 rounded-full text-sm font-medium transition-all">
                        For Employers
                    </button>
                </div>
                @else
                <div class="mb-12"></div>
                @endif
            </div>
        </section>

        {{-- Job Seeker Plans --}}
        <section x-show="tab === 'seekers'" class="py-16 bg-canvas-subtle">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-8">
                    {{-- Free Plan --}}
                    <div class="plan-card card-free rounded-2xl border shadow-card p-8">
                        <div class="plan-icon icon-free">🚀</div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-1">Free</h3>
                            <p class="text-sm text-gray-500">Everything you need to start your job search.</p>
                            <span class="best-for best-for-free">★ Best for getting started</span>
                        </div>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold price-free">₹0</span>
                            <span class="text-gray-500 text-sm">/forever</span>
                        </div>
                        <div class="credit-pill credit-pill-free">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            30 credits / month included
                        </div>
                        <div class="value-strip value-strip-free">
                            <span>⏱</span>
                            <span>Save hours of manual applying — test-drive AI job search risk-free, no card required.</span>
                        </div>
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-free">
                                Get Started Free
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-free">
                                Get Started Free
                            </a>
                        @endauth
                        <ul class="space-y-3">
                            @foreach(['Unlimited job search', 'Basic resume builder', '5 AI interview sessions/month', 'Application tracking', 'Basic salary insights'] as $feature)
                            <li class="flex items-start gap-3 text-sm text-gray-700">
                                <svg class="w-5 h-5 check-free flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Pro Plan --}}
                    <div class="plan-card card-pro rounded-2xl border-2 shadow-elevation-3 p-8 relative" style="border-color:#3b82f6;">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="popular-badge px-4 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">Most Popular</span>
                        </div>
                        <div class="plan-icon icon-pro">⚡</div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-1">Pro</h3>
                            <p class="text-sm text-gray-500">Accelerate your career with AI automation.</p>
                            <span class="best-for best-for-pro">★ Best for active job hunters</span>
                        </div>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold price-pro">₹499</span>
                            <span class="text-gray-500 text-sm">/month</span>
                        </div>
                        <div class="credit-pill credit-pill-pro">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            50 credits / day &mdash; up to 1,500 applications a month
                        </div>
                        <div class="value-strip value-strip-pro">
                            <span>🎯</span>
                            <span>Get 3&times; more interview calls — let the AI agent apply, tailor resumes &amp; negotiate while you sleep.</span>
                        </div>
                        @auth
                            <a href="{{ route('subscriptions.select-plan', ['plan_id' => 4]) }}" class="studai-btn studai-btn-primary w-full mb-8">
                                Start Pro Trial
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="studai-btn studai-btn-primary w-full mb-8">
                                Start Pro Trial
                            </a>
                        @endauth
                        <ul class="space-y-3">
                            @foreach(['Everything in Free', 'Autonomous Agent (50 credits/day)', 'Unlimited AI interviews', 'Premium resume templates', 'Salary negotiation coach', 'Priority support'] as $feature)
                            <li class="flex items-start gap-3 text-sm text-gray-700">
                                <svg class="w-5 h-5 check-pro flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Executive Plan --}}
                    <div class="plan-card card-exec rounded-2xl border shadow-card p-8">
                        <div class="plan-icon icon-exec">👑</div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-1">Executive</h3>
                            <p class="text-sm text-gray-500">For senior professionals and career changers.</p>
                            <span class="best-for best-for-exec">★ Best for senior &amp; leadership roles</span>
                        </div>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold price-exec">₹1,999</span>
                            <span class="text-gray-500 text-sm">/month</span>
                        </div>
                        <div class="credit-pill credit-pill-exec">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Unlimited credits &mdash; no daily caps
                        </div>
                        <div class="value-strip value-strip-exec">
                            <span>👑</span>
                            <span>White-glove service — human career coaches, expert-written resumes &amp; a dedicated success manager.</span>
                        </div>
                        @auth
                            <a href="{{ route('subscriptions.select-plan', ['plan_id' => 7]) }}" class="btn-executive">
                                Go Executive
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-executive">
                                Go Executive
                            </a>
                        @endauth
                        <ul class="space-y-3">
                            @foreach(['Everything in Pro', 'Unlimited agent applications', 'Executive resume service', '1:1 career coaching sessions', 'LinkedIn profile optimization', 'Dedicated success manager'] as $feature)
                            <li class="flex items-start gap-3 text-sm text-gray-700">
                                <svg class="w-5 h-5 check-exec flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- Credits Explainer (job seekers) --}}
        <section x-show="tab === 'seekers'" class="py-16 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="inline-block text-xs font-semibold tracking-wide uppercase text-google-blue-600 mb-3">How it works</span>
                    <h2 class="text-3xl font-bold text-ink-primary mb-4">What are credits?</h2>
                    <p class="text-lg text-ink-secondary max-w-2xl mx-auto">
                        Credits power your AI career assistant. <strong>1 credit = 1 autonomous action</strong> — like the agent applying to a job, tailoring your resume, or running a salary negotiation. The more credits you have, the more the AI does for you.
                    </p>
                </div>
                <div class="grid sm:grid-cols-3 gap-6">
                    <div class="rounded-2xl border border-surface-200 bg-surface-50 p-6 text-center">
                        <div class="text-3xl mb-2">📝</div>
                        <h3 class="font-semibold text-ink-primary mb-1">1 credit / application</h3>
                        <p class="text-sm text-ink-secondary">Each auto-applied job, with a resume tailored to that role, uses a single credit.</p>
                    </div>
                    <div class="rounded-2xl border border-surface-200 bg-surface-50 p-6 text-center">
                        <div class="text-3xl mb-2">🤝</div>
                        <h3 class="font-semibold text-ink-primary mb-1">AI actions count too</h3>
                        <p class="text-sm text-ink-secondary">Negotiation coaching, deep resume rewrites and outreach messages each spend credits.</p>
                    </div>
                    <div class="rounded-2xl border border-surface-200 bg-surface-50 p-6 text-center">
                        <div class="text-3xl mb-2">🔄</div>
                        <h3 class="font-semibold text-ink-primary mb-1">Refilled automatically</h3>
                        <p class="text-sm text-ink-secondary">Pro credits refresh every day; Executive removes daily caps entirely.</p>
                    </div>
                </div>
                <div class="grid sm:grid-cols-3 gap-6 mt-6">
                    <div class="rounded-xl border-2 border-green-200 bg-green-50 p-5 text-center">
                        <p class="text-sm font-semibold text-green-700">Free</p>
                        <p class="text-2xl font-extrabold text-green-700">30 <span class="text-base font-semibold">credits/mo</span></p>
                    </div>
                    <div class="rounded-xl border-2 border-blue-300 bg-blue-50 p-5 text-center">
                        <p class="text-sm font-semibold text-blue-700">Pro</p>
                        <p class="text-2xl font-extrabold text-blue-700">50 <span class="text-base font-semibold">credits/day</span></p>
                    </div>
                    <div class="rounded-xl border-2 border-purple-200 bg-purple-50 p-5 text-center">
                        <p class="text-sm font-semibold text-purple-700">Executive</p>
                        <p class="text-2xl font-extrabold text-purple-700">Unlimited</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Plan Comparison (job seekers) --}}
        <section x-show="tab === 'seekers'" class="py-16 bg-canvas-subtle">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-ink-primary text-center mb-3">Compare plans at a glance</h2>
                <p class="text-center text-ink-secondary mb-10">See exactly what you unlock as you upgrade.</p>
                <div class="overflow-x-auto rounded-2xl border border-surface-200 bg-white shadow-card">
                    <table class="compare-table">
                        <thead>
                            <tr>
                                <th>Feature</th>
                                <th>Free</th>
                                <th class="col-pro">Pro</th>
                                <th>Executive</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Autonomous job applications</td>
                                <td>30 / month</td>
                                <td class="col-pro">50 / day</td>
                                <td>Unlimited</td>
                            </tr>
                            <tr>
                                <td>AI mock interviews</td>
                                <td>5 / month</td>
                                <td class="col-pro">Unlimited</td>
                                <td>Unlimited</td>
                            </tr>
                            <tr>
                                <td>Resume builder</td>
                                <td>Basic</td>
                                <td class="col-pro">Premium templates</td>
                                <td>Expert-written service</td>
                            </tr>
                            <tr>
                                <td>Salary negotiation coach</td>
                                <td><span class="compare-no">—</span></td>
                                <td class="col-pro"><span class="compare-yes">✓</span></td>
                                <td><span class="compare-yes">✓</span></td>
                            </tr>
                            <tr>
                                <td>1:1 human career coaching</td>
                                <td><span class="compare-no">—</span></td>
                                <td class="col-pro"><span class="compare-no">—</span></td>
                                <td><span class="compare-yes">✓</span></td>
                            </tr>
                            <tr>
                                <td>LinkedIn profile optimization</td>
                                <td><span class="compare-no">—</span></td>
                                <td class="col-pro"><span class="compare-no">—</span></td>
                                <td><span class="compare-yes">✓</span></td>
                            </tr>
                            <tr>
                                <td>Support</td>
                                <td>Community</td>
                                <td class="col-pro">Priority</td>
                                <td>Dedicated manager</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Employer Plans: only shown to employers and guests, never to job seekers --}}
        @if(!auth()->check() || !auth()->user()->isJobSeeker())
        <section x-show="tab === 'employers'" class="py-16 bg-canvas-subtle" x-cloak>
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-8">
                    {{-- Starter --}}
                    <div class="plan-card card-starter rounded-2xl border shadow-card p-8">
                        <div class="plan-icon icon-starter">🏢</div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-1">Starter</h3>
                            <p class="text-sm text-gray-500">For small teams making their first hires.</p>
                        </div>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold price-starter">₹0</span>
                            <span class="text-gray-500 text-sm">/forever</span>
                        </div>
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-starter">
                                Start Hiring Free
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-starter">
                                Start Hiring Free
                            </a>
                        @endauth
                        <ul class="space-y-3">
                            @foreach(['3 active job posts', 'Basic candidate management', 'Email notifications', 'Standard job board distribution'] as $feature)
                            <li class="flex items-start gap-3 text-sm text-gray-700">
                                <svg class="w-5 h-5 check-starter flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Growth --}}
                    <div class="plan-card card-growth rounded-2xl border-2 shadow-elevation-3 p-8 relative" style="border-color:#3b82f6;">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="popular-badge px-4 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">Best Value</span>
                        </div>
                        <div class="plan-icon icon-growth">📈</div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-1">Growth</h3>
                            <p class="text-sm text-gray-500">For growing companies scaling their teams.</p>
                        </div>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold price-growth">₹4,999</span>
                            <span class="text-gray-500 text-sm">/month</span>
                        </div>
                        @auth
                            <a href="{{ route('dashboard') }}" class="studai-btn studai-btn-primary w-full mb-8">
                                Start Growth Trial
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="studai-btn studai-btn-primary w-full mb-8">
                                Start Growth Trial
                            </a>
                        @endauth
                        <ul class="space-y-3">
                            @foreach(['Unlimited job posts', 'S.C.O.U.T. AI screening', 'Team collaboration tools', '50+ job board distribution', 'Interview scheduling', 'Basic analytics'] as $feature)
                            <li class="flex items-start gap-3 text-sm text-gray-700">
                                <svg class="w-5 h-5 check-growth flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Enterprise --}}
                    <div class="plan-card card-enterprise rounded-2xl border shadow-card p-8">
                        <div class="plan-icon icon-ent">🌐</div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold mb-1" style="color:#e2e8f0">Enterprise</h3>
                            <p class="text-sm" style="color:#94a3b8">For large organizations with complex needs.</p>
                        </div>
                        <div class="mb-6">
                            <span class=\"text-4xl font-extrabold\" style=\"color:#a9c4ec\">Custom</span>
                        </div>
                        <a href="{{ route('contact') }}" class="btn-enterprise">
                            Contact Sales
                        </a>
                        <ul class="space-y-3">
                            @foreach(['Everything in Growth', 'Custom integrations (HRIS, ATS)', 'Dedicated account manager', 'Advanced analytics & reporting', 'SSO & advanced security', 'Custom SLAs', 'On-premise deployment option'] as $feature)
                            <li class="flex items-start gap-3 text-sm" style="color:#cbd5e1">
                                <svg class="w-5 h-5 check-ent flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        @endif {{-- end employer plans block --}}

        {{-- Trust Note --}}
        <section class="py-8 bg-canvas-subtle">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <p class="text-sm text-ink-tertiary">
                    All plans include our core platform. No setup fees. Cancel anytime. 14-day free trial on all paid plans.
                </p>
            </div>
        </section>

        {{-- FAQ Section --}}
        <section class="py-20 bg-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-ink-primary text-center mb-12">Frequently Asked Questions</h2>
                
                <div class="space-y-4">
                    <details class="bg-surface-50 rounded-xl border border-surface-200 group" open>
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">Can I switch plans anytime?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            Yes. Upgrade or downgrade at any time. Changes take effect immediately, and we prorate accordingly.
                        </div>
                    </details>

                    <details class="bg-surface-50 rounded-xl border border-surface-200 group">
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">Is there a free trial for paid plans?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            Yes. All paid plans include a 14-day free trial. No credit card required to start.
                        </div>
                    </details>

                    <details class="bg-surface-50 rounded-xl border border-surface-200 group">
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">What payment methods do you accept?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            We accept all major credit cards, debit cards, UPI, and net banking. Enterprise plans can pay via invoice.
                        </div>
                    </details>

                    <details class="bg-surface-50 rounded-xl border border-surface-200 group">
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">What is a credit and how is it used?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            A credit is one autonomous AI action. Most actions — like the agent applying to a job with a tailored resume — cost a single credit. Free includes 30 credits/month, Pro gives you 50 credits/day, and Executive is unlimited.
                        </div>
                    </details>

                    <details class="bg-surface-50 rounded-xl border border-surface-200 group">
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">Do unused credits roll over?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            Pro credits refresh every day, so you always start fresh with a full 50. Free credits reset monthly. With Executive there are no caps to worry about — apply and automate as much as you need.
                        </div>
                    </details>

                    <details class="bg-surface-50 rounded-xl border border-surface-200 group">
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">Is my data secure?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            Absolutely. We use bank-grade encryption, are SOC 2 compliant, and never sell your data.
                        </div>
                    </details>
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="py-20 bg-gradient-to-br from-google-blue-600 to-purple-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                    Ready to put your career on autopilot?
                </h2>
                <p class="text-lg text-white/80 mb-8">
                    Start free today. No credit card required.
                </p>
                <a href="{{ route('register') }}" class="studai-btn bg-white text-google-blue-600 hover:bg-gray-100 studai-btn-xl">
                    Get Started Free
                </a>
            </div>
        </section>
    </main>

    @include('partials.footer-marketing')
</body>
</html>
