<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- DNS Prefetch & Preconnect for Performance --}}
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="dns-prefetch" href="https://unpkg.com">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    {{-- SEO Meta Tags --}}
    <title>{{ $title ?? 'StudAI Hire — Your Career, On Autopilot' }}</title>
    <meta name="description" content="{{ $description ?? 'India\'s first autonomous career OS. AI-powered job search, interview prep, and salary negotiation.' }}">
    <meta name="keywords" content="job search, AI career platform, resume optimization, interview preparation, job matching, career growth, StudAI Hire">
    <meta name="author" content="StudAI Hire">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $ogTitle ?? $title ?? 'StudAI Hire — Your Career, On Autopilot' }}">
    <meta property="og:description" content="{{ $ogDescription ?? $description ?? 'India\'s first autonomous career OS' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/og-image.jpg') }}">
    <meta property="og:site_name" content="StudAI Hire">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $twitterTitle ?? $title ?? 'StudAI Hire' }}">
    <meta name="twitter:description" content="{{ $twitterDescription ?? $description ?? 'AI-powered job discovery platform' }}">
    <meta name="twitter:image" content="{{ $twitterImage ?? asset('images/twitter-card.jpg') }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Fonts - Preloaded for Performance --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Additional Styles --}}
    @stack('styles')

    <style>
        :root {
            --brand: #6366f1;
            --brand-dark: #4f46e5;
        }
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg,#6366f1,#a855f7); border-radius:999px; }
        .gradient-text {
            background-image: linear-gradient(135deg, #6366f1, #a855f7, #ec4899);
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-scrolled { background:#fff !important; box-shadow:0 2px 20px rgba(0,0,0,.08); }
        [x-cloak]{ display:none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-gray-900">
    {{-- Navigation --}}
    <nav x-data="{ mobileMenuOpen: false, scrolled: false }"
         @scroll.window="scrolled = (window.pageYOffset > 20)"
         :class="scrolled ? 'nav-scrolled' : 'bg-white/90 backdrop-blur'"
         class="fixed w-full top-0 z-50 transition-all duration-300 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <img src="/assets/logo/icon.png" alt="StudAI Hire" style="width:36px;height:36px;object-fit:contain;flex-shrink:0" class="transition-transform group-hover:scale-110">
                    <span class="text-lg font-extrabold tracking-tight text-gray-900">Stud<span class="gradient-text">AI</span> One</span>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center gap-7 text-sm font-medium">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Home</a>
                    @if(Route::has('features'))
                    <a href="{{ route('features') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Features</a>
                    @endif
                    <a href="{{ route('pricing') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Pricing</a>
                    @if(Route::has('about'))
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">About</a>
                    @endif
                    @if(Route::has('contact'))
                    <a href="{{ route('contact') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Contact</a>
                    @endif
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-full text-white text-sm font-semibold transition-all hover:shadow-lg hover:opacity-90" style="background:linear-gradient(135deg,#6366f1,#7c3aed)">Dashboard →</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-full text-white text-sm font-semibold transition-all hover:shadow-lg hover:opacity-90" style="background:linear-gradient(135deg,#6366f1,#7c3aed)">Get Started Free</a>
                    @endauth
                </div>

                {{-- Mobile Menu Button --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden bg-white border-t border-gray-100 shadow-lg">
            <div class="px-4 pt-3 pb-5 space-y-1">
                <a href="{{ route('home') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition font-medium">Home</a>
                @if(Route::has('features'))
                <a href="{{ route('features') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition font-medium">Features</a>
                @endif
                <a href="{{ route('pricing') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition font-medium">Pricing</a>
                @if(Route::has('about'))
                <a href="{{ route('about') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition font-medium">About</a>
                @endif
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-white rounded-xl font-semibold text-center mt-2" style="background:linear-gradient(135deg,#6366f1,#7c3aed)">Dashboard →</a>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition font-medium">Login</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2.5 text-white rounded-xl font-semibold text-center mt-2" style="background:linear-gradient(135deg,#6366f1,#7c3aed)">Get Started Free</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="pt-16">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer style="background:#f7f7fc; border-top:1px solid #ebebf5;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-10">
                {{-- Brand --}}
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2.5 mb-4">
                        <img src="/assets/logo/icon.png" alt="StudAI Hire" style="width:32px;height:32px;object-fit:contain;flex-shrink:0">
                        <span class="text-base font-extrabold text-gray-900">Stud<span class="gradient-text">AI</span> One</span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed mb-5 max-w-xs">
                        India's first autonomous career OS. AI-powered job search, interview prep, and salary negotiation.
                        <strong class="text-indigo-600">Your Career. On Autopilot.</strong>
                    </p>
                </div>

                {{-- Platform --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Platform</h3>
                    <ul class="space-y-2.5">
                        @if(Route::has('features'))<li><a href="{{ route('features') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Features</a></li>@endif
                        <li><a href="{{ route('pricing') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Pricing</a></li>
                        @if(Route::has('about'))<li><a href="{{ route('about') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">About</a></li>@endif
                        @if(Route::has('contact'))<li><a href="{{ route('contact') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Contact</a></li>@endif
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Legal</h3>
                    <ul class="space-y-2.5">
                        @if(Route::has('privacy'))<li><a href="{{ route('privacy') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Privacy Policy</a></li>@endif
                        @if(Route::has('terms'))<li><a href="{{ route('terms') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Terms of Service</a></li>@endif
                        @if(Route::has('cookie-policy'))<li><a href="{{ route('cookie-policy') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Cookie Policy</a></li>@endif
                        @if(Route::has('security'))<li><a href="{{ route('security') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">Security</a></li>@endif
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-8 text-center">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} <strong class="gradient-text">StudAI Hire</strong>. All rights reserved. Powered by <span class="text-indigo-600 font-semibold">Orin™ AI</span>.</p>
            </div>
        </div>
    </footer>

    {{-- Cookie Consent Banner --}}
    @include('components.cookie-consent')

    {{-- Live Chat Widget Placeholder --}}
    <div x-data="{ chatOpen: false }" class="fixed bottom-6 right-6 z-40">
        <button @click="chatOpen = !chatOpen" 
                class="bg-pink-600 hover:bg-pink-700 text-white rounded-full p-4 shadow-lg transition transform hover:scale-110">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </button>

        <div x-show="chatOpen" 
             x-transition
             class="absolute bottom-20 right-0 w-80 bg-white rounded-lg shadow-xl border border-gray-200">
            <div class="bg-pink-600 text-white p-4 rounded-t-lg flex justify-between items-center">
                <h3 class="font-semibold">Chat with us</h3>
                <button @click="chatOpen = false" class="hover:bg-pink-700 rounded p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4 h-64 flex items-center justify-center text-gray-500">
                <p class="text-sm text-center">
                    Live chat integration will be added here.<br>
                    <a href="mailto:support@studaihire.com" class="text-pink-600 hover:underline">Email us instead</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Additional Scripts --}}
    @stack('scripts')

    {{-- Analytics Placeholder --}}
    {{-- Google Analytics, Facebook Pixel, etc. will be added here --}}
</body>
</html>
