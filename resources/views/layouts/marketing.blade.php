<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $description ?? 'Discover your dream job with AI-powered matching. Smart resume optimization, interview prep, and personalized career guidance.' }}">
    <meta name="author" content="StudAI Hire">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <title>@yield('title', 'StudAI Hire — Your Career, On Autopilot')</title>

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'StudAI Hire — Your Career, On Autopilot')">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('meta')
    @stack('styles')

    <style>
        /* ── DESIGN TOKENS (MERIDIAN) ───────────── */
        :root {
            --brand:      var(--color-accent, #2D6CDF);
            --brand-dark: var(--color-accent-hover, #1B57C4);
            --brand-lite: var(--color-accent-subtle, #EBF2FF);
            --bg:         var(--color-canvas, #F7F7F5);
            --surface:    var(--color-surface, #FFFFFF);
            --border:     var(--color-border, #E2E2E0);
            --text:       var(--color-ink-1,#2D6CDF);
            --text-2:     var(--color-ink-3, #737373);
            --text-3:     var(--color-ink-4, #A8A8A8);
        }

        html { font-size:14px; }
        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        /* ── SCROLLBAR ──────────────────────────── */
        ::-webkit-scrollbar { width:8px; }
        ::-webkit-scrollbar-track { background:var(--color-canvas); }
        ::-webkit-scrollbar-thumb { background:var(--color-border-strong); border-radius:999px; }
        ::-webkit-scrollbar-thumb:hover { background:var(--color-ink-4); }

        /* ── KEYFRAMES ──────────────────────────── */
        @keyframes fadeUp   { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        @keyframes gradShift{ 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
        @keyframes floatY   { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }

        .mkt-fade-up { animation:fadeUp .6s ease both; }

        /* ── NAV ────────────────────────────────── */
        .nav-scrolled {
            background: var(--color-surface) !important;
            box-shadow: none !important;
            border-bottom: 1px solid var(--color-border) !important;
        }

        /* ── GRAD TEXT (MERIDIAN: solid accent) ─── */
        .grad-text {
            color: var(--color-accent);
            -webkit-text-fill-color: currentColor;
        }

        /* ── FOOTER ─────────────────────────────── */
        .footer-link { color:var(--text-3); font-size:.8rem; transition:color .2s; }
        .footer-link:hover { color:var(--brand); }
        .footer-dot { width:5px; height:5px; border-radius:50%; flex-shrink:0; margin-top:2px; }
    </style>
</head>
<body>

    {{-- ── NAVBAR ─────────────────────────────────── --}}
    <nav x-data="{ open: false, scrolled: false }"
         @scroll.window="scrolled = window.pageYOffset > 20"
         :class="scrolled ? 'nav-scrolled' : 'bg-white/80 backdrop-blur'"
         class="fixed w-full top-0 z-50 transition-all duration-300 border-b border-transparent"
         style="border-bottom-color: rgba(224,227,234,.7)">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <img src="/assets/logo/studai-hire-wordmark.svg?v=4" alt="StudAI Hire" style="height:30px;width:auto;object-fit:contain;flex-shrink:0" class="transition-transform group-hover:scale-110">
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center text-sm font-medium" style="gap:1.75rem">
                    <a href="{{ route('home') }}" class="transition-colors hover:text-[#2D6CDF]" style="color:#737373">Home</a>
                    @if(Route::has('features'))
                    <a href="{{ route('features') }}" class="transition-colors hover:text-[#2D6CDF]" style="color:#737373">Features</a>
                    @endif
                    @if(Route::has('pricing'))
                    <a href="{{ route('pricing') }}" class="transition-colors hover:text-[#2D6CDF]" style="color:#737373">Pricing</a>
                    @endif
                    @if(Route::has('about'))
                    <a href="{{ route('about') }}" class="transition-colors hover:text-[#2D6CDF]" style="color:#737373">About</a>
                    @endif
                    <a href="{{ route('contact') }}" class="transition-colors hover:text-[#2D6CDF]" style="color:#737373">Contact</a>

                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-white"
                           style="background:linear-gradient(135deg,#2D6CDF,#2D6CDF)">
                            Dashboard →
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold transition-colors hover:text-[#2D6CDF]" style="color:#737373">Sign in</a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-white"
                           style="background:linear-gradient(135deg,#2D6CDF,#2D6CDF)">
                            Get started free
                        </a>
                    @endauth
                </div>

                {{-- Mobile hamburger --}}
                <button @click="open = !open" class="md:hidden p-2 rounded-lg transition-colors hover:bg-[#EBF2FF]" style="color:#737373">
                    <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open"  class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t px-4 py-4 space-y-1"
             style="background:white; border-color:#E2E2E0">
            <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors hover:bg-[#EBF2FF] hover:text-[#0C2E72]" style="color:#737373">Home</a>
            @if(Route::has('features'))
            <a href="{{ route('features') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors hover:bg-[#EBF2FF] hover:text-[#0C2E72]" style="color:#737373">Features</a>
            @endif
            @if(Route::has('pricing'))
            <a href="{{ route('pricing') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors hover:bg-[#EBF2FF] hover:text-[#0C2E72]" style="color:#737373">Pricing</a>
            @endif
            @if(Route::has('about'))
            <a href="{{ route('about') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors hover:bg-[#EBF2FF] hover:text-[#0C2E72]" style="color:#737373">About</a>
            @endif
            <a href="{{ route('contact') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors hover:bg-[#EBF2FF] hover:text-[#0C2E72]" style="color:#737373">Contact</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block mt-2 px-3 py-2.5 rounded-xl text-sm font-semibold text-center text-white" style="background:linear-gradient(135deg,#2D6CDF,#2D6CDF)">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium transition-colors hover:bg-[#EBF2FF] hover:text-[#0C2E72]" style="color:#737373">Sign in</a>
                <a href="{{ route('register') }}" class="block mt-2 px-3 py-2.5 rounded-xl text-sm font-semibold text-center text-white" style="background:linear-gradient(135deg,#2D6CDF,#2D6CDF)">Get Started Free</a>
            @endauth
        </div>
    </nav>

    {{-- ── MAIN ────────────────────────────────────── --}}
    <main class="pt-16">
        @yield('content')
    </main>

    {{-- ── FOOTER ──────────────────────────────────── --}}
    <footer style="background:#F7F7F5; border-top:1px solid #E2E2E0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">

                {{-- Brand --}}
                <div class="col-span-1">
                    <div class="flex items-center gap-2.5 mb-4">
                        <img src="/assets/logo/studai-hire-wordmark.svg?v=4" alt="StudAI Hire" style="height:28px;width:auto;object-fit:contain;flex-shrink:0">
                    </div>
                    <p class="text-sm leading-relaxed mb-5" style="color:#737373">
                        India's first autonomous career OS. AI-powered job search, interview prep, and salary negotiation.
                        <strong style="color:#2D6CDF">Your Career. On Autopilot.</strong>
                    </p>
                    {{-- Social --}}
                    <div class="flex gap-2.5">
                        @foreach([
                            ['#','M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z'],
                            ['#','M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z'],
                            ['#','M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z']
                        ] as $s)
                        <a href="{{ $s[0] }}" class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:-translate-y-0.5 hover:shadow-md" style="background:white; border:1px solid #E2E2E0; color:#A8A8A8">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $s[1] }}"/></svg>
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest mb-4" style="color:#0C0C0C">Platform</h4>
                    <ul class="space-y-2.5">
                        @foreach([['Features',Route::has('features') ? route('features') : '#'],['Pricing',Route::has('pricing') ? route('pricing') : '#'],['About',Route::has('about') ? route('about') : '#'],['Contact',Route::has('contact') ? route('contact') : '#']] as $l)
                        <li><a href="{{ $l[1] }}" class="footer-link flex items-center gap-2"><span class="footer-dot" style="background:#BFCFEE"></span>{{ $l[0] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Job Seekers --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest mb-4" style="color:#0C0C0C">For Job Seekers</h4>
                    <ul class="space-y-2.5">
                        @foreach([['Browse Jobs',route('jobs.search')],['Resume Builder',route('resume.index')],['Interview Prep',route('interview.index')],['Career Coach',route('career-coach.index')]] as $l)
                        <li><a href="{{ $l[1] }}" class="footer-link flex items-center gap-2"><span class="footer-dot" style="background:#d8e1f1"></span>{{ $l[0] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Employers --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest mb-4" style="color:#0C0C0C">For Employers</h4>
                    <ul class="space-y-2.5">
                        @foreach([['Post a Job',route('employer.jobs.index')],['Find Talent','#'],['S.C.O.U.T. AI','#'],['Employer Pricing','#']] as $l)
                        <li><a href="{{ $l[1] }}" class="footer-link flex items-center gap-2"><span class="footer-dot" style="background:#f3e2ad"></span>{{ $l[0] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 pt-8" style="border-top:1px solid #E2E2E0">
                <p class="text-xs" style="color:#A8A8A8">
                    &copy; {{ date('Y') }} <strong class="grad-text">StudAI Hire</strong>. All rights reserved. Powered by <span style="color:#2D6CDF">Orin™ AI</span>.
                </p>
                <div class="flex flex-wrap gap-6">
                    @foreach([
                        ['Privacy Policy', Route::has('privacy') ? route('privacy') : '#'],
                        ['Terms of Service', Route::has('terms') ? route('terms') : '#'],
                        ['Cookie Policy', Route::has('cookie-policy') ? route('cookie-policy') : '#'],
                        ['Security', Route::has('security') ? route('security') : '#'],
                    ] as $l)
                    <a href="{{ $l[1] }}" class="footer-link">{{ $l[0] }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    {{-- Cookie consent --}}
    @include('components.cookie-consent')

    {{-- Back button --}}
    <div id="mkt-back" class="fixed bottom-6 left-6 z-50" style="display:none">
        <button onclick="history.back()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold transition-all hover:-translate-y-0.5 hover:shadow-lg"
            style="background:white; border:1.5px solid #E2E2E0; color:#737373; box-shadow:0 2px 12px rgba(0,0,0,.06)">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back
        </button>
    </div>
    <script>if(window.history.length>1)document.getElementById('mkt-back').style.display='block';</script>

    @stack('scripts')
    <x-cursor />
</body>
</html>
