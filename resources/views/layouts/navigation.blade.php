<style>
/* ── Nav — Light Theme ──────────────────────────────── */
@keyframes nav-shine   { to { background-position: -200% center; } }
@keyframes nav-float   { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-2.5px)} }
@keyframes nav-fadein  { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
@keyframes nav-dot     { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.6)} }
@keyframes nav-bar-grad{ 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
@keyframes nav-particle{ 0%{transform:translateY(0) scale(1);opacity:.6} 100%{transform:translateY(-40px) scale(0);opacity:0} }
@keyframes nav-ring    { 0%{transform:scale(1);opacity:.7} 100%{transform:scale(1.8);opacity:0} }
@keyframes nav-slide-in{ from{opacity:0;transform:translateX(-8px)} to{opacity:1;transform:translateX(0)} }

/* Navbar shell */
.nav-bar {
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid rgba(47,95,176,.12);
    box-shadow: 0 1px 0 rgba(47,95,176,.08), 0 4px 24px rgba(21,35,58,.07), 0 1px 3px rgba(0,0,0,.04);
    position: relative;
    animation: nav-fadein .4s ease both;
}
/* Animated gradient line at very bottom */
.nav-bar::after {
    content: '';
    position: absolute; bottom: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, #2D6CDF, #1B57C4, #0C0C0C, #E37400, #2D6CDF);
    background-size: 300% auto;
    animation: nav-bar-grad 4s linear infinite;
}
/* Subtle noise overlay */
.nav-bar::before {
    content: '';
    position: absolute; inset: 0; pointer-events: none;
    background: radial-gradient(ellipse at 20% 50%, rgba(47,95,176,.04) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 50%, rgba(28,52,77,.04) 0%, transparent 60%);
}

/* Floating particles inside nav */
.nav-particle {
    position: absolute; border-radius: 50%; pointer-events: none;
    animation: nav-particle var(--dur,3s) ease-out var(--del,0s) infinite;
    opacity: 0;
}

/* Logo */
.nav-logo { animation: nav-fadein .5s ease both; text-decoration: none; }
.nav-logo-img {
    animation: nav-float 4s ease-in-out infinite;
    transition: filter .3s, transform .3s;
    filter: drop-shadow(0 2px 6px rgba(47,95,176,.25));
}
.nav-logo:hover .nav-logo-img { filter: drop-shadow(0 4px 14px rgba(47,95,176,.55)) brightness(1.05); }
.nav-logo-text {
    background: linear-gradient(135deg, #1B57C4 0%, #2D6CDF 40%, #0C0C0C 70%, #1B57C4 100%);
    background-size: 200%;
    -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
    animation: nav-shine 4s linear infinite;
    font-size: .95rem; font-weight: 800; letter-spacing: -.5px;
}

/* Dashboard pill */
.nav-pill {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .35rem .95rem; border-radius: 999px;
    background: #EBF2FF;
    border: 1.5px solid rgba(47,95,176,.2);
    color: #1B57C4; font-size: .8rem; font-weight: 700;
    text-decoration: none;
    transition: all .25s cubic-bezier(.34,1.56,.64,1);
    animation: nav-fadein .5s ease .1s both;
    position: relative; overflow: hidden;
}
.nav-pill::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(47,95,176,.12), rgba(28,52,77,.12));
    opacity: 0; transition: opacity .2s;
}
.nav-pill:hover { border-color: rgba(47,95,176,.5); transform: translateY(-2px) scale(1.03); box-shadow: 0 6px 20px rgba(47,95,176,.2); color: #0C2E72; }
.nav-pill:hover::before { opacity: 1; }
.nav-dot { width: 6px; height: 6px; border-radius: 50%; background: #2D6CDF; animation: nav-dot 2s ease-in-out infinite; }

/* Breadcrumb */
.nav-breadcrumb { color: #737373; font-size: .78rem; font-weight: 600; }
.nav-sep { color: #9aa6bd; }

/* Search icon */
.nav-icon-btn {
    width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
    color: #737373; background: transparent; border: 1.5px solid #E2E2E0;
    transition: all .2s cubic-bezier(.34,1.56,.64,1);
}
.nav-icon-btn:hover { color: #1B57C4; border-color: rgba(47,95,176,.4); background: #EBF2FF; transform: scale(1.08); box-shadow: 0 4px 12px rgba(47,95,176,.15); }

/* Avatar with animated ring */
.nav-avatar-wrap { position: relative; }
.nav-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, #1B57C4, #2D6CDF,#2D6CDF);
    background-size: 200%; animation: nav-shine 3s linear infinite;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .82rem; color: #fff;
    border: 2px solid #fff;
    box-shadow: 0 2px 10px rgba(47,95,176,.35);
    transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s;
    z-index: 1; position: relative;
}
.nav-avatar-ring {
    position: absolute; inset: -4px; border-radius: 50%;
    border: 2px solid rgba(47,95,176,.5);
    animation: nav-ring 2s ease-out infinite;
}
.nav-user-btn:hover .nav-avatar { transform: scale(1.1); box-shadow: 0 4px 20px rgba(47,95,176,.45); }
.nav-username { font-size: .83rem; font-weight: 700; color: #3D3D3D; }
.nav-chevron  { color: #9aa6bd; transition: transform .2s, color .2s; }
.nav-user-btn:hover .nav-chevron { transform: rotate(180deg); color: #2D6CDF; }

/* Dropdown */
.nav-dropdown {
    background: #fff;
    border: 1px solid rgba(47,95,176,.15);
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(21,35,58,.12), 0 4px 20px rgba(47,95,176,.1), 0 0 0 1px rgba(47,95,176,.05);
    overflow: hidden; min-width: 200px;
    animation: nav-fadein .15s ease both;
}
.nav-dropdown-header {
    padding: .75rem 1rem;
    background: #EBF2FF;
    border-bottom: 1px solid rgba(47,95,176,.1);
}
.nav-dropdown-item {
    display: flex; align-items: center; gap: .65rem;
    padding: .6rem 1rem; color: #3D3D3D; font-size: .83rem; font-weight: 500;
    transition: all .15s ease; text-decoration: none;
    border: none; width: 100%; cursor: pointer; background: transparent; text-align: left;
}
.nav-dropdown-item:hover {
    background: #EBF2FF;
    color: #1B57C4;
    padding-left: 1.25rem;
}
.nav-dropdown-item svg { transition: transform .2s; }
.nav-dropdown-item:hover svg { transform: scale(1.15); color: #2D6CDF; }
.nav-dropdown-divider { height: 1px; background: #eef0f4; margin: .25rem 0; }
.nav-dropdown-item.danger { color: #cf3a3a; }
.nav-dropdown-item.danger:hover { background: #fbe9e9; color: #9e2727; padding-left: 1.25rem; }
</style>

<nav x-data="{ open: false }" class="nav-bar sticky top-0 z-40">

    {{-- Floating particles (light, subtle) --}}
    <div class="nav-particle" style="left:15%;bottom:0;width:4px;height:4px;background:#2D6CDF;--dur:4s;--del:0s;"></div>
    <div class="nav-particle" style="left:35%;bottom:0;width:3px;height:3px;background:#0C0C0C;--dur:5s;--del:1.2s;"></div>
    <div class="nav-particle" style="left:60%;bottom:0;width:4px;height:4px;background:#E37400;--dur:3.5s;--del:.6s;"></div>
    <div class="nav-particle" style="left:80%;bottom:0;width:3px;height:3px;background:#2D6CDF;--dur:4.5s;--del:2s;"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- LEFT --}}
            <div class="flex items-center gap-5">
                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="nav-logo flex items-center gap-2.5">
                    <img src="/assets/logo/studai-hire-wordmark.svg?v=4" alt="StudAI Hire" class="nav-logo-img" style="height:30px;width:auto;object-fit:contain;">
                </a>

                {{-- Divider --}}
                <div class="hidden sm:block w-px h-5" style="background:linear-gradient(180deg,transparent,#d3def0,transparent)"></div>

                {{-- Dashboard pill --}}
                <a href="{{ route('dashboard') }}" class="nav-pill hidden sm:inline-flex">
                    <div class="nav-dot"></div>
                    Dashboard
                </a>

                {{-- Breadcrumb for resume pages --}}
                @if(request()->routeIs('resume.*'))
                <div class="hidden md:flex items-center gap-1.5">
                    <svg class="w-3 h-3 nav-sep" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    <span class="nav-breadcrumb" style="animation:nav-slide-in .3s ease both">Resume Builder</span>
                </div>
                @elseif(request()->routeIs('jobs.*'))
                <div class="hidden md:flex items-center gap-1.5">
                    <svg class="w-3 h-3 nav-sep" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    <span class="nav-breadcrumb" style="animation:nav-slide-in .3s ease both">Job Search</span>
                </div>
                @elseif(request()->routeIs('agent.*'))
                <div class="hidden md:flex items-center gap-1.5">
                    <svg class="w-3 h-3 nav-sep" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    <span class="nav-breadcrumb" style="animation:nav-slide-in .3s ease both">AI Agent</span>
                </div>
                @endif
            </div>

            {{-- RIGHT --}}
            <div class="flex items-center gap-2">
                {{-- Search --}}
                <a href="{{ route('jobs.search') }}" class="nav-icon-btn" title="Search Jobs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </a>

                {{-- User dropdown --}}
                <div class="relative" x-data="{ userMenu: false }">
                    <button @click="userMenu = !userMenu" @click.outside="userMenu = false"
                            class="nav-user-btn flex items-center gap-2 px-2 py-1 rounded-xl transition-all duration-200 hover:bg-indigo-50">
                        <div class="nav-avatar-wrap">
                            <div class="nav-avatar-ring"></div>
                            <div class="nav-avatar">{{ strtoupper(substr(Auth::user()?->name ?? 'G', 0, 1)) }}</div>
                        </div>
                        <span class="nav-username hidden sm:block">{{ explode(' ', Auth::user()?->name ?? 'Guest')[0] }}</span>
                        <svg class="nav-chevron w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="userMenu"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="nav-dropdown absolute right-0 z-50"
                         style="top:calc(100% + 10px)">

                        <div class="nav-dropdown-header">
                            <p class="text-xs font-bold text-indigo-900">{{ Auth::user()?->name ?? 'Guest' }}</p>
                            <p class="text-xs mt-0.5 text-indigo-400">{{ Auth::user()?->email ?? '' }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('dashboard') }}" class="nav-dropdown-item">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dashboard
                            </a>
                            <a href="{{ route('profile.edit') }}" class="nav-dropdown-item">
                                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profile
                            </a>
                            <a href="{{ route('subscriptions.pricing') }}" class="nav-dropdown-item">
                                <svg class="w-4 h-4 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                Upgrade Plan
                            </a>

                            <div class="nav-dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-dropdown-item danger">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="nav-icon-btn">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden"
         style="background:#fff;border-top:1px solid #EBF2FF;box-shadow:0 8px 24px rgba(47,95,176,.1)">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-semibold text-indigo-700 bg-indigo-50">Dashboard</a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Profile</a>
        </div>
        <div class="px-4 py-3" style="border-top:1px solid #EBF2FF">
            <p class="text-sm font-bold text-gray-800">{{ Auth::user()?->name ?? 'Guest' }}</p>
            <p class="text-xs mt-0.5 text-gray-400">{{ Auth::user()?->email ?? '' }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="text-sm font-semibold text-red-500">Log Out</button>
            </form>
        </div>
    </div>
</nav>

