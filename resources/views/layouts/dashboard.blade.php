{{--
    StudAI Hire - Dashboard Layout
    Premium light SaaS design: Plus Jakarta Sans, #F7F7F5 bg, #2D6CDF royal-blue brand
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Apply saved theme before paint to avoid flash of incorrect theme --}}
    <script>
        (function () {
            try {
                var t = localStorage.getItem('meridian-theme');
                var dark = t ? t === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.dataset.theme = dark ? 'dark' : 'light';
            } catch (e) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>

    <title>@yield('title', isset($title) ? $title : 'Dashboard') - {{ config('app.name', 'StudAI Hire') }}</title>

    <!-- Fonts: MERIDIAN — DM Sans + DM Mono + Instrument Serif -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Favicon & PWA -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.svg">
    <meta name="theme-color" content="#0C2E72">
    @yield('head')
    @stack('styles')

    <style>
        /* -- DESIGN TOKENS -- */
        /* MERIDIAN: legacy token names mapped to MERIDIAN design tokens */
        :root {
            --brand:        var(--color-accent, #2D6CDF);
            --brand-hover:  var(--color-accent-hover, #1B57C4);
            --brand-light:  var(--color-accent-subtle, #EBF2FF);
            --bg:           var(--color-canvas, #F7F7F5);
            --surface:      var(--color-surface, #FFFFFF);
            --border:       var(--color-border, #E2E2E0);
            --text:         var(--color-ink-1,#2D6CDF);
            --text-2:       var(--color-ink-2, #3D3D3D);
            --text-muted:   var(--color-ink-3, #737373);
            --sidebar-w:    256px;
            --topbar-h:     56px;
            --ease:         cubic-bezier(0.2, 0, 0, 1);
            --dur:          180ms;
            /* module accents flattened to MERIDIAN accent / ink */
            --accent-coach:       var(--color-accent, #2D6CDF);
            --accent-interview:   var(--color-accent, #2D6CDF);
            --accent-jobs:        var(--color-accent, #2D6CDF);
            --accent-market:      var(--color-accent, #2D6CDF);
            --accent-negotiation: var(--color-accent, #2D6CDF);
            --accent-scout:       var(--color-accent, #2D6CDF);
            --accent-vantage:     var(--color-ink-1,#2D6CDF);
            --accent-resume:      var(--color-accent, #2D6CDF);
            /* stat card accents */
            --stat-applications:  var(--color-accent, #2D6CDF);
            --stat-interviews:    var(--color-ink-2, #3D3D3D);
            --stat-views:         var(--color-ink-1,#2D6CDF);
            --stat-match:         var(--color-success, #1F7A4D);
        }

        /* -- BASE (MERIDIAN) -- */
        body { font-family:'Inter',system-ui,sans-serif; font-size:14px; line-height:1.55; background:var(--bg); color:var(--text); -webkit-font-smoothing:antialiased; }
        .main-bg { background:var(--bg); min-height:100vh; padding-top:80px; }
        h1,h2,h3,h4,h5,h6 { font-weight:600; letter-spacing:-0.01em; color:var(--text); }
        .font-mono { font-family:'Roboto Mono',ui-monospace,monospace; }

        /* -- SIDEBAR / TOPBAR (MERIDIAN: flat, no blur, no shadow) -- */
        aside  { background:var(--surface); border-right:1px solid var(--border); }
        header { background:var(--surface); border-bottom:1px solid var(--border); height:var(--topbar-h); }

        /* -- NAV ITEMS (MERIDIAN) -- */
        .nav-item { display:flex; align-items:center; gap:10px; padding:8px 12px; border-radius:8px; font-size:13.5px; font-weight:500; color:var(--text-2); transition:all var(--dur) var(--ease); cursor:pointer; text-decoration:none; }
        .nav-item:hover { background:var(--color-surface-raised, #F0F0EE); color:var(--text); }
        .nav-item svg { width:18px !important; height:18px !important; flex-shrink:0; }
        .nav-sub { padding-left:10px; font-size:12.5px; padding-top:6px; padding-bottom:6px; opacity:.9; }
        .nav-item.active { background:var(--brand-light); color:var(--color-accent-text, #0C2E72); font-weight:600; position:relative; }
        .nav-item.active::before { content:''; position:absolute; left:0; top:50%; transform:translateY(-50%); width:3px; height:65%; background:var(--brand); border-radius:0 3px 3px 0; }
        .nav-active { position:relative; }
        .nav-active::before { content:''; position:absolute; left:0; top:50%; transform:translateY(-50%); width:3px; height:65%; border-radius:0 3px 3px 0; background:var(--brand); }
        .nav-section-label { font-size:10.5px; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--color-ink-4, #A8A8A8); padding:0 12px; margin-bottom:4px; margin-top:20px; display:block; }

        /* -- CARDS (MERIDIAN: flat, 1px border, 12px radius, no shadow) -- */
        .card { background:var(--surface); border:1px solid var(--border); border-radius:12px; box-shadow:none; transition:border-color var(--dur) var(--ease); }
        .card:hover { box-shadow:none; transform:none; border-color:var(--color-border-strong, #C8C8C5); }
        .card-static { background:var(--surface); border:1px solid var(--border); border-radius:12px; box-shadow:none; }
        .card-lift { transition:border-color var(--dur) var(--ease); }
        .card-lift:hover { transform:none; box-shadow:none; border-color:var(--color-border-strong, #C8C8C5); }
        .shadow-card { box-shadow:none; }

        /* -- BUTTONS (MERIDIAN: 8px radius, no shadow, no lift) -- */
        .btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:13.5px; font-weight:500; cursor:pointer; border:1px solid transparent; transition:all var(--dur) var(--ease); text-decoration:none; }
        .btn:hover { transform:none; }
        .btn:active { transform:none; }

        /* Primary */
        .btn-primary { background:var(--brand); color:#fff; box-shadow:none; }
        .btn-primary:hover { background:var(--brand-hover); box-shadow:none; }

        /* Secondary */
        .btn-secondary { background:var(--surface); color:var(--text-2); border:1px solid var(--border); box-shadow:none; }
        .btn-secondary:hover { border-color:var(--color-border-strong, #C8C8C5); color:var(--text); background:var(--color-surface-raised, #F0F0EE); }

        /* Success */
        .btn-success { background:var(--color-success-subtle, #E6F4EC); color:var(--color-success, #1F7A4D); border:1px solid var(--color-success-border, #9FD6BA); }
        .btn-success:hover { background:var(--color-success-subtle, #CDEADB); }

        /* Danger */
        .btn-danger { background:var(--color-error-subtle, #FBE9E9); color:var(--color-error, #B42318); border:1px solid var(--color-error-border, #F3C2C2); }
        .btn-danger:hover { background:var(--color-error-subtle, #F7D6D6); }

        /* Ghost */
        .btn-ghost { background:transparent; color:var(--text-muted); border:1px solid transparent; }
        .btn-ghost:hover { background:var(--color-surface-raised, #F0F0EE); color:var(--text); }

        /* -- STAT CARD ACCENTS -- */
        .stat-applications .stat-icon { background:rgba(47,95,176,.10); color:#2D6CDF; }
        .stat-applications .stat-value { color:#2D6CDF; }
        .stat-interviews   .stat-icon { background:rgba(201,148,26,.12); color:#2D6CDF; }
        .stat-interviews   .stat-value { color:#2D6CDF; }
        .stat-views        .stat-icon { background:rgba(28,52,77,.10);  color:#0C0C0C; }
        .stat-views        .stat-value { color:#0C0C0C; }
        .stat-match        .stat-icon { background:rgba(31,138,91,.10);   color:#1F7A4D; }
        .stat-match        .stat-value { color:#1F7A4D; }

        /* -- MAIN CONTENT LAYOUT -- */
        .main-content { max-width:1320px; margin:0 auto; padding:28px 32px 32px; }

        .icon-circle { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; }
        .stagger > * { opacity:0; animation:fadeUp .4s var(--ease) both; }
        .stagger > *:nth-child(1){animation-delay:.05s} .stagger > *:nth-child(2){animation-delay:.10s}
        .stagger > *:nth-child(3){animation-delay:.15s} .stagger > *:nth-child(4){animation-delay:.20s}
        .stagger > *:nth-child(5){animation-delay:.25s} .stagger > *:nth-child(6){animation-delay:.30s}
        .stagger > *:nth-child(7){animation-delay:.35s} .stagger > *:nth-child(8){animation-delay:.40s}
        @keyframes fadeUp    { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        @keyframes fadeIn    { from{opacity:0} to{opacity:1} }
        @keyframes scaleIn   { from{opacity:0;transform:scale(.94)} to{opacity:1;transform:scale(1)} }
        @keyframes fadeInUp  { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        @keyframes shimmer   { to{background-position:-200% center} }
        @keyframes pulseSoft { 0%,100%{opacity:1} 50%{opacity:.55} }
        @keyframes bounceSoft{ 0%,100%{transform:translateY(0)} 50%{transform:translateY(-5px)} }
        @keyframes spinSlow  { to{transform:rotate(360deg)} }
        @keyframes aiFabPulse{ 0%,100%{box-shadow:0 0 0 0 rgba(47,95,176,.4)} 50%{box-shadow:0 0 0 14px rgba(47,95,176,0)} }
        @keyframes alertPulse{ 0%,100%{transform:scale(1)} 50%{transform:scale(1.25)} }
        .animate-fade-in    { animation:fadeIn .3s var(--ease) both; }
        .animate-fade-up    { animation:fadeUp .4s var(--ease) both; }
        .animate-scale-in   { animation:scaleIn .22s var(--ease) both; }
        .animate-pulse-soft { animation:pulseSoft 2s ease-in-out infinite; }
        .animate-bounce-soft{ animation:bounceSoft 1.2s ease-in-out infinite; }
        .animate-spin-slow  { animation:spinSlow 3s linear infinite; }
        .animate-shimmer    { animation:meridian-shimmer 1.5s linear infinite; background:var(--color-surface-raised, #F0F0EE); background-size:200%; }
        .skeleton           { background:var(--color-surface-raised, #F0F0EE); background-size:200%; animation:meridian-shimmer 1.5s linear infinite; border-radius:8px; }
        .skill-bar-fill     { width:0; transition:width 1s cubic-bezier(.4,0,.2,1); }
        .xp-bar             { background:var(--brand); }
        #cmd-palette        { backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); }
        .ai-fab-pulse       { animation:aiFabPulse 2.5s ease-in-out infinite; }
        .alert-pulse        { animation:alertPulse 1.5s ease-in-out infinite; }
        .completeness-ring  { transform:rotate(-90deg); }
        .completeness-ring circle { transition:stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1); }
        .dark { --bg:var(--color-canvas); --surface:var(--color-surface); --border:var(--color-border); --text:var(--color-ink-1); --text-muted:var(--color-ink-3); }
        /* Hide Alpine-controlled overlays/dropdowns until Alpine initializes.
           Without this, x-show elements (esp. the full-screen command palette)
           render visible before/if-without Alpine and block all clicks. */
        [x-cloak] { display: none !important; }
        /* Input styles that work in both light and dark mode (MERIDIAN) */
        .input-google { display:block; width:100%; border-radius:8px; border:1px solid var(--border); background-color:var(--surface); padding:9px 14px; font-size:14px; color:var(--text); transition:all .15s ease; }
        .input-google:focus { outline:none; border-color:var(--brand); box-shadow:0 0 0 4px var(--color-accent-subtle, rgba(20,71,186,.12)); }
        .input-google::placeholder { color:var(--color-ink-4, #A8A8A8); }
        .dark .input-google { background-color:var(--color-surface); border-color:var(--color-border); color:var(--color-ink-1); }
        .dark .input-google::placeholder { color:var(--color-ink-4); }
        .dark body  { background:var(--color-canvas); color:var(--color-ink-1); }
        .dark aside { background:var(--color-surface); border-color:var(--color-border); }
        .dark header{ background:var(--color-surface); border-color:var(--color-border); }
        .scrollbar-thin::-webkit-scrollbar { width:4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background:transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background:#E2E2E0; border-radius:999px; }
        .glow-coach{box-shadow:none} .glow-interview{box-shadow:none}
        .glow-jobs {box-shadow:none}  .glow-market{box-shadow:none}
        .glow-negotiation{box-shadow:none} .glow-scout{box-shadow:none}
        .glow-vantage{box-shadow:none}
        .count-up { font-variant-numeric:tabular-nums; }
    </style>
</head>
<body class="antialiased" x-data="{
    sidebarOpen: true,
    sidebarMobileOpen: false,
    cmdOpen: false,
    negotiationOpen: true
}"
@keydown.ctrl.k.window.prevent="cmdOpen = !cmdOpen"
@keydown.meta.k.window.prevent="cmdOpen = !cmdOpen"
@keydown.escape.window="cmdOpen = false">

    <div class="min-h-screen flex">

        {{-- ============================================
            SIDEBAR
        ============================================ --}}
        <aside
            class="fixed inset-y-0 left-0 z-40 flex flex-col transition-all duration-300"
            :style="sidebarOpen ? 'width:252px' : 'width:72px'"
        >
            {{-- Logo --}}
            <div class="flex items-center gap-2 px-3 border-b flex-shrink-0" style="height:64px; border-color:var(--border)">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0">
                    <img src="/assets/logo/studai-hire-icon.svg?v=4" alt="StudAI Hire" x-show="!sidebarOpen" style="width:32px;height:32px;object-fit:contain;flex-shrink:0">
                    <img src="/assets/logo/studai-hire-wordmark.svg?v=4" alt="StudAI Hire" x-show="sidebarOpen" x-transition style="height:26px;width:auto;object-fit:contain;flex-shrink:0">
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 scrollbar-thin">

                @php $isEmployer = auth()->user()?->isEmployer(); @endphp

                @if($isEmployer)
                {{-- ===== EMPLOYER NAV ===== --}}
                <span class="nav-section-label" x-show="sidebarOpen">Main</span>

                <a href="{{ route('employer.dashboard') }}"
                   class="nav-item {{ request()->routeIs('employer.dashboard') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Dashboard</span>
                </a>

                <a href="{{ route('employer.jobs.index') }}"
                   class="nav-item {{ request()->routeIs('employer.jobs.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Job Postings</span>
                </a>

                <a href="{{ route('employer.applicants.index') }}"
                   class="nav-item {{ request()->routeIs('employer.applicants.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Applicants</span>
                </a>

                <span class="nav-section-label" x-show="sidebarOpen">S.C.O.U.T. AI</span>

                <a href="{{ route('employer.scout.dashboard') }}"
                   class="nav-item {{ request()->routeIs('employer.scout.dashboard') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>DNA Dashboard</span>
                    <span x-show="sidebarOpen" class="ml-auto px-1.5 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-100 text-emerald-700">AI</span>
                </a>

                <a href="{{ route('employer.scout.shortlisting') }}"
                   class="nav-item {{ request()->routeIs('employer.scout.shortlisting') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Auto Shortlisting</span>
                </a>

                <a href="{{ route('employer.scout.candidate-matching') }}"
                   class="nav-item {{ request()->routeIs('employer.scout.candidate-matching') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Candidate Match</span>
                </a>

                <a href="{{ route('employer.scout.predictive') }}"
                   class="nav-item {{ request()->routeIs('employer.scout.predictive') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Predictive Analytics</span>
                </a>

                <span class="nav-section-label" x-show="sidebarOpen">More</span>

                <a href="{{ route('employer.interviews.index') }}"
                   class="nav-item {{ request()->routeIs('employer.interviews.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Interviews</span>
                </a>

                <a href="{{ route('employer.profile.show') }}"
                   class="nav-item {{ request()->routeIs('employer.profile.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Company Profile</span>
                </a>

                <a href="{{ route('employer.analytics') }}"
                   class="nav-item {{ request()->routeIs('employer.analytics') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Analytics</span>
                </a>

                <span class="nav-section-label" x-show="sidebarOpen">Marketplace</span>

                <a href="{{ route('marketplace.index') }}"
                   class="nav-item {{ request()->routeIs('marketplace.index') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Marketplace</span>
                </a>

                <a href="{{ route('marketplace.employer.dashboard') }}"
                   class="nav-item nav-sub {{ request()->routeIs('marketplace.employer.*') ? 'active' : '' }}"
                   x-show="sidebarOpen" x-transition
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[16px] h-[16px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition style="font-size:12px;">🏢 Company Portal</span>
                </a>

                <a href="{{ route('marketplace.gigs') }}"
                   class="nav-item nav-sub {{ request()->routeIs('marketplace.gigs') ? 'active' : '' }}"
                   x-show="sidebarOpen" x-transition
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[16px] h-[16px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition style="font-size:12px;">🛒 Browse Services</span>
                </a>

                <a href="{{ route('marketplace.freelancers') }}"
                   class="nav-item nav-sub {{ request()->routeIs('marketplace.freelancer-show') ? 'active' : '' }}"
                   x-show="sidebarOpen" x-transition
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[16px] h-[16px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition style="font-size:12px;">👤 Find Talent</span>
                </a>

                @else
                {{-- ===== JOB SEEKER NAV ===== --}}
                <span class="nav-section-label" x-show="sidebarOpen">Main</span>

                <a href="{{ route('dashboard') }}"
                   class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Dashboard</span>
                </a>

                <a href="{{ route('jobs.search') }}"
                   class="nav-item {{ request()->routeIs('jobs.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Job Search</span>
                </a>

                @if(auth()->user()?->account_type !== 'employer')
                <a href="{{ route('agent.dashboard') }}"
                   class="nav-item {{ request()->routeIs('agent.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>AI Agent</span>
                    <span x-show="sidebarOpen" class="ml-auto px-1.5 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-100 text-emerald-700">Live</span>
                </a>
                @endif

                <span class="nav-section-label" x-show="sidebarOpen">Career Tools</span>

                <a href="{{ route('resume.index') }}"
                   class="nav-item {{ request()->routeIs('resume.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Resume Builder</span>
                </a>

                <a href="{{ route('interview.index') }}"
                   class="nav-item {{ request()->routeIs('interview.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Interview Lab</span>
                </a>

                <a href="{{ route('career-coach.index') }}"
                   class="nav-item {{ request()->routeIs('career-coach.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Career Coach</span>
                </a>

                {{-- Negotiation accordion --}}
                <div x-show="sidebarOpen" x-transition>
                    {{-- Parent row: label navigates to dashboard, chevron toggles sub-menu --}}
                    <div class="flex items-center gap-1">
                        <a href="{{ route('negotiation.dashboard') }}"
                           class="nav-item flex-1 {{ request()->routeIs('negotiation.*') && ! request()->routeIs('negotiation.chatbot') ? 'active' : '' }}">
                            <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="flex-1">Negotiation</span>
                        </a>
                        <button type="button" @click="negotiationOpen = !negotiationOpen"
                                aria-label="Toggle negotiation menu"
                                class="flex-shrink-0 flex items-center justify-center w-7 h-7 rounded"
                                style="background:rgba(47,95,176,.12);border:none;cursor:pointer;">
                            <svg class="w-3 h-3 transition-transform duration-200"
                                 :class="negotiationOpen ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    {{-- Sub-item --}}
                    <div x-show="negotiationOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="ml-4 relative">
                        <div class="absolute inset-y-1 left-[11px] w-px rounded-full"
                             style="background:linear-gradient(to bottom,rgba(47,95,176,.4),rgba(47,95,176,.06))"></div>
                        <a href="{{ route('negotiation.chatbot') }}"
                           class="nav-item nav-sub {{ request()->routeIs('negotiation.chatbot') ? 'active' : '' }}">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 ml-3" style="background:#2D6CDF"></span>
                            <span>AI Negotiation Agent</span>
                        </a>
                    </div>
                </div>
                {{-- Collapsed sidebar: icon-only --}}
                <a x-show="!sidebarOpen"
                   href="{{ route('negotiation.dashboard') }}"
                   class="nav-item {{ request()->routeIs('negotiation.*') ? 'active' : '' }} justify-center">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </a>

                <span class="nav-section-label" x-show="sidebarOpen">More</span>

                <a href="{{ route('marketplace.index') }}"
                   class="nav-item {{ request()->routeIs('marketplace.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Marketplace</span>
                </a>
                <a href="{{ route('marketplace.gigs') }}"
                   class="nav-item nav-sub {{ request()->routeIs('marketplace.gig*') ? 'active' : '' }}"
                   x-show="sidebarOpen" x-transition
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[16px] h-[16px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition style="font-size:12px;">🛒 Buy Services</span>
                </a>
                <a href="{{ route('marketplace.freelancer.dashboard') }}"
                   class="nav-item nav-sub {{ request()->routeIs('marketplace.freelancer.*') ? 'active' : '' }}"
                   x-show="sidebarOpen" x-transition
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[16px] h-[16px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 7l6.16-3.422A12.083 12.083 0 0121 12.03V5.25L12 2 3 5.25v6.78c0 2.59 1.42 4.99 3.84 6.298L12 21z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition style="font-size:12px;">🎓 Student Portal</span>
                </a>

                <a href="{{ route('gamification.dashboard') }}"
                   class="nav-item {{ request()->routeIs('gamification.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Achievements</span>
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Settings</span>
                </a>
                @endif

            </nav>

            {{-- Sidebar Footer --}}
            <div class="p-3 border-t flex-shrink-0" style="border-color:var(--border)">
                @php
                    $userName = auth()->user()?->name ?? 'User';
                    $userEmail = auth()->user()?->email ?? '';
                    $userInitial = strtoupper(substr($userName, 0, 1));
                @endphp
                <div class="flex items-center gap-3 px-1 mb-2" :class="!sidebarOpen && 'justify-center'">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold text-white" style="background:linear-gradient(135deg,#2D6CDF,#2D6CDF)">
                        {{ $userInitial }}
                    </div>
                    <div x-show="sidebarOpen" x-transition class="flex-1 min-w-0">
                        <div class="text-sm font-semibold truncate" style="color:#0C0C0C">{{ $userName }}</div>
                        <div class="text-xs truncate" style="color:#737373">{{ $userEmail }}</div>
                    </div>
                </div>
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    class="nav-item w-full"
                    :class="!sidebarOpen && 'justify-center'"
                >
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform" :class="!sidebarOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="text-xs">Collapse</span>
                </button>
            </div>
        </aside>

        {{-- ============================================
            MAIN CONTENT
        ============================================ --}}
        <div class="main-bg flex-1 min-h-screen transition-all duration-300" :style="sidebarOpen ? 'margin-left:252px' : 'margin-left:72px'">

            {{-- Topbar --}}
            <header class="fixed top-0 right-0 flex items-center justify-between px-6 transition-all duration-300"
                    :style="sidebarOpen ? 'left:252px;z-index:999' : 'left:72px;z-index:999'">

                {{-- Left: Title / Breadcrumb --}}
                <div class="flex items-center gap-3">
                    @if(isset($breadcrumb))
                        {{ $breadcrumb }}
                    @else
                        <h1 class="text-[17px] font-semibold tracking-tight" style="color:var(--text)">
                            @if(isset($title))
                                {{ $title }}
                            @else
                                @hasSection('page-title')
                                    @yield('page-title')
                                @else
                                    @yield('title', 'Dashboard')
                                @endif
                            @endif
                        </h1>
                    @endif
                </div>

                {{-- Right: Actions --}}
                <div class="flex items-center gap-2">

                    {{-- Ctrl+K trigger --}}
                    <button @click="cmdOpen = true"
                        class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all hover:opacity-80"
                        style="background:var(--bg); border:1px solid var(--border); color:var(--text-muted)">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                        <kbd class="px-1 py-0.5 text-[10px] rounded" style="background:#E2E2E0">Ctrl K</kbd>
                    </button>

                    {{-- AI Active pill --}}
                    <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        AI Active
                    </div>

                    {{-- Theme toggle: flips the entire dashboard between light and dark --}}
                    <button @click="$store.theme.toggle()" type="button"
                        class="flex items-center justify-center w-9 h-9 rounded-lg transition-all hover:opacity-80"
                        style="background:var(--bg); border:1px solid var(--border); color:var(--text-muted)"
                        :aria-label="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'"
                        :title="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'">
                        {{-- Moon (shown in light mode) --}}
                        <svg x-show="!$store.theme.dark" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                        {{-- Sun (shown in dark mode) --}}
                        <svg x-show="$store.theme.dark" x-cloak class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="4" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32l1.41-1.41" />
                        </svg>
                    </button>

                    {{-- Notifications --}}
                    @php
                        $dbNotifications = auth()->user()?->notifications()->latest()->take(8)->get() ?? collect();
                        $unreadCount = auth()->user()?->unreadNotifications()->count() ?? 0;

                        // Icon/color map by notification type
                        $notifStyles = [
                            'application'  => ['bg'=>'#EBF2FF','color'=>'#2D6CDF','dot'=>'#2D6CDF'],
                            'interview'    => ['bg'=>'#FFF8EC','color'=>'#2D6CDF','dot'=>'#2D6CDF'],
                            'pipeline'     => ['bg'=>'#FFF8EC','color'=>'#2D6CDF','dot'=>'#2D6CDF'],
                            'test'         => ['bg'=>'#EBF2FF','color'=>'#2D6CDF','dot'=>'#2D6CDF'],
                            'scout'        => ['bg'=>'#EBF2FF','color'=>'#2D6CDF','dot'=>'#2D6CDF'],
                            'shortlisted'  => ['bg'=>'#e6f4ec','color'=>'#1F7A4D','dot'=>'#1F7A4D'],
                            'hired'        => ['bg'=>'#e6f4ec','color'=>'#166442','dot'=>'#166442'],
                            'rejected'     => ['bg'=>'#fbe9e9','color'=>'#cf3a3a','dot'=>'#cf3a3a'],
                            'job'          => ['bg'=>'#EBF2FF','color'=>'#2D6CDF','dot'=>'#2D6CDF'],
                            'default'      => ['bg'=>'#F7F7F5','color'=>'#737373','dot'=>'#737373'],
                        ];

                        if (!function_exists('notifTypeKey')) {
                            function notifTypeKey(string $type): string {
                                $t = strtolower($type);
                                foreach (['interview','pipeline','test','shortlisted','hired','rejected','scout','application','job'] as $k) {
                                    if (str_contains($t, $k)) return $k;
                                }
                                return 'default';
                            }
                        }
                    @endphp
                    <div x-data="{ open: false }" class="relative" style="z-index:1001">
                        {{-- Bell Button --}}
                        <button @click="open = !open"
                            class="relative flex items-center justify-center rounded-xl transition-all"
                            style="width:38px;height:38px;background:var(--bg);border:1.5px solid var(--border);color:#2D6CDF;box-shadow:0 1px 3px rgba(0,0,0,.06);"
                            :style="open ? 'background:#EBF2FF;border-color:#2D6CDF' : ''">
                            <svg style="width:19px;height:19px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($unreadCount > 0)
                            <span style="position:absolute;top:4px;right:4px;min-width:16px;height:16px;padding:0 3px;border-radius:99px;background:#2D6CDF;border:1.5px solid white;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:#fff;line-height:1;">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                            @else
                            <span style="position:absolute;top:5px;right:5px;width:8px;height:8px;border-radius:50%;background:#C8C8C5;border:1.5px solid white;display:block;"></span>
                            @endif
                        </button>

                        {{-- Dropdown --}}
                        <div x-cloak x-show="open"
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            style="position:absolute;top:calc(100% + 10px);right:0;width:340px;background:white;border:1px solid #E2E2E0;border-radius:18px;box-shadow:0 8px 40px rgba(21,35,58,.14),0 2px 12px rgba(0,0,0,.08);overflow:hidden;z-index:9999;">

                            {{-- Header --}}
                            <div style="padding:14px 18px 11px;border-bottom:1px solid #F0F0EE;display:flex;align-items:center;justify-content:space-between;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="font-size:15px;font-weight:700;color:#0C0C0C">Notifications</span>
                                    @if($unreadCount > 0)
                                    <span style="padding:2px 9px;font-size:11px;font-weight:700;border-radius:99px;background:#EBF2FF;color:#2D6CDF;border:1px solid #cfddf3;">{{ $unreadCount }} new</span>
                                    @endif
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" style="font-size:11px;font-weight:600;color:#2D6CDF;background:none;border:none;cursor:pointer;padding:0;">Mark all read</button>
                                    </form>
                                    @endif
                                    <button @click="open = false" style="color:#A8A8A8;background:none;border:none;cursor:pointer;padding:2px;">
                                        <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Items --}}
                            <div style="max-height:340px;overflow-y:auto;padding:5px 0;">
                                @forelse($dbNotifications as $notif)
                                @php
                                    $typeKey = notifTypeKey(class_basename($notif->type));
                                    $style   = $notifStyles[$typeKey];
                                    $isUnread = is_null($notif->read_at);
                                    $data    = $notif->data ?? [];
                                    if (!empty($data['message'])) {
                                        $message = $data['message'];
                                    } elseif (!empty($data['project_title'])) {
                                        $amount  = !empty($data['amount']) ? ' — ₹' . number_format((float)$data['amount']) : '';
                                        $message = '🎉 Offer received for "' . $data['project_title'] . '"' . $amount;
                                    } elseif (!empty($data['title'])) {
                                        $message = $data['title'];
                                    } elseif (!empty($data['body'])) {
                                        $message = $data['body'];
                                    } elseif (!empty($data['subject'])) {
                                        $message = $data['subject'];
                                    } else {
                                        $message = ucfirst(str_replace(['_', '-'], ' ', $data['type'] ?? 'Notification'));
                                    }
                                    $url     = $data['url'] ?? $data['action_url'] ?? '#';
                                    $timeAgo = $notif->created_at->diffForHumans();
                                @endphp
                                <a href="{{ $url }}" style="display:flex;align-items:flex-start;gap:12px;padding:11px 18px;cursor:pointer;transition:background .15s;position:relative;text-decoration:none;"
                                   onmouseover="this.style.background='#F0F0EE'" onmouseout="this.style.background=''">
                                    @if($isUnread)
                                    <span style="position:absolute;left:8px;top:50%;transform:translateY(-50%);width:6px;height:6px;border-radius:50%;background:{{ $style['dot'] }};flex-shrink:0;"></span>
                                    @endif
                                    <div style="width:36px;height:36px;border-radius:10px;background:{{ $style['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;{{ $isUnread ? 'margin-left:10px;' : 'margin-left:16px;' }}">
                                        @if($typeKey === 'interview')
                                        <svg style="width:17px;height:17px;color:{{ $style['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        @elseif(in_array($typeKey, ['hired','shortlisted']))
                                        <svg style="width:17px;height:17px;color:{{ $style['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        @elseif($typeKey === 'rejected')
                                        <svg style="width:17px;height:17px;color:{{ $style['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        @elseif($typeKey === 'job')
                                        <svg style="width:17px;height:17px;color:{{ $style['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        @elseif($typeKey === 'scout')
                                        <svg style="width:17px;height:17px;color:{{ $style['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        @else
                                        <svg style="width:17px;height:17px;color:{{ $style['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        @endif
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:13px;font-weight:{{ $isUnread ? '600' : '500' }};color:{{ $isUnread ? '#0C0C0C' : '#3D3D3D' }};line-height:1.4;">
                                            {{ Str::limit($message, 70) }}
                                        </div>
                                        <div style="font-size:11px;color:#A8A8A8;margin-top:3px;">{{ $timeAgo }}</div>
                                    </div>
                                </a>
                                @empty
                                <div style="padding:2rem 1rem;text-align:center;">
                                    <svg style="width:2.5rem;height:2.5rem;color:#C8C8C5;margin:0 auto .75rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <p style="font-size:13px;font-weight:600;color:#3D3D3D;">No notifications</p>
                                    <p style="font-size:12px;color:#A8A8A8;margin-top:4px;">You're all caught up!</p>
                                </div>
                                @endforelse
                            </div>

                            {{-- Footer --}}
                            <div style="padding:10px 18px 13px;border-top:1px solid #F0F0EE;display:flex;justify-content:center;">
                                <a href="{{ route('notifications.all') }}" style="font-size:13px;font-weight:600;color:#2D6CDF;text-decoration:none;">View all notifications &#8594;</a>
                            </div>
                        </div>
                    </div>

                    {{-- Profile Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-xl transition-colors hover:bg-gray-100">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg,#2D6CDF,#2D6CDF)">
                                @php echo strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)); @endphp
                            </div>
                            <svg class="w-3.5 h-3.5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-cloak x-show="open" @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 rounded-2xl shadow-xl py-2 z-50"
                            style="background:var(--surface); border:1px solid var(--border)">
                            <div class="px-4 py-3 border-b" style="border-color:var(--border)">
                                <div class="text-sm font-semibold" style="color:var(--text)">{{ auth()->user()?->name ?? 'Guest' }}</div>
                                <div class="text-xs mt-0.5" style="color:var(--text-muted)">{{ auth()->user()?->email ?? '' }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-gray-50" style="color:var(--text)">
                                <svg class="w-4 h-4" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                Profile
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-gray-50" style="color:var(--text)">
                                <svg class="w-4 h-4" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Settings
                            </a>
                            <div class="my-1 border-t" style="border-color:var(--border)"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 transition-colors hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </header>

            {{-- Page Content --}}
            <main>
                <div class="main-content">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         class="mx-4 mt-4 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-700/50 dark:bg-green-900/20 dark:text-green-300">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)"
                         class="mx-4 mt-4 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700/50 dark:bg-red-900/20 dark:text-red-300">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                    </div>
                @endif
                @if(session('info'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         class="mx-4 mt-4 flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-700/50 dark:bg-blue-900/20 dark:text-blue-300">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <span>{{ session('info') }}</span>
                        <button @click="show = false" class="ml-auto text-blue-600 hover:text-blue-800"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                    </div>
                @endif
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
                </div>
            </main>
        </div>
    </div>

    {{-- ============================================
        COMMAND PALETTE (Ctrl+K)
    ============================================ --}}
    <style>
    @keyframes cmdSlideIn {
        from { opacity:0; transform:translateY(-18px) scale(.96); }
        to   { opacity:1; transform:translateY(0)     scale(1); }
    }
    @keyframes cmdRipple {
        from { transform:scale(0); opacity:.5; }
        to   { transform:scale(2.5); opacity:0; }
    }
    .cmd-panel {
        animation: cmdSlideIn .22s cubic-bezier(.22,.68,0,1.2) both;
        background: linear-gradient(145deg,#ffffff 0%,#F7F7F5 55%,#EBF2FF 100%);
        border: 1.5px solid rgba(47,95,176,.18);
        border-radius: 1.5rem;
        box-shadow: 0 24px 80px rgba(21,35,58,.22), 0 4px 20px rgba(47,95,176,.12), 0 0 0 1px rgba(255,255,255,.6) inset;
        overflow: hidden;
        margin-top: -80px;
    }
    .cmd-search-row {
        display:flex; align-items:center; gap:.75rem;
        padding:.9rem 1.25rem;
        background: linear-gradient(90deg,rgba(47,95,176,.07) 0%,rgba(28,52,77,.05) 100%);
        border-bottom: 1.5px solid rgba(47,95,176,.1);
    }
    .cmd-search-icon {
        width:2rem; height:2rem; border-radius:.625rem; flex-shrink:0;
        background: linear-gradient(135deg,#2D6CDF,#2D6CDF);
        display:flex; align-items:center; justify-content:center;
        box-shadow: 0 3px 10px rgba(47,95,176,.35);
    }
    .cmd-input {
        flex:1; background:transparent; outline:none; border:none;
        font-size:.9375rem; font-weight:500; letter-spacing:-.01em;
        color:#0C0C0C;
        font-family:'Plus Jakarta Sans',sans-serif;
    }
    .cmd-input::placeholder { color:#9aa6bd; font-weight:400; }
    .cmd-esc-badge {
        padding:.25rem .6rem; border-radius:.5rem; font-size:.7rem; font-weight:700;
        background: #EBF2FF;
        color:#2D6CDF; border:1px solid rgba(47,95,176,.2);
        font-family:'JetBrains Mono',monospace;
    }
    .cmd-section-label {
        font-size:.67rem; font-weight:800; letter-spacing:.1em; text-transform:uppercase;
        color:#9aa6bd; padding:.75rem 1.25rem .3rem; display:block;
    }
    .cmd-item {
        display:flex; align-items:center; gap:.875rem;
        padding:.6rem 1.25rem; text-decoration:none;
        position:relative; overflow:hidden;
        transition: background .15s, transform .15s;
        border-radius:0;
        cursor:pointer;
    }
    .cmd-item:hover {
        background: linear-gradient(90deg,rgba(47,95,176,.08) 0%,rgba(28,52,77,.05) 100%);
        transform: translateX(3px);
    }
    .cmd-item:active { transform:translateX(3px) scale(.98); }
    .cmd-item .ripple {
        position:absolute; border-radius:50%;
        background:rgba(47,95,176,.25);
        pointer-events:none;
        animation: cmdRipple .5s ease-out forwards;
    }
    .cmd-icon-badge {
        width:2rem; height:2rem; border-radius:.625rem; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        box-shadow:0 2px 8px rgba(0,0,0,.08);
        transition: box-shadow .2s, transform .2s;
    }
    .cmd-item:hover .cmd-icon-badge { transform:scale(1.1) rotate(-5deg); box-shadow:0 4px 14px rgba(0,0,0,.14); }
    .cmd-label {
        font-size:.875rem; font-weight:600; color:#0C0C0C;
        letter-spacing:-.01em; font-family:'Plus Jakarta Sans',sans-serif;
        flex:1;
    }
    .cmd-arrow {
        font-size:.75rem; color:#9aa6bd; opacity:0;
        transition: opacity .15s, transform .15s;
        font-weight:700;
    }
    .cmd-item:hover .cmd-arrow { opacity:1; transform:translateX(3px); }
    .cmd-footer {
        padding:.625rem 1.25rem;
        background: linear-gradient(90deg,rgba(47,95,176,.05) 0%,rgba(28,52,77,.04) 100%);
        border-top:1.5px solid rgba(47,95,176,.1);
        display:flex; align-items:center; gap:1rem;
    }
    .cmd-kbd {
        display:inline-flex; align-items:center; gap:.35rem;
        font-size:.7rem; color:#737373; font-weight:600;
    }
    .cmd-kbd kbd {
        padding:.2rem .5rem; border-radius:.375rem;
        background:#EBF2FF;
        border:1px solid rgba(47,95,176,.2);
        color:#2D6CDF; font-size:.65rem; font-weight:700;
        font-family:'JetBrains Mono',monospace;
        box-shadow:0 1px 3px rgba(47,95,176,.15);
    }
    </style>
    <div id="cmd-palette"
        x-cloak
        x-show="cmdOpen"
        @click="cmdOpen = false"
        class="fixed inset-0 flex items-center justify-center px-4"
        style="display:none; z-index:9999; background:linear-gradient(135deg,rgba(12,28,44,.30) 0%,rgba(28,52,77,.22) 100%); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-120"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div @click.stop x-data="{ q: '' }" class="cmd-panel w-full max-w-xl">

            {{-- Search Row --}}
            <div class="cmd-search-row">
                <div class="cmd-search-icon">
                    <svg style="width:15px;height:15px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="q" placeholder="Search pages, tools, actions�" x-ref="searchInput"
                    x-init="$watch('cmdOpen', v => v && $nextTick(() => $refs.searchInput.focus()))"
                    @keydown.escape="cmdOpen = false"
                    class="cmd-input">
                <div class="cmd-esc-badge">Esc</div>
            </div>

            {{-- Results --}}
            <div class="py-1 max-h-80 overflow-y-auto" style="scrollbar-width:thin;scrollbar-color:rgba(47,95,176,.2) transparent">
                @php
                    $cmdItems = [
                        ['href'=>route('dashboard'),            'label'=>'Dashboard',            'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',       'grad'=>'linear-gradient(135deg,#2D6CDF,#1B57C4)', 'shadow'=>'rgba(47,95,176,.35)'],
                        ['href'=>route('resume.index'),         'label'=>'Resume Builder',        'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',                               'grad'=>'linear-gradient(135deg,#2D6CDF,#2D6CDF)', 'shadow'=>'rgba(47,95,176,.35)'],
                        ['href'=>route('interview.index'),      'label'=>'Interview Lab',         'icon'=>'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',               'grad'=>'linear-gradient(135deg,#E37400,#2D6CDF)', 'shadow'=>'rgba(201,148,26,.35)'],
                        ['href'=>route('jobs.search'),          'label'=>'Job Search',            'icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',                                                                                                       'grad'=>'linear-gradient(135deg,#1F7A4D,#26a86c)', 'shadow'=>'rgba(31,138,91,.35)'],
                        ['href'=>route('career-coach.index'),   'label'=>'Career Coach',          'icon'=>'M13 10V3L4 14h7v7l9-11h-7z',                                                                                                                        'grad'=>'linear-gradient(135deg,#2D6CDF,#1B57C4)', 'shadow'=>'rgba(47,95,176,.35)'],
                        ['href'=>route('negotiation.dashboard'),'label'=>'Negotiation Strategist','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'grad'=>'linear-gradient(135deg,#E37400,#2D6CDF)', 'shadow'=>'rgba(201,148,26,.35)'],
                        ['href'=>route('agent.dashboard'),      'label'=>'AI Agent',              'icon'=>'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',                                       'grad'=>'linear-gradient(135deg,#0C0C0C,#0C2E72)', 'shadow'=>'rgba(28,52,77,.35)'],
                        ['href'=>route('profile.edit'),         'label'=>'Profile Settings',      'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',                                                                              'grad'=>'linear-gradient(135deg,#737373,#A8A8A8)', 'shadow'=>'rgba(100,116,139,.35)'],
                        ['href'=>route('subscriptions.pricing'),'label'=>'Upgrade Plan',          'icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',                                        'grad'=>'linear-gradient(135deg,#E37400,#2D6CDF)', 'shadow'=>'rgba(201,148,26,.35)'],
                    ];
                @endphp

                <span class="cmd-section-label">Quick Navigation</span>

                @foreach($cmdItems as $cmd)
                <a href="{{ $cmd['href'] }}"
                    @click.prevent="
                        cmdOpen = false;
                        const el = $el;
                        const rect = el.getBoundingClientRect();
                        const r = document.createElement('span');
                        r.className = 'ripple';
                        const size = Math.max(rect.width, rect.height);
                        r.style.cssText = 'width:'+size+'px;height:'+size+'px;left:'+(rect.width/2 - size/2)+'px;top:'+(rect.height/2 - size/2)+'px;';
                        el.appendChild(r);
                        setTimeout(() => { r.remove(); window.location = '{{ $cmd['href'] }}'; }, 300);
                    "
                    x-show="q === '' || '{{ strtolower($cmd['label']) }}'.includes(q.toLowerCase())"
                    class="cmd-item group">
                    <div class="cmd-icon-badge" style="background:{{ $cmd['grad'] }};box-shadow:0 3px 10px {{ $cmd['shadow'] }};">
                        <svg style="width:13px;height:13px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cmd['icon'] }}" />
                        </svg>
                    </div>
                    <span class="cmd-label" x-html="q !== '' ? '{{ $cmd['label'] }}'.replace(new RegExp(q, 'gi'), m => '<mark style=\'background:#EBF2FF;color:#2D6CDF;border-radius:.25rem;padding:0 .2rem;font-weight:700\'>'+m+'</mark>') : '{{ $cmd['label'] }}'"></span>
                    <span class="cmd-arrow">&#8594;</span>
                </a>
                @endforeach

                <p x-show="q !== '' && !{{ json_encode(array_map(fn($c) => strtolower($c['label']), $cmdItems)) }}.some(l => l.includes(q.toLowerCase()))"
                   style="padding:1.5rem 1.25rem;text-align:center;font-size:.84rem;color:#9aa6bd;font-weight:500;font-family:'Plus Jakarta Sans',sans-serif;">
                    No results for "<span x-text="q" style="color:#2D6CDF;font-weight:700"></span>"
                </p>
            </div>

            {{-- Footer --}}
            <div class="cmd-footer">
                <span class="cmd-kbd"><kbd>??</kbd> navigate</span>
                <span class="cmd-kbd"><kbd>?</kbd> open</span>
                <span class="cmd-kbd"><kbd>Esc</kbd> close</span>
                <span style="margin-left:auto;font-size:.68rem;font-weight:700;background:linear-gradient(135deg,#2D6CDF,#2D6CDF);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:.02em">StudAI CMD</span>
            </div>
        </div>
    </div>

    {{-- Floating AI Button (job seekers only) --}}
    @if(auth()->user()?->account_type !== 'employer')
    <a href="{{ route('agent.dashboard') }}"
        class="ai-fab-pulse fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full text-white flex items-center justify-center shadow-lg transition-transform hover:scale-110 active:scale-95"
        style="background:linear-gradient(135deg,#2D6CDF,#2D6CDF)"
        title="Open AI Agent">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    </a>
    @endif

    {{-- Toast Notifications --}}
    <x-ui.toast-container position="bottom-right" :max-toasts="5" :default-duration="4000" />

    @stack('scripts')
    @yield('scripts')
    @livewireScripts

    {{-- PWA Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(function (reg) {
                        console.debug('[SW] Registered:', reg.scope);
                    })
                    .catch(function (err) {
                        console.warn('[SW] Registration failed:', err);
                    });
            });
        }
    </script>
</body>
</html>
