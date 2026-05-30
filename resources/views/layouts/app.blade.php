<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="StudAI Hire — AI-powered job marketplace with autonomous agent, negotiation strategist, and smart matching.">
        <meta name="theme-color" content="#0c1c2c">

        <title>{{ $title ?? config('app.name', 'StudAI Hire') }}</title>

        <!-- Favicon & PWA -->
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="/icons/icon-192x192.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @auth
                @include('layouts.navigation')
            @endauth

            <!-- Back Button + Page Heading -->
            @isset($header)
                <header style="background:#f7f8fa;border-bottom:1px solid #eaecf1;box-shadow:0 1px 0 rgba(47,95,176,.08),0 2px 12px rgba(21,35,58,.05)">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 space-y-1">
                        <div class="flex items-center gap-3">
                            <x-back-button />
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium transition-colors duration-150" style="color:#2f5fb0" onmouseover="this.style.color='#21426f'" onmouseout="this.style.color='#2f5fb0'">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="pt-1" style="color:#15233a">{{ $header }}</div>
                    </div>
                </header>
            @else
                <div class="max-w-7xl mx-auto pt-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <x-back-button />
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium transition-colors duration-150" style="color:#2f5fb0" onmouseover="this.style.color='#21426f'" onmouseout="this.style.color='#2f5fb0'">
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
