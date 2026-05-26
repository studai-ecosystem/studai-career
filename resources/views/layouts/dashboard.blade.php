{{--
    StudAI Hire - Dashboard Layout
    Premium light SaaS design: Plus Jakarta Sans, #f7f7fc bg, #6366f1 brand indigo
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', isset($title) ? $title : 'Dashboard') - {{ config('app.name', 'StudAI Hire') }}</title>

    <!-- Fonts: Plus Jakarta Sans + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Favicon & PWA -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.svg">
    <meta name="theme-color" content="#6366f1">
    @stack('styles')

    <style>
        /* -- DESIGN TOKENS -- */
        :root {
            --brand:        #6366f1;
            --brand-hover:  #4f46e5;
            --brand-light:  #f0f0ff;
            --bg:           #f5f5fb; /* fallback */
            --surface:      #ffffff;
            --border:       #ebebf4;
            --text:         #1a1a2e;
            --text-2:       #374151;
            --text-muted:   #6b7280;
            --sidebar-w:    252px;
            --topbar-h:     64px;
            --ease:         cubic-bezier(0.4, 0, 0.2, 1);
            --dur:          220ms;
            /* module accents */
            --accent-coach:       #a855f7;
            --accent-interview:   #f97316;
            --accent-jobs:        #22c55e;
            --accent-market:      #3b82f6;
            --accent-negotiation: #eab308;
            --accent-scout:       #f43f5e;
            --accent-vantage:     #14b8a6;
            --accent-resume:      #8b5cf6;
            /* stat card accents */
            --stat-applications:  #6366f1;
            --stat-interviews:    #f97316;
            --stat-views:         #3b82f6;
            --stat-match:         #22c55e;
        }

        /* -- BASE -- */
        body { font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; line-height:1.6; background:#eef2ff; color:var(--text); -webkit-font-smoothing:antialiased; }
        .main-bg { background:linear-gradient(135deg, #e0e7ff 0%, #ede9fe 20%, #f5d0fe 40%, #fce7f3 60%, #dbeafe 80%, #d1fae5 100%); background-attachment:fixed; min-height:100vh; padding-top:80px; }
        h1,h2,h3,h4,h5,h6 { font-weight:700; letter-spacing:-0.02em; }
        .font-mono { font-family:'JetBrains Mono',monospace; }

        /* -- SIDEBAR / TOPBAR -- */
        aside  { background:linear-gradient(180deg, #f0edff 0%, #ede9fe 30%, #e9d5ff 65%, #f3e8ff 100%); border-right:1px solid rgba(167,139,250,.25); }
        header { background:linear-gradient(90deg, #e0e7ff 0%, #ede9fe 35%, #f5d0fe 65%, #dbeafe 100%); border-bottom:1px solid rgba(167,139,250,.2); height:var(--topbar-h); }

        /* -- NAV ITEMS -- */
        .nav-item { display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:10px; font-size:13.5px; font-weight:500; color:#5b21b6; transition:all var(--dur) var(--ease); cursor:pointer; text-decoration:none; }
        .nav-item:hover { background:rgba(139,92,246,.12); color:#4c1d95; }
        .nav-item:hover svg { transform:scale(1.1) rotate(-4deg); }
        .nav-item svg { width:18px !important; height:18px !important; flex-shrink:0; }
        .nav-sub { padding-left:28px; font-size:12.5px; padding-top:6px; padding-bottom:6px; opacity:.85; }
        .nav-item.active { background:rgba(109,40,217,.15); color:#4c1d95; font-weight:600; position:relative; }
        .nav-item.active::before { content:''; position:absolute; left:0; top:50%; transform:translateY(-50%); width:3px; height:65%; background:#7c3aed; border-radius:0 3px 3px 0; }
        .nav-active { position:relative; }
        .nav-active::before { content:''; position:absolute; left:0; top:50%; transform:translateY(-50%); width:3px; height:65%; border-radius:0 3px 3px 0; background:#7c3aed; }
        .nav-section-label { font-size:10.5px; font-weight:600; letter-spacing:.1em; text-transform:uppercase; color:#9333ea; padding:0 12px; margin-bottom:4px; margin-top:20px; display:block; }

        /* -- CARDS -- */
        .card { background:#ffffff; border:1px solid var(--border); border-radius:14px; box-shadow:0 1px 3px rgb(0 0 0 / 0.05); transition:transform var(--dur) var(--ease),box-shadow var(--dur) var(--ease); }
        .card:hover { box-shadow:0 8px 32px rgba(99,102,241,.10); transform:translateY(-3px); }
        .card-static { background:#ffffff; border:1px solid var(--border); border-radius:14px; box-shadow:0 1px 3px rgb(0 0 0 / 0.05); }
        .card-lift { transition:transform var(--dur) var(--ease),box-shadow var(--dur) var(--ease); }
        .card-lift:hover { transform:translateY(-3px); box-shadow:0 8px 32px rgba(99,102,241,.10); }
        .shadow-card { box-shadow:0 1px 3px rgb(0 0 0 / 0.05); }

        /* -- BUTTONS -- */
        .btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:10px; font-size:13.5px; font-weight:600; cursor:pointer; border:none; transition:all var(--dur) var(--ease); text-decoration:none; }
        .btn:hover { transform:translateY(-2px); }
        .btn:active { transform:translateY(0) scale(.98); }

        /* Primary */
        .btn-primary { background:#6366f1; color:#fff; box-shadow:0 4px 14px rgba(99,102,241,.28); }
        .btn-primary:hover { background:#4f46e5; box-shadow:0 6px 20px rgba(99,102,241,.38); }

        /* Secondary */
        .btn-secondary { background:#ffffff; color:#374151; border:1px solid #ddddf0; box-shadow:0 1px 2px rgba(0,0,0,.04); }
        .btn-secondary:hover { border-color:#6366f1; color:#6366f1; }

        /* Success */
        .btn-success { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
        .btn-success:hover { background:#dcfce7; }

        /* Danger */
        .btn-danger { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
        .btn-danger:hover { background:#ffe4e6; }

        /* Ghost */
        .btn-ghost { background:transparent; color:#6b7280; border:1px solid transparent; }
        .btn-ghost:hover { background:#f5f5fb; color:var(--text); }

        /* -- STAT CARD ACCENTS -- */
        .stat-applications .stat-icon { background:rgba(99,102,241,.10); color:#6366f1; }
        .stat-applications .stat-value { color:#6366f1; }
        .stat-interviews   .stat-icon { background:rgba(249,115,22,.10); color:#f97316; }
        .stat-interviews   .stat-value { color:#f97316; }
        .stat-views        .stat-icon { background:rgba(59,130,246,.10);  color:#3b82f6; }
        .stat-views        .stat-value { color:#3b82f6; }
        .stat-match        .stat-icon { background:rgba(34,197,94,.10);   color:#22c55e; }
        .stat-match        .stat-value { color:#22c55e; }

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
        @keyframes aiFabPulse{ 0%,100%{box-shadow:0 0 0 0 rgba(99,102,241,.4)} 50%{box-shadow:0 0 0 14px rgba(99,102,241,0)} }
        @keyframes alertPulse{ 0%,100%{transform:scale(1)} 50%{transform:scale(1.25)} }
        .animate-fade-in    { animation:fadeIn .3s var(--ease) both; }
        .animate-fade-up    { animation:fadeUp .4s var(--ease) both; }
        .animate-scale-in   { animation:scaleIn .22s var(--ease) both; }
        .animate-pulse-soft { animation:pulseSoft 2s ease-in-out infinite; }
        .animate-bounce-soft{ animation:bounceSoft 1.2s ease-in-out infinite; }
        .animate-spin-slow  { animation:spinSlow 3s linear infinite; }
        .animate-shimmer    { animation:shimmer 1.5s linear infinite; background:linear-gradient(90deg,#f0f0f8 25%,#e8e8f4 50%,#f0f0f8 75%); background-size:200%; }
        .skeleton           { background:linear-gradient(90deg,#f0f0f8 25%,#e8e8f4 50%,#f0f0f8 75%); background-size:200%; animation:shimmer 1.5s linear infinite; border-radius:8px; }
        .skill-bar-fill     { width:0; transition:width 1s cubic-bezier(.4,0,.2,1); }
        .xp-bar             { background:linear-gradient(90deg,#6366f1,#a855f7,#ec4899); background-size:200%; animation:shimmer 2.5s linear infinite; }
        #cmd-palette        { backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); }
        .ai-fab-pulse       { animation:aiFabPulse 2.5s ease-in-out infinite; }
        .alert-pulse        { animation:alertPulse 1.5s ease-in-out infinite; }
        .completeness-ring  { transform:rotate(-90deg); }
        .completeness-ring circle { transition:stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1); }
        .dark { --bg:#0f172a; --surface:#1e293b; --border:#334155; --text:#f1f5f9; --text-muted:#94a3b8; }
        /* Input styles that work in both light and dark mode */
        .input-google { display:block; width:100%; border-radius:10px; border:1px solid #ddd6fe; background-color:#ffffff; padding:10px 14px; font-size:14px; color:#1a1a2e; transition:all .15s ease; }
        .input-google:focus { outline:none; border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,.12); }
        .input-google::placeholder { color:#9ca3af; }
        .dark .input-google { background-color:#1e293b; border-color:#334155; color:#f1f5f9; }
        .dark .input-google::placeholder { color:#64748b; }
        .dark body  { background:#0f172a; color:#f1f5f9; }
        .dark aside { background:#1e293b; border-color:#334155; }
        .dark header{ background:rgba(15,23,42,.92); border-color:#334155; }
        .scrollbar-thin::-webkit-scrollbar { width:4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background:transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background:#e5e7eb; border-radius:999px; }
        .glow-coach{box-shadow:0 0 24px rgba(168,85,247,.20)} .glow-interview{box-shadow:0 0 24px rgba(249,115,22,.20)}
        .glow-jobs {box-shadow:0 0 24px rgba(34,197,94,.20)}  .glow-market{box-shadow:0 0 24px rgba(59,130,246,.20)}
        .glow-negotiation{box-shadow:0 0 24px rgba(234,179,8,.20)} .glow-scout{box-shadow:0 0 24px rgba(244,63,94,.20)}
        .glow-vantage{box-shadow:0 0 24px rgba(20,184,166,.20)}
        .count-up { font-variant-numeric:tabular-nums; }
    </style>
</head>
<body class="antialiased" x-data="{
    sidebarOpen: true,
    sidebarMobileOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    cmdOpen: false
}" :class="{ 'dark': darkMode }"
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
                    <img src="/assets/logo/icon.png" alt="StudAI Hire" style="width:32px;height:32px;object-fit:contain;flex-shrink:0">
                    <span x-show="sidebarOpen" x-transition class="truncate" style="font-size:13px;font-weight:700;letter-spacing:-0.01em">
                        <span style="background:linear-gradient(135deg,#6366f1,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Stud<span style="font-weight:800">AI</span></span><span style="color:#6b21a8;font-weight:500;margin-left:2px">One</span>
                    </span>
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

                <a href="{{ route('negotiation.dashboard') }}"
                   class="nav-item {{ request()->routeIs('negotiation.*') ? 'active' : '' }}"
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Negotiation</span>
                </a>
                <a href="{{ route('negotiation.chatbot') }}"
                   class="nav-item nav-sub {{ request()->routeIs('negotiation.chatbot') ? 'active' : '' }}"
                   x-show="sidebarOpen" x-transition
                   :class="!sidebarOpen && 'justify-center'">
                    <svg class="w-[16px] h-[16px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition>AI Negotiation Agent</span>
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
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold text-white" style="background:linear-gradient(135deg,#6366f1,#a855f7)">
                        {{ $userInitial }}
                    </div>
                    <div x-show="sidebarOpen" x-transition class="flex-1 min-w-0">
                        <div class="text-sm font-semibold truncate" style="color:#1e1b4b">{{ $userName }}</div>
                        <div class="text-xs truncate" style="color:#6b7280">{{ $userEmail }}</div>
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
                        <kbd class="px-1 py-0.5 text-[10px] rounded" style="background:#e5e7eb">Ctrl K</kbd>
                    </button>

                    {{-- AI Active pill --}}
                    <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        AI Active
                    </div>

                    {{-- Dark mode toggle --}}
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                        class="p-2 rounded-lg transition-colors hover:bg-gray-100"
                        style="color:var(--text-muted)">
                        <svg x-show="!darkMode" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    {{-- Notifications --}}
                    @php
                        $dbNotifications = auth()->user()?->notifications()->latest()->take(8)->get() ?? collect();
                        $unreadCount = auth()->user()?->unreadNotifications()->count() ?? 0;

                        // Icon/color map by notification type
                        $notifStyles = [
                            'application'  => ['bg'=>'#f0f0ff','color'=>'#6366f1','dot'=>'#6366f1'],
                            'interview'    => ['bg'=>'#fff7ed','color'=>'#f97316','dot'=>'#f97316'],
                            'pipeline'     => ['bg'=>'#fff7ed','color'=>'#f97316','dot'=>'#f97316'],
                            'test'         => ['bg'=>'#eff6ff','color'=>'#1A73E8','dot'=>'#1A73E8'],
                            'scout'        => ['bg'=>'#fdf4ff','color'=>'#a855f7','dot'=>'#a855f7'],
                            'shortlisted'  => ['bg'=>'#f0fdf4','color'=>'#22c55e','dot'=>'#22c55e'],
                            'hired'        => ['bg'=>'#f0fdf4','color'=>'#16a34a','dot'=>'#16a34a'],
                            'rejected'     => ['bg'=>'#fef2f2','color'=>'#ef4444','dot'=>'#ef4444'],
                            'job'          => ['bg'=>'#eff6ff','color'=>'#3b82f6','dot'=>'#3b82f6'],
                            'default'      => ['bg'=>'#f9fafb','color'=>'#6b7280','dot'=>'#6b7280'],
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
                            style="width:38px;height:38px;background:var(--bg);border:1.5px solid var(--border);color:#6366f1;box-shadow:0 1px 3px rgba(0,0,0,.06);"
                            :style="open ? 'background:#f0f0ff;border-color:#6366f1' : ''">
                            <svg style="width:19px;height:19px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($unreadCount > 0)
                            <span style="position:absolute;top:4px;right:4px;min-width:16px;height:16px;padding:0 3px;border-radius:99px;background:#ef4444;border:1.5px solid white;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:#fff;line-height:1;">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                            @else
                            <span style="position:absolute;top:5px;right:5px;width:8px;height:8px;border-radius:50%;background:#d1d5db;border:1.5px solid white;display:block;"></span>
                            @endif
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open"
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            style="position:absolute;top:calc(100% + 10px);right:0;width:340px;background:white;border:1px solid #ebebf5;border-radius:18px;box-shadow:0 8px 40px rgba(99,102,241,.13),0 2px 12px rgba(0,0,0,.08);overflow:hidden;z-index:9999;">

                            {{-- Header --}}
                            <div style="padding:14px 18px 11px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="font-size:15px;font-weight:700;color:#1a1a2e">Notifications</span>
                                    @if($unreadCount > 0)
                                    <span style="padding:2px 9px;font-size:11px;font-weight:700;border-radius:99px;background:#f0f0ff;color:#6366f1;border:1px solid #e0e0ff;">{{ $unreadCount }} new</span>
                                    @endif
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" style="font-size:11px;font-weight:600;color:#6366f1;background:none;border:none;cursor:pointer;padding:0;">Mark all read</button>
                                    </form>
                                    @endif
                                    <button @click="open = false" style="color:#9ca3af;background:none;border:none;cursor:pointer;padding:2px;">
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
                                   onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
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
                                        <div style="font-size:13px;font-weight:{{ $isUnread ? '600' : '500' }};color:{{ $isUnread ? '#1a1a2e' : '#4b5563' }};line-height:1.4;">
                                            {{ Str::limit($message, 70) }}
                                        </div>
                                        <div style="font-size:11px;color:#9ca3af;margin-top:3px;">{{ $timeAgo }}</div>
                                    </div>
                                </a>
                                @empty
                                <div style="padding:2rem 1rem;text-align:center;">
                                    <svg style="width:2.5rem;height:2.5rem;color:#d1d5db;margin:0 auto .75rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <p style="font-size:13px;font-weight:600;color:#374151;">No notifications</p>
                                    <p style="font-size:12px;color:#9ca3af;margin-top:4px;">You're all caught up!</p>
                                </div>
                                @endforelse
                            </div>

                            {{-- Footer --}}
                            <div style="padding:10px 18px 13px;border-top:1px solid #f3f4f6;display:flex;justify-content:center;">
                                <a href="{{ route('notifications.all') }}" style="font-size:13px;font-weight:600;color:#6366f1;text-decoration:none;">View all notifications &#8594;</a>
                            </div>
                        </div>
                    </div>

                    {{-- Profile Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-xl transition-colors hover:bg-gray-100">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg,#6366f1,#a855f7)">
                                @php echo strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)); @endphp
                            </div>
                            <svg class="w-3.5 h-3.5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false"
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
        background: linear-gradient(145deg,#fdfcff 0%,#f5f3ff 40%,#fdf4ff 70%,#f0f4ff 100%);
        border: 1.5px solid rgba(99,102,241,.18);
        border-radius: 1.5rem;
        box-shadow: 0 24px 80px rgba(99,102,241,.22), 0 4px 20px rgba(139,92,246,.12), 0 0 0 1px rgba(255,255,255,.6) inset;
        overflow: hidden;
        margin-top: -80px;
    }
    .cmd-search-row {
        display:flex; align-items:center; gap:.75rem;
        padding:.9rem 1.25rem;
        background: linear-gradient(90deg,rgba(99,102,241,.07) 0%,rgba(139,92,246,.05) 100%);
        border-bottom: 1.5px solid rgba(99,102,241,.1);
    }
    .cmd-search-icon {
        width:2rem; height:2rem; border-radius:.625rem; flex-shrink:0;
        background: linear-gradient(135deg,#6366f1,#a855f7);
        display:flex; align-items:center; justify-content:center;
        box-shadow: 0 3px 10px rgba(99,102,241,.35);
    }
    .cmd-input {
        flex:1; background:transparent; outline:none; border:none;
        font-size:.9375rem; font-weight:500; letter-spacing:-.01em;
        color:#1a1a2e;
        font-family:'Plus Jakarta Sans',sans-serif;
    }
    .cmd-input::placeholder { color:#a5b4fc; font-weight:400; }
    .cmd-esc-badge {
        padding:.25rem .6rem; border-radius:.5rem; font-size:.7rem; font-weight:700;
        background: linear-gradient(135deg,#eef2ff,#e0e7ff);
        color:#6366f1; border:1px solid rgba(99,102,241,.2);
        font-family:'JetBrains Mono',monospace;
    }
    .cmd-section-label {
        font-size:.67rem; font-weight:800; letter-spacing:.1em; text-transform:uppercase;
        color:#a5b4fc; padding:.75rem 1.25rem .3rem; display:block;
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
        background: linear-gradient(90deg,rgba(99,102,241,.08) 0%,rgba(139,92,246,.05) 100%);
        transform: translateX(3px);
    }
    .cmd-item:active { transform:translateX(3px) scale(.98); }
    .cmd-item .ripple {
        position:absolute; border-radius:50%;
        background:rgba(99,102,241,.25);
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
        font-size:.875rem; font-weight:600; color:#1a1a2e;
        letter-spacing:-.01em; font-family:'Plus Jakarta Sans',sans-serif;
        flex:1;
    }
    .cmd-arrow {
        font-size:.75rem; color:#c4b5fd; opacity:0;
        transition: opacity .15s, transform .15s;
        font-weight:700;
    }
    .cmd-item:hover .cmd-arrow { opacity:1; transform:translateX(3px); }
    .cmd-footer {
        padding:.625rem 1.25rem;
        background: linear-gradient(90deg,rgba(99,102,241,.05) 0%,rgba(139,92,246,.04) 100%);
        border-top:1.5px solid rgba(99,102,241,.1);
        display:flex; align-items:center; gap:1rem;
    }
    .cmd-kbd {
        display:inline-flex; align-items:center; gap:.35rem;
        font-size:.7rem; color:#7c6fd4; font-weight:600;
    }
    .cmd-kbd kbd {
        padding:.2rem .5rem; border-radius:.375rem;
        background:linear-gradient(135deg,#eef2ff,#e0e7ff);
        border:1px solid rgba(99,102,241,.2);
        color:#6366f1; font-size:.65rem; font-weight:700;
        font-family:'JetBrains Mono',monospace;
        box-shadow:0 1px 3px rgba(99,102,241,.15);
    }
    </style>
    <div id="cmd-palette"
        x-show="cmdOpen"
        @click="cmdOpen = false"
        class="fixed inset-0 flex items-center justify-center px-4"
        style="z-index:9999; background:linear-gradient(135deg,rgba(99,102,241,.18) 0%,rgba(139,92,246,.15) 50%,rgba(236,72,153,.12) 100%); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);"
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
            <div class="py-1 max-h-80 overflow-y-auto" style="scrollbar-width:thin;scrollbar-color:rgba(99,102,241,.2) transparent">
                @php
                    $cmdItems = [
                        ['href'=>route('dashboard'),            'label'=>'Dashboard',            'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',       'grad'=>'linear-gradient(135deg,#6366f1,#818cf8)', 'shadow'=>'rgba(99,102,241,.35)'],
                        ['href'=>route('resume.index'),         'label'=>'Resume Builder',        'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',                               'grad'=>'linear-gradient(135deg,#8b5cf6,#a855f7)', 'shadow'=>'rgba(139,92,246,.35)'],
                        ['href'=>route('interview.index'),      'label'=>'Interview Lab',         'icon'=>'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',               'grad'=>'linear-gradient(135deg,#f97316,#fb923c)', 'shadow'=>'rgba(249,115,22,.35)'],
                        ['href'=>route('jobs.search'),          'label'=>'Job Search',            'icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',                                                                                                       'grad'=>'linear-gradient(135deg,#22c55e,#4ade80)', 'shadow'=>'rgba(34,197,94,.35)'],
                        ['href'=>route('career-coach.index'),   'label'=>'Career Coach',          'icon'=>'M13 10V3L4 14h7v7l9-11h-7z',                                                                                                                        'grad'=>'linear-gradient(135deg,#ec4899,#f472b6)', 'shadow'=>'rgba(236,72,153,.35)'],
                        ['href'=>route('negotiation.dashboard'),'label'=>'Negotiation Strategist','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'grad'=>'linear-gradient(135deg,#7c3aed,#a855f7)', 'shadow'=>'rgba(124,58,237,.35)'],
                        ['href'=>route('agent.dashboard'),      'label'=>'AI Agent',              'icon'=>'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',                                       'grad'=>'linear-gradient(135deg,#06b6d4,#22d3ee)', 'shadow'=>'rgba(6,182,212,.35)'],
                        ['href'=>route('profile.edit'),         'label'=>'Profile Settings',      'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',                                                                              'grad'=>'linear-gradient(135deg,#64748b,#94a3b8)', 'shadow'=>'rgba(100,116,139,.35)'],
                        ['href'=>route('subscriptions.pricing'),'label'=>'Upgrade Plan',          'icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',                                        'grad'=>'linear-gradient(135deg,#f43f5e,#fb7185)', 'shadow'=>'rgba(244,63,94,.35)'],
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
                    <span class="cmd-label" x-html="q !== '' ? '{{ $cmd['label'] }}'.replace(new RegExp(q, 'gi'), m => '<mark style=\'background:linear-gradient(135deg,#eef2ff,#ddd6fe);color:#6366f1;border-radius:.25rem;padding:0 .2rem;font-weight:700\'>'+m+'</mark>') : '{{ $cmd['label'] }}'"></span>
                    <span class="cmd-arrow">&#8594;</span>
                </a>
                @endforeach

                <p x-show="q !== '' && !{{ json_encode(array_map(fn($c) => strtolower($c['label']), $cmdItems)) }}.some(l => l.includes(q.toLowerCase()))"
                   style="padding:1.5rem 1.25rem;text-align:center;font-size:.84rem;color:#a5b4fc;font-weight:500;font-family:'Plus Jakarta Sans',sans-serif;">
                    No results for "<span x-text="q" style="color:#6366f1;font-weight:700"></span>" ??
                </p>
            </div>

            {{-- Footer --}}
            <div class="cmd-footer">
                <span class="cmd-kbd"><kbd>??</kbd> navigate</span>
                <span class="cmd-kbd"><kbd>?</kbd> open</span>
                <span class="cmd-kbd"><kbd>Esc</kbd> close</span>
                <span style="margin-left:auto;font-size:.68rem;font-weight:700;background:linear-gradient(135deg,#6366f1,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:.02em">StudAI CMD</span>
            </div>
        </div>
    </div>

    {{-- Floating AI Button (job seekers only) --}}
    @if(auth()->user()?->account_type !== 'employer')
    <a href="{{ route('agent.dashboard') }}"
        class="ai-fab-pulse fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full text-white flex items-center justify-center shadow-lg transition-transform hover:scale-110 active:scale-95"
        style="background:linear-gradient(135deg,#6366f1,#a855f7)"
        title="Open AI Agent">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    </a>
    @endif

    {{-- Toast Notifications --}}
    <x-ui.toast-container position="bottom-right" :max-toasts="5" :default-duration="4000" />

    @stack('scripts')
    @livewireScripts
</body>
</html>
