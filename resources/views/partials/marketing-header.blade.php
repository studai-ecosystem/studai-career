{{-- ── SHARED MARKETING NAVBAR ─────────────────────────────────────────
     Single source of truth for the public-site header. Included by both
     layouts/marketing.blade.php and pages/landing.blade.php so the homepage
     and every marketing/legal page share an identical header.
------------------------------------------------------------------------ --}}
<nav x-data="{ open: false, scrolled: false }"
     @scroll.window="scrolled = window.pageYOffset > 20"
     :class="scrolled ? 'nav-scrolled' : 'bg-white/80 backdrop-blur'"
     class="fixed w-full top-0 z-50 transition-all duration-300 border-b border-transparent"
     style="border-bottom-color: rgba(224,227,234,.7)">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- Logo / wordmark --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <img src="/assets/logo/icon.png?v=3" alt="StudAI One" style="height:30px;width:auto;object-fit:contain;flex-shrink:0" class="transition-transform group-hover:scale-110">
                <span class="text-lg font-extrabold tracking-tight" style="color:#0C0C0C">Stud<span style="color:#2D6CDF">AI</span> One</span>
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
