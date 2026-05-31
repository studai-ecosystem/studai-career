<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="StudAI Hire â€” AI-powered job marketplace with autonomous agent, negotiation strategist, and smart matching.">
        <meta name="theme-color" content="#0C2E72">

        <title>{{ $title ?? config('app.name', 'StudAI Hire') }}</title>

        <!-- Favicon & PWA -->
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="/icons/icon-192x192.svg">

        <!-- Fonts: MERIDIAN â€” DM Sans + DM Mono + Instrument Serif -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen" style="background:var(--color-canvas);">
            @auth
                @include('layouts.navigation')
            @endauth

            <!-- Back Button + Page Heading -->
            @isset($header)
                <header style="background:var(--color-surface);border-bottom:1px solid var(--color-border);">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 space-y-1">
                        <div class="flex items-center gap-3">
                            <x-back-button />
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium transition-colors duration-150" style="color:var(--color-accent)" onmouseover="this.style.color='var(--color-accent-hover)'" onmouseout="this.style.color='var(--color-accent)'">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="pt-1" style="color:var(--color-ink-1)">{{ $header }}</div>
                    </div>
                </header>
            @else
                <div class="max-w-7xl mx-auto pt-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <x-back-button />
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium transition-colors duration-150" style="color:var(--color-accent)" onmouseover="this.style.color='var(--color-accent-hover)'" onmouseout="this.style.color='var(--color-accent)'">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>
            @endisset

            <!-- Page Content -->
            <main>
                @if (View::hasSection('content'))
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>

        {{-- Toast Notifications --}}
        <x-ui.toast-container position="bottom-right" :max-toasts="5" :default-duration="4000" />

        @livewireScripts
        @stack('scripts')
        <x-cursor />

        {{-- PWA Service Worker Registration --}}
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function () {
                    navigator.serviceWorker.register('/sw.js', { scope: '/' })
                        .then(function (reg) { console.debug('[SW] Registered:', reg.scope); })
                        .catch(function (err) { console.warn('[SW] Registration failed:', err); });
                });
            }
        </script>
    </body>
</html>
