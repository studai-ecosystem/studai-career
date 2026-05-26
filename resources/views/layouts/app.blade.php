<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="StudAI Hire — AI-powered job marketplace with autonomous agent, negotiation strategist, and smart matching.">
        <meta name="theme-color" content="#1A73E8">

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
                <header style="background:linear-gradient(135deg,#f8faff 0%,#f0f4ff 50%,#faf5ff 100%);border-bottom:1px solid rgba(99,102,241,.12);box-shadow:0 1px 0 rgba(99,102,241,.08),0 2px 12px rgba(99,102,241,.06)">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 space-y-1">
                        <div class="flex items-center gap-3">
                            <x-back-button />
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium transition-colors duration-150" style="color:#6366f1" onmouseover="this.style.color='#4338ca'" onmouseout="this.style.color='#6366f1'">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="pt-1" style="color:#1e1b4b">{{ $header }}</div>
                    </div>
                </header>
            @else
                <div class="max-w-7xl mx-auto pt-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <x-back-button />
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium transition-colors duration-150" style="color:#6366f1" onmouseover="this.style.color='#4338ca'" onmouseout="this.style.color='#6366f1'">
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
    </body>
</html>
