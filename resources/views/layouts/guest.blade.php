<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sign in to StudAI Hire — India's first autonomous career OS.">
    <title>{{ $title ?? config('app.name', 'StudAI Hire') }}</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')

    <style>
        :root {
            --brand:      #6366f1;
            --brand-dark: #4f46e5;
            --brand-lite: #f0f0ff;
            --bg:         #f7f7fc;
            --surface:    #ffffff;
            --border:     #ebebf5;
            --text:       #1a1a2e;
            --text-2:     #4b5563;
            --text-3:     #9ca3af;
        }

        html { font-size:14px; }
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        @keyframes fadeInUp  { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        @keyframes gradShift { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
        @keyframes floatY    { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        @keyframes blobMove  { 0%,100%{transform:translate(0,0)scale(1)} 33%{transform:translate(30px,-40px)scale(1.05)} 66%{transform:translate(-20px,20px)scale(.97)} }

        /* ── GRAD TEXT ───────────────────────────── */
        .g-text {
            background: linear-gradient(135deg,#6366f1,#a855f7,#ec4899);
            background-size: 200%;
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradShift 4s ease infinite;
        }

        /* ── AUTH CARD ───────────────────────────── */
        .auth-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow:
                0 2px 4px rgba(99,102,241,.04),
                0 16px 48px rgba(99,102,241,.10),
                0 0 0 1px rgba(99,102,241,.04);
            animation: fadeInUp .5s ease both;
        }

        /* ── ROLE CARDS ──────────────────────────── */
        .role-card {
            border: 1.5px solid var(--border);
            border-radius: 14px;
            background: #f9f9ff;
            padding: 14px;
            cursor: pointer;
            transition: all .2s ease;
            position: relative;
            overflow: hidden;
        }
        .role-card:hover {
            border-color: rgba(99,102,241,.4);
            background: #f0f0ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(99,102,241,.12);
        }
        .role-card.active-seeker {
            border-color: #6366f1 !important;
            background: #f0f0ff !important;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15), 0 4px 16px rgba(99,102,241,.12) !important;
        }
        .role-card.active-employer {
            border-color: #a855f7 !important;
            background: #faf5ff !important;
            box-shadow: 0 0 0 3px rgba(168,85,247,.15), 0 4px 16px rgba(168,85,247,.12) !important;
        }

        /* ── AUTH INPUTS ─────────────────────────── */
        .auth-input {
            width: 100%;
            background: #f9f9ff;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 11px 14px;
            color: var(--text);
            font-size: .875rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .auth-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
            background: white;
        }
        .auth-input::placeholder { color: var(--text-3); }

        /* ── SUBMIT BUTTON ───────────────────────── */
        .btn-auth {
            width: 100%;
            padding: 13px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: .875rem;
            color: white;
            border: none;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease;
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            box-shadow: 0 4px 20px rgba(99,102,241,.35);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(99,102,241,.45);
        }
        .btn-auth:active { transform: translateY(0) scale(.99); }
        .btn-auth.employer-btn {
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            box-shadow: 0 4px 20px rgba(168,85,247,.35);
        }
        .btn-auth.employer-btn:hover { box-shadow: 0 8px 28px rgba(168,85,247,.45); }

        /* ── DIVIDER ─────────────────────────────── */
        .auth-divider {
            display: flex; align-items: center; gap: 12px;
            color: var(--text-3); font-size: .75rem;
        }
        .auth-divider::before, .auth-divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }

        /* ── LOGO FLOAT ──────────────────────────── */
        .logo-float { animation: floatY 4s ease-in-out infinite; }

        /* ── BG BLOBS ────────────────────────────── */
        .blob-auth {
            position: absolute; border-radius: 50%; pointer-events: none;
            animation: blobMove 10s ease-in-out infinite;
            filter: blur(60px);
        }
        .blob-auth-2 { animation-delay: 4s; }
    </style>
</head>
<body>

    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 overflow-hidden" style="background:#f7f7fc">

        {{-- Soft decorative blobs --}}
        <div class="blob-auth w-[500px] h-[500px] -top-48 -left-32 opacity-30"
             style="background:radial-gradient(circle,rgba(99,102,241,.18) 0%,transparent 70%)"></div>
        <div class="blob-auth blob-auth-2 w-[400px] h-[400px] -bottom-32 -right-24 opacity-25"
             style="background:radial-gradient(circle,rgba(168,85,247,.15) 0%,transparent 70%)"></div>
        <div class="blob-auth w-[300px] h-[300px] top-1/3 right-1/4 opacity-20"
             style="background:radial-gradient(circle,rgba(236,72,153,.10) 0%,transparent 70%)"></div>

        {{-- Dot grid --}}
        <div class="absolute inset-0 pointer-events-none" style="background-image:radial-gradient(rgba(99,102,241,.08) 1px,transparent 1px); background-size:28px 28px;"></div>

        {{-- Main content --}}
        <div class="relative z-10 w-full max-w-md flex flex-col items-center gap-6">

            {{-- Logo --}}
            <div class="logo-float flex flex-col items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="relative w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg overflow-hidden" style="background:linear-gradient(135deg,#6366f1,#7c3aed)">
                        <img src="{{ asset('assets/logo/icon.png') }}" alt="StudAI Hire" class="w-10 h-10 object-contain">
                        <span class="absolute -inset-2 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity" style="background:rgba(99,102,241,.15); filter:blur(10px)"></span>
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight" style="color:#1a1a2e">Stud<span class="g-text">AI</span> One</span>
                </a>

                {{-- Tagline pill --}}
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold" style="background:#f0f0ff; border:1.5px solid rgba(99,102,241,.2); color:#6366f1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 100 19 9.5 9.5 0 000-19zm1 12.28l-3.14-3.15A.75.75 0 019.5 8.5h1V5.25a.75.75 0 011.5 0V8.5h1a.75.75 0 01.53 1.28l-2.03 2.03z"/></svg>
                    Your Career. On Autopilot.
                </div>
            </div>

            {{-- Auth card --}}
            <div class="auth-card w-full px-8 py-8">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <p class="text-xs text-center" style="color:#9ca3af">
                &copy; {{ date('Y') }} <span class="g-text font-semibold">StudAI Hire</span>. Powered by <span style="color:#6366f1; font-weight:600">Orin™ AI</span>.
            </p>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
