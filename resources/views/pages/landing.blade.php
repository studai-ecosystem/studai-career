{{--
    StudAI Hire � India's First Autonomous Career OS
    Ultra-clean, modern design with Google/Material Design aesthetics
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- SEO Meta Tags --}}
    <title>StudAI Hire - India's First Autonomous Career OS | AI Job Search & Hiring</title>
    <meta name="description" content="StudAI Hire is an AI-powered Career OS that finds jobs, applies automatically, preps interviews, and negotiates salaries. For job seekers, freelancers & employers.">
    <meta name="keywords" content="AI job search, autonomous job applications, career OS, AI resume builder, interview preparation, S.C.O.U.T. ATS, StudAI Hire">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="Your Career. On Autopilot. | StudAI Hire">
    <meta property="og:description" content="India's first Autonomous Career OS. AI finds jobs, applies for you, preps interviews, and negotiates salaries.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('images/og-studai-path.svg') }}">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Your Career. On Autopilot. | StudAI Hire">
    <meta name="twitter:description" content="India's first Autonomous Career OS. AI finds jobs, applies for you, preps interviews, and negotiates salaries.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* -- HERO GRADIENT --------------------------- */
        .hero-gradient {
            background:
                radial-gradient(ellipse 70% 55% at 15% 20%, rgba(99,102,241,.28) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 85% 10%, rgba(236,72,153,.22) 0%, transparent 60%),
                radial-gradient(ellipse 55% 45% at 70% 85%, rgba(16,185,129,.18) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 30% 75%, rgba(245,158,11,.16) 0%, transparent 60%),
                radial-gradient(ellipse 80% 60% at 50% 50%, rgba(139,92,246,.14) 0%, transparent 80%),
                linear-gradient(145deg, #eef1ff 0%, #f9f0ff 30%, #eefff7 60%, #fff8ee 100%);
        }

        /* -- KEYFRAMES ------------------------------- */
        @keyframes floating    { 0%,100%{transform:translateY(0)}    50%{transform:translateY(-18px)} }
        @keyframes floatSlow   { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-12px) rotate(3deg)} }
        @keyframes gradShift   { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
        @keyframes pulseGlow   { 0%,100%{box-shadow:0 0 0 0 rgba(26,115,232,.4)} 50%{box-shadow:0 0 0 14px rgba(26,115,232,0)} }
        @keyframes fadeUp      { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }
        @keyframes fadeIn      { from{opacity:0} to{opacity:1} }
        @keyframes slideInLeft { from{opacity:0;transform:translateX(-36px)} to{opacity:1;transform:translateX(0)} }
        @keyframes slideInRight{ from{opacity:0;transform:translateX(36px)} to{opacity:1;transform:translateX(0)} }
        @keyframes scaleIn     { from{opacity:0;transform:scale(.88)} to{opacity:1;transform:scale(1)} }
        @keyframes shimmer     { to{background-position:-200% center} }
        @keyframes orb1        { 0%,100%{transform:translate(0,0) scale(1)}   33%{transform:translate(40px,-55px) scale(1.12)} 66%{transform:translate(-20px,30px) scale(.95)} }
        @keyframes orb2        { 0%,100%{transform:translate(0,0) scale(1)}   33%{transform:translate(-35px,45px) scale(.9)}  66%{transform:translate(25px,-30px) scale(1.08)} }
        @keyframes orb3        { 0%,100%{transform:translate(0,0)}            33%{transform:translate(30px,20px)} 66%{transform:translate(-20px,-35px)} }
        @keyframes orb4        { 0%,100%{transform:translate(0,0) scale(1)}   50%{transform:translate(20px,-25px) scale(1.1)} }
        @keyframes orb5        { 0%,100%{transform:translate(0,0) scale(1)}   50%{transform:translate(-30px,20px) scale(.92)} }
        @keyframes typewriter  { from{width:0} to{width:100%} }
        @keyframes blink       { 50%{border-color:transparent} }
        @keyframes tickerX     { from{transform:translateX(0)} to{transform:translateX(-50%)} }
        @keyframes countUp     { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        @keyframes cardHover   { to{transform:translateY(-4px);box-shadow:0 20px 40px rgba(99,102,241,.14)} }
        @keyframes spinSlow    { to{transform:rotate(360deg)} }
        @keyframes ringPulse   { 0%,100%{transform:scale(1);opacity:.5} 50%{transform:scale(1.15);opacity:.2} }
        @keyframes morphBlob   { 0%,100%{border-radius:60% 40% 30% 70%/60% 30% 70% 40%} 33%{border-radius:30% 60% 70% 40%/50% 60% 30% 60%} 66%{border-radius:50% 60% 30% 40%/40% 50% 60% 50%} }
        @keyframes driftX      { 0%,100%{transform:translateX(0) rotate(0deg)} 50%{transform:translateX(18px) rotate(8deg)} }
        @keyframes driftY      { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-16px) rotate(-5deg)} }
        @keyframes particleFly { 0%{opacity:1;transform:translate(0,0) scale(1)} 100%{opacity:0;transform:translate(var(--tx),var(--ty)) scale(0)} }
        @keyframes meshMove    { 0%,100%{background-position:0% 0%,100% 100%,50% 50%} 50%{background-position:100% 100%,0% 0%,80% 20%} }

        /* -- ANIMATED ELEMENTS ----------------------- */
        .floating     { animation: floating  6s ease-in-out infinite; }
        .float-slow   { animation: floatSlow 8s ease-in-out infinite; }
        .spin-slow    { animation: spinSlow  20s linear infinite; }

        /* Animated gradient text */
        .text-gradient-anim {
            background: linear-gradient(135deg, #1a73e8, #7c3aed, #ec4899, #1a73e8);
            background-size: 300% auto;
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradShift 4s ease infinite;
        }

        /* Orbs � now more vivid and more of them */
        .orb { position:absolute; border-radius:50%; filter:blur(64px); pointer-events:none; will-change:transform; }
        .orb-1 { width:600px;height:600px; background:radial-gradient(circle, rgba(99,102,241,.35) 0%, rgba(139,92,246,.2) 40%, transparent 70%); top:-200px;left:-150px; animation:orb1 14s ease-in-out infinite; }
        .orb-2 { width:500px;height:500px; background:radial-gradient(circle, rgba(236,72,153,.3) 0%, rgba(245,101,101,.15) 40%, transparent 70%); bottom:-120px;right:-100px; animation:orb2 12s ease-in-out infinite; }
        .orb-3 { width:350px;height:350px; background:radial-gradient(circle, rgba(16,185,129,.25) 0%, rgba(52,211,153,.12) 40%, transparent 70%); top:25%;right:8%; animation:orb3 16s ease-in-out infinite; }
        .orb-4 { width:280px;height:280px; background:radial-gradient(circle, rgba(245,158,11,.28) 0%, rgba(251,191,36,.12) 40%, transparent 70%); top:55%;left:5%; animation:orb4 11s ease-in-out 2s infinite; }
        .orb-5 { width:220px;height:220px; background:radial-gradient(circle, rgba(59,130,246,.3)  0%, rgba(14,165,233,.12) 40%, transparent 70%); bottom:15%;left:30%; animation:orb5 9s ease-in-out 4s infinite; }

        /* Morphing blobs */
        .blob { position:absolute; pointer-events:none; opacity:.18; }
        .blob-1 { width:200px;height:200px; background:linear-gradient(135deg,#6366f1,#a855f7); border-radius:60% 40% 30% 70%/60% 30% 70% 40%; top:15%;left:3%; animation:morphBlob 12s ease-in-out infinite, driftY 8s ease-in-out infinite; }
        .blob-2 { width:140px;height:140px; background:linear-gradient(135deg,#ec4899,#f43f5e); border-radius:40% 60% 60% 40%/40% 60% 40% 60%; top:10%;right:4%; animation:morphBlob 10s ease-in-out 3s infinite, driftX 7s ease-in-out infinite; }
        .blob-3 { width:120px;height:120px; background:linear-gradient(135deg,#10b981,#34d399); border-radius:50% 60% 40% 50%/50% 40% 60% 50%; bottom:20%;right:6%; animation:morphBlob 14s ease-in-out 6s infinite, driftY 10s ease-in-out 2s infinite; }

        /* Animated rings */
        .ring { position:absolute; border-radius:50%; border:2px solid; pointer-events:none; animation:ringPulse 4s ease-in-out infinite; }
        .ring-1 { width:120px;height:120px; border-color:rgba(99,102,241,.25); top:20%;left:12%; animation-delay:0s; }
        .ring-2 { width:80px;height:80px; border-color:rgba(236,72,153,.25); top:65%;right:15%; animation-delay:1.5s; }
        .ring-3 { width:60px;height:60px; border-color:rgba(16,185,129,.3); bottom:30%;left:20%; animation-delay:3s; }

        /* Dot grid sparkle */
        .dot-grid {
            background-image: radial-gradient(circle, rgba(99,102,241,.18) 1px, transparent 1px);
            background-size: 36px 36px;
        }

        /* Grid noise overlay */
        .grid-noise {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%236366f1' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Scroll-reveal */
        .reveal { opacity:0; transform:translateY(32px); transition:opacity .7s cubic-bezier(.22,1,.36,1), transform .7s cubic-bezier(.22,1,.36,1); }
        .reveal.visible { opacity:1; transform:translateY(0); }
        .reveal-left  { opacity:0; transform:translateX(-36px); transition:opacity .7s ease, transform .7s ease; }
        .reveal-left.visible  { opacity:1; transform:translateX(0); }
        .reveal-right { opacity:0; transform:translateX(36px);  transition:opacity .7s ease, transform .7s ease; }
        .reveal-right.visible { opacity:1; transform:translateX(0); }
        .reveal-scale { opacity:0; transform:scale(.9); transition:opacity .6s ease, transform .6s ease; }
        .reveal-scale.visible { opacity:1; transform:scale(1); }
        .delay-100{transition-delay:.1s} .delay-200{transition-delay:.2s} .delay-300{transition-delay:.3s}
        .delay-400{transition-delay:.4s} .delay-500{transition-delay:.5s} .delay-600{transition-delay:.6s}

        /* Hero entrance animations */
        .hero-badge { animation: fadeUp .6s ease .1s both; }
        .hero-h1    { animation: fadeUp .7s ease .25s both; }
        .hero-sub   { animation: fadeUp .7s ease .4s both; }
        .hero-form  { animation: fadeUp .7s ease .55s both; }
        .hero-cta   { animation: fadeUp .7s ease .7s both; }
        .hero-stats { animation: fadeUp .7s ease .85s both; }

        /* Card hover effect */
        .anim-card { transition: transform .3s ease, box-shadow .3s ease; }
        .anim-card:hover { transform: translateY(-5px); box-shadow: 0 20px 48px rgba(99,102,241,.16); }

        /* Shimmer button */
        .btn-shimmer {
            background: linear-gradient(135deg, #1a73e8 0%, #7c3aed 50%, #1a73e8 100%);
            background-size: 200% auto;
            animation: shimmer 2.5s linear infinite;
            color: #fff; border: none;
        }
        .btn-shimmer:hover { opacity: .92; }

        /* Search glow */
        .search-glow:focus-within { box-shadow: 0 0 0 4px rgba(26,115,232,.15), 0 8px 32px rgba(26,115,232,.1); }

        /* Pulsing CTA ring */
        .pulse-btn { animation: pulseGlow 2.5s ease-in-out infinite; }

        /* Feature icon glow */
        .icon-glow { transition: filter .3s; }
        .anim-card:hover .icon-glow { filter: drop-shadow(0 0 8px rgba(99,102,241,.5)); }

        /* Ticker tape */
        .ticker-wrap { overflow:hidden; }
        .ticker      { display:flex; gap:48px; animation:tickerX 20s linear infinite; white-space:nowrap; }

        /* Particle dots */
        .particle-dot { position:absolute; width:6px; height:6px; border-radius:50%; pointer-events:none; }

        /* Number counter */
        .stat-counter { animation: countUp 1.2s ease-out both; }
    </style>
</head>
<body class="font-sans antialiased text-ink-primary" style="background:#f4f6ff;">
    {{-- Navigation --}}
    <nav class="fixed top-0 left-0 right-0 z-sticky backdrop-blur-xl" style="background:rgba(255,255,255,0.72);border-bottom:1.5px solid transparent;background-clip:padding-box;box-shadow:0 1px 0 rgba(99,102,241,.15),0 4px 24px rgba(99,102,241,.08);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2 group">
                    <img src="/assets/logo/icon.png?v=3" alt="StudAI Hire" style="width:36px;height:36px;object-fit:contain;flex-shrink:0" class="transition-transform group-hover:scale-110">
                    <span class="text-xl font-bold" style="color:#1a1a2e">Stud<span style="background:linear-gradient(135deg,#1a73e8,#7c3aed);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">AI</span> One</span>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm font-medium transition-colors" style="color:#4b5563;" onmouseover="this.style.color='#6366f1'" onmouseout="this.style.color='#4b5563'">Features</a>
                    <a href="{{ route('pricing') }}" class="text-sm font-medium transition-colors" style="color:#4b5563;" onmouseover="this.style.color='#6366f1'" onmouseout="this.style.color='#4b5563'">Pricing</a>
                    <a href="{{ route('employers') }}" class="text-sm font-medium transition-colors" style="color:#4b5563;" onmouseover="this.style.color='#6366f1'" onmouseout="this.style.color='#4b5563'">For Employers</a>
                    <a href="{{ route('about') }}" class="text-sm font-medium transition-colors" style="color:#4b5563;" onmouseover="this.style.color='#6366f1'" onmouseout="this.style.color='#4b5563'">About</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="flex items-center" style="gap:.75rem;">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#6366f1,#7c3aed);box-shadow:0 2px 10px rgba(99,102,241,.35);">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium hidden sm:block" style="color:#4b5563;">Sign in</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#6366f1,#7c3aed);box-shadow:0 2px 10px rgba(99,102,241,.35);">
                            Start Free
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{-- ============================================
            SECTION 1: HERO
            "Your Career. On Autopilot."
        ============================================ --}}
        <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-32 hero-gradient dot-grid overflow-hidden">
            {{-- Animated background orbs --}}
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
            <div class="orb orb-4"></div>
            <div class="orb orb-5"></div>
            {{-- Morphing blobs --}}
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>
            {{-- Animated rings --}}
            <div class="ring ring-1"></div>
            <div class="ring ring-2"></div>
            <div class="ring ring-3"></div>
            {{-- Floating particles --}}
            <div class="particle-dot" style="background:linear-gradient(135deg,#6366f1,#a855f7);top:18%;left:8%;animation:floatSlow 7s ease-in-out infinite;width:10px;height:10px;box-shadow:0 0 12px rgba(99,102,241,.6);"></div>
            <div class="particle-dot" style="background:linear-gradient(135deg,#ec4899,#f43f5e);top:60%;left:5%;animation:floatSlow 9s ease-in-out 1s infinite;width:8px;height:8px;box-shadow:0 0 10px rgba(236,72,153,.5);"></div>
            <div class="particle-dot" style="background:linear-gradient(135deg,#10b981,#34d399);top:25%;right:7%;animation:floatSlow 8s ease-in-out 2s infinite;width:10px;height:10px;box-shadow:0 0 12px rgba(16,185,129,.5);"></div>
            <div class="particle-dot" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);top:70%;right:10%;animation:floatSlow 6s ease-in-out .5s infinite;width:8px;height:8px;box-shadow:0 0 10px rgba(245,158,11,.5);"></div>
            <div class="particle-dot" style="background:linear-gradient(135deg,#3b82f6,#0ea5e9);top:40%;right:3%;animation:floatSlow 11s ease-in-out 3s infinite;width:6px;height:6px;box-shadow:0 0 8px rgba(59,130,246,.6);"></div>
            <div class="particle-dot" style="background:linear-gradient(135deg,#8b5cf6,#d946ef);top:80%;left:25%;animation:floatSlow 13s ease-in-out 1.5s infinite;width:7px;height:7px;box-shadow:0 0 9px rgba(139,92,246,.5);"></div>
            {{-- Floating geometric shapes --}}
            <div class="absolute floating" style="top:38%;left:2%;width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,rgba(99,102,241,.25),rgba(139,92,246,.15));border:1.5px solid rgba(99,102,241,.2);animation-delay:-2s;"></div>
            <div class="absolute floating" style="bottom:18%;right:3%;width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,rgba(236,72,153,.22),rgba(245,101,101,.12));border:1.5px solid rgba(236,72,153,.2);animation-delay:-4s;"></div>
            <div class="absolute floating" style="top:55%;right:18%;width:36px;height:36px;border-radius:8px;transform:rotate(45deg);background:linear-gradient(135deg,rgba(16,185,129,.22),rgba(52,211,153,.12));border:1.5px solid rgba(16,185,129,.2);animation-delay:-1s;"></div>
            <div class="absolute floating" style="top:28%;left:18%;width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,rgba(245,158,11,.3),rgba(251,191,36,.15));border:1.5px solid rgba(245,158,11,.25);animation-delay:-3s;"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="text-center max-w-4xl mx-auto">
                    {{-- AI Badge --}}
                    <div class="hero-badge inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-surface-200 shadow-soft-sm mb-8" style="backdrop-filter:blur(8px)">
                        <span class="flex h-2 w-2 rounded-full bg-google-green-500 animate-pulse"></span>
                        <span class="text-sm font-medium text-ink-secondary">India's First Autonomous Career OS</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-google-blue-50 text-google-blue-600 font-medium">AI-Powered</span>
                    </div>

                    {{-- Headline --}}
                    <h1 class="hero-h1 text-4xl sm:text-5xl lg:text-7xl font-extrabold tracking-tight leading-tight mb-6">
                        <span style="color:#1a1a2e">Your Career.</span>
                        <br>
                        <span class="text-gradient-anim">On Autopilot.</span>
                    </h1>

                    {{-- Subheadline --}}
                    <p class="hero-sub text-lg sm:text-xl text-ink-secondary max-w-2xl mx-auto mb-10">
                        The AI that finds jobs, applies for you, and lands interviews � while you sleep. StudAI Hire manages your entire career journey, end to end.
                    </p>

                    {{-- Google-style Search Bar --}}
                    <form action="{{ route('jobs.search') }}" method="GET" class="hero-form max-w-2xl mx-auto mb-8">
                        <div class="relative search-glow rounded-full transition-all duration-200">
                            <div class="absolute left-5 top-1/2 -translate-y-1/2 text-ink-tertiary">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="search" 
                                name="q"
                                placeholder="Search jobs, skills, companies, or ask AI anything..."
                                class="w-full px-14 py-4 text-base bg-white rounded-full border border-surface-300 shadow-card hover:shadow-card-hover focus:shadow-elevation-3 focus:border-transparent focus:outline-none transition-all duration-200"
                            >
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 p-2 bg-google-blue-600 text-white rounded-full hover:bg-google-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-ink-tertiary mt-2">Try: "Find remote React jobs in Bangalore paying 20L+"</p>
                    </form>

                    {{-- CTA Buttons --}}
                    <div class="hero-cta flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="studai-btn studai-btn-primary studai-btn-lg pulse-btn">
                            <span>Start Free � No Credit Card</span>
                            <svg class="w-5 h-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="{{ route('features') }}" class="studai-btn studai-btn-ghost studai-btn-lg">
                            <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Watch Demo
                        </a>
                    </div>

                    {{-- Trust Badge --}}
                    <p class="mt-8 text-sm font-medium text-ink-tertiary">
                        Trusted by 50,000+ professionals across India
                    </p>
                    
                    {{-- Stats Row --}}
                    <div class="hero-stats flex flex-wrap items-center justify-center gap-8 mt-10">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-google-blue-600 stat-counter">50K+</div>
                            <div class="text-sm text-ink-tertiary">Careers Launched</div>
                        </div>
                        <div class="hidden sm:block w-px h-10 bg-surface-200"></div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-google-green-600 stat-counter">2.5M</div>
                            <div class="text-sm text-ink-tertiary">Jobs Indexed</div>
                        </div>
                        <div class="hidden sm:block w-px h-10 bg-surface-200"></div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600 stat-counter">94%</div>
                            <div class="text-sm text-ink-tertiary">Interview Success</div>
                        </div>
                        <div class="hidden sm:block w-px h-10 bg-surface-200"></div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-google-yellow-600 stat-counter">40%</div>
                            <div class="text-sm text-ink-tertiary">Avg. Salary Increase</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Floating elements for visual interest --}}
            <div class="absolute top-40 left-10 w-20 h-20 bg-indigo-100 rounded-2xl opacity-70 floating" style="animation-delay:-2s;border:1.5px solid rgba(99,102,241,.2)"></div>
            <div class="absolute bottom-20 right-10 w-16 h-16 rounded-full opacity-70 floating" style="animation-delay:-4s;background:linear-gradient(135deg,rgba(236,72,153,.2),rgba(168,85,247,.15));border:1.5px solid rgba(236,72,153,.2)"></div>
            <div class="absolute top-60 right-20 w-12 h-12 rounded-xl opacity-70 floating" style="animation-delay:-1s;background:linear-gradient(135deg,rgba(16,185,129,.2),rgba(52,211,153,.12));border:1.5px solid rgba(16,185,129,.2)"></div>
        </section>

        {{-- ============================================
            SECTION 2: FEATURE GRID
            Six core modules of the Career OS
        ============================================ --}}
        <section class="py-20 lg:py-28" style="background:linear-gradient(180deg,#eef0ff 0%,#f9f0ff 100%);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{-- Section Header --}}
                <div class="text-center max-w-2xl mx-auto mb-16 reveal">
                    <span class="text-xs font-semibold uppercase tracking-widest text-google-blue-600 mb-4 block">The Career OS</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-ink-primary mb-4">
                        One platform. Your entire career.
                    </h2>
                    <p class="text-lg text-ink-secondary">
                        From job discovery to salary negotiation � every step automated.
                    </p>
                </div>

                {{-- Feature Cards Grid --}}
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Autonomous Agent --}}
                    <div class="anim-card group reveal delay-100 rounded-2xl p-7 border border-indigo-200 relative cursor-default flex flex-col" style="background:linear-gradient(135deg,#f5f3ff 0%,#fdf4ff 100%);box-shadow:0 4px 24px rgba(99,102,241,.12),0 1px 4px rgba(99,102,241,.08);transition:transform .3s ease,box-shadow .3s ease;" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 40px rgba(99,102,241,.22),0 4px 12px rgba(99,102,241,.12)';" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(99,102,241,.12),0 1px 4px rgba(99,102,241,.08)';">
                        <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity" style="background:linear-gradient(135deg,rgba(99,102,241,.06),rgba(167,139,250,.04));"></div>
                        <div class="w-13 h-13 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform" style="width:3.25rem;height:3.25rem;background:linear-gradient(135deg,#ede9fe,#ddd6fe);box-shadow:0 4px 12px rgba(99,102,241,.25);">
                            <svg class="w-6 h-6" style="color:#7c3aed;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2" style="color:#1a1a2e;">Autonomous Agent</h3>
                        <p class="text-sm mb-5" style="color:#6b7280;">Set your preferences. Our AI applies to 100+ matching jobs daily � while you sleep.</p>
                        <a href="{{ route('agent.dashboard') }}" class="inline-flex items-center text-sm font-semibold mt-auto" style="color:#7c3aed;">
                            <span>Activate Agent</span>
                            <svg class="w-4 h-4 ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- Smart Job Search --}}
                    <div class="anim-card group reveal delay-200 rounded-2xl p-7 border border-blue-200 relative cursor-default flex flex-col" style="background:linear-gradient(135deg,#eff6ff 0%,#e0f2fe 100%);box-shadow:0 4px 24px rgba(59,130,246,.12),0 1px 4px rgba(59,130,246,.08);transition:transform .3s ease,box-shadow .3s ease;" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 40px rgba(59,130,246,.22),0 4px 12px rgba(59,130,246,.12)';" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(59,130,246,.12),0 1px 4px rgba(59,130,246,.08)';">
                        <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity" style="background:linear-gradient(135deg,rgba(59,130,246,.06),rgba(14,165,233,.04));"></div>
                        <div class="w-13 h-13 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform" style="width:3.25rem;height:3.25rem;background:linear-gradient(135deg,#dbeafe,#bae6fd);box-shadow:0 4px 12px rgba(59,130,246,.25);">
                            <svg class="w-6 h-6" style="color:#2563eb;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2" style="color:#1a1a2e;">Smart Job Search</h3>
                        <p class="text-sm mb-5" style="color:#6b7280;">AI-powered matching. No endless scrolling. Just perfect-fit opportunities.</p>
                        <a href="{{ route('jobs.search') }}" class="inline-flex items-center text-sm font-semibold mt-auto" style="color:#2563eb;">
                            <span>Find Jobs</span>
                            <svg class="w-4 h-4 ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- Resume Studio --}}
                    <div class="anim-card group reveal delay-300 rounded-2xl p-7 border border-green-200 relative cursor-default flex flex-col" style="background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);box-shadow:0 4px 24px rgba(16,185,129,.12),0 1px 4px rgba(16,185,129,.08);transition:transform .3s ease,box-shadow .3s ease;" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 40px rgba(16,185,129,.22),0 4px 12px rgba(16,185,129,.12)';" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(16,185,129,.12),0 1px 4px rgba(16,185,129,.08)';">
                        <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity" style="background:linear-gradient(135deg,rgba(16,185,129,.06),rgba(52,211,153,.04));"></div>
                        <div class="w-13 h-13 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform" style="width:3.25rem;height:3.25rem;background:linear-gradient(135deg,#bbf7d0,#a7f3d0);box-shadow:0 4px 12px rgba(16,185,129,.25);">
                            <svg class="w-6 h-6" style="color:#059669;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2" style="color:#1a1a2e;">Resume Studio</h3>
                        <p class="text-sm mb-5" style="color:#6b7280;">ATS-optimized resumes that actually get callbacks. Built in 5 minutes.</p>
                        <a href="{{ route('features') }}#resume-studio" class="inline-flex items-center text-sm font-semibold mt-auto" style="color:#059669;">
                            <span>Build Resume</span>
                            <svg class="w-4 h-4 ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- Interview AI --}}
                    <div class="anim-card group reveal delay-400 rounded-2xl p-7 border border-yellow-200 relative cursor-default flex flex-col" style="background:linear-gradient(135deg,#fffbeb 0%,#fef9c3 100%);box-shadow:0 4px 24px rgba(245,158,11,.12),0 1px 4px rgba(245,158,11,.08);transition:transform .3s ease,box-shadow .3s ease;" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 40px rgba(245,158,11,.22),0 4px 12px rgba(245,158,11,.12)';" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(245,158,11,.12),0 1px 4px rgba(245,158,11,.08)';">
                        <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity" style="background:linear-gradient(135deg,rgba(245,158,11,.06),rgba(251,191,36,.04));"></div>
                        <div class="w-13 h-13 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform" style="width:3.25rem;height:3.25rem;background:linear-gradient(135deg,#fde68a,#fcd34d);box-shadow:0 4px 12px rgba(245,158,11,.25);">
                            <svg class="w-6 h-6" style="color:#b45309;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2" style="color:#1a1a2e;">Interview AI</h3>
                        <p class="text-sm mb-5" style="color:#6b7280;">Practice with AI interviewers. Get real-time feedback. Walk in confident.</p>
                        <a href="{{ route('interview.index') }}" class="inline-flex items-center text-sm font-semibold mt-auto" style="color:#b45309;">
                            <span>Start Practice</span>
                            <svg class="w-4 h-4 ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- S.C.O.U.T. for Employers --}}
                    <div class="anim-card group reveal delay-600 rounded-2xl p-7 border border-indigo-200 relative cursor-default flex flex-col" id="employers" style="background:linear-gradient(135deg,#eef2ff 0%,#ede9fe 100%);box-shadow:0 4px 24px rgba(99,102,241,.15),0 1px 4px rgba(99,102,241,.08);transition:transform .3s ease,box-shadow .3s ease;" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 40px rgba(99,102,241,.28),0 4px 12px rgba(99,102,241,.15)';" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(99,102,241,.15),0 1px 4px rgba(99,102,241,.08)';">
                        <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity" style="background:linear-gradient(135deg,rgba(99,102,241,.07),rgba(139,92,246,.04));"></div>
                        <div class="absolute top-4 right-4 px-2.5 py-1 text-white text-xs font-bold rounded-full" style="background:linear-gradient(135deg,#6366f1,#7c3aed);box-shadow:0 2px 8px rgba(99,102,241,.4);">For Employers</div>
                        <div class="w-13 h-13 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform" style="width:3.25rem;height:3.25rem;background:linear-gradient(135deg,#c7d2fe,#ddd6fe);box-shadow:0 4px 12px rgba(99,102,241,.25);">
                            <svg class="w-6 h-6" style="color:#6366f1;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2" style="color:#1a1a2e;">S.C.O.U.T. AI</h3>
                        <p class="text-sm mb-5" style="color:#6b7280;">Bias-free AI hiring. Find the best talent faster. Reduce time-to-hire by 60%.</p>
                        <a href="{{ route('employer.dashboard') }}" class="inline-flex items-center text-sm font-semibold mt-auto" style="color:#6366f1;">
                            <span>For Employers</span>
                            <svg class="w-4 h-4 ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================
            SECTION 3: WHY CHOOSE US
            Value propositions with benefit-focused copy
        ============================================ --}}
        <section class="py-20 lg:py-28" style="background:linear-gradient(160deg,#fdfeff 0%,#f4f6ff 50%,#fff8f6 100%);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="reveal-left">
                        <span class="text-xs font-semibold uppercase tracking-widest text-google-blue-600 mb-4 block">Why StudAI Hire</span>
                        <h2 class="text-3xl sm:text-4xl font-bold text-ink-primary mb-6">
                            Why 50,000+ Professionals Choose StudAI Hire
                        </h2>
                        
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-lg bg-google-green-50 text-google-green-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-ink-primary mb-1">Zero Manual Applications</h3>
                                    <p class="text-sm text-ink-secondary">Our AI agent handles everything. From finding matches to submitting applications � fully autonomous.</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-lg bg-google-blue-50 text-google-blue-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-ink-primary mb-1">Interview-Ready in Hours</h3>
                                    <p class="text-sm text-ink-secondary">AI mock interviews tailored to your target role. Real questions. Real-time coaching.</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-ink-primary mb-1">Salary Negotiation AI</h3>
                                    <p class="text-sm text-ink-secondary">Know your worth. Our AI analyzes market data and coaches you through negotiations.</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-lg bg-google-yellow-50 text-google-yellow-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-ink-primary mb-1">One Profile. Infinite Reach.</h3>
                                    <p class="text-sm text-ink-secondary">Apply across platforms, companies, and countries � with a single unified career profile.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Right Column - Dashboard Preview --}}
                    <div class="relative reveal-right delay-200 floating">
                        <div class="rounded-2xl overflow-hidden shadow-soft-xl border border-surface-200">
                            {{-- Browser Chrome --}}
                            <div class="bg-surface-100 border-b border-surface-200 px-4 py-3 flex items-center gap-2">
                                <div class="flex gap-2">
                                    <div class="w-3 h-3 rounded-full bg-google-red-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-google-yellow-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-google-green-400"></div>
                                </div>
                                <div class="flex-1 flex justify-center">
                                    <div class="px-4 py-1.5 bg-white rounded-lg border border-surface-300 text-sm text-ink-secondary w-full max-w-md text-center">
                                        app.studaipath.com/dashboard
                                    </div>
                                </div>
                            </div>

                            {{-- Dashboard Content --}}
                            <div class="bg-canvas-subtle p-6">
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="bg-white rounded-xl p-4 border border-surface-200">
                                        <div class="text-2xl font-bold text-google-blue-600">89%</div>
                                        <div class="text-sm text-ink-secondary">AI Match Score</div>
                                    </div>
                                    <div class="bg-white rounded-xl p-4 border border-surface-200">
                                        <div class="text-2xl font-bold text-google-green-600">24</div>
                                        <div class="text-sm text-ink-secondary">Applications Today</div>
                                    </div>
                                </div>
                                
                                {{-- AI Activity --}}
                                <div class="bg-white rounded-xl p-4 border border-surface-200">
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="w-8 h-8 bg-gradient-to-br from-google-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        <span class="font-semibold text-ink-primary">Autonomous Agent</span>
                                        <span class="ml-auto px-2 py-0.5 bg-google-green-50 text-google-green-600 text-xs rounded-full">Active</span>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex items-start gap-3 text-sm">
                                            <div class="w-2 h-2 mt-1.5 rounded-full bg-google-green-500"></div>
                                            <div>
                                                <div class="text-ink-primary">Applied to 5 jobs at Google</div>
                                                <div class="text-xs text-ink-tertiary">Just now</div>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3 text-sm">
                                            <div class="w-2 h-2 mt-1.5 rounded-full bg-google-blue-500"></div>
                                            <div>
                                                <div class="text-ink-primary">Resume optimized for Microsoft roles</div>
                                                <div class="text-xs text-ink-tertiary">2 min ago</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================
            SECTION 4: TESTIMONIALS
            Social proof with real success stories
        ============================================ --}}
        <section class="py-20 lg:py-28 bg-canvas-subtle" style="background:linear-gradient(180deg,#effffa 0%,#eef0ff 100%) !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-semibold uppercase tracking-widest text-google-blue-600 mb-4 block">Success Stories</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-ink-primary mb-4">
                        Careers Transformed
                    </h2>
                    <p class="text-lg text-ink-secondary">
                        Real people. Real results. Real careers on autopilot.
                    </p>
                </div>

                {{-- Testimonial Cards --}}
                <div class="grid md:grid-cols-3 gap-6 mb-16">
                    <div class="anim-card reveal delay-100 rounded-xl p-6 border border-indigo-100" style="background:linear-gradient(135deg,#f5f3ff,#eff6ff);">
                        <div class="flex items-center gap-1 mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 text-google-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-ink-secondary mb-4">"StudAI Hire got me 3 offers in 2 weeks. The autonomous agent is like having a full-time job search assistant. I didn't fill a single form myself."</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-google-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">PS</div>
                            <div>
                                <div class="font-semibold text-ink-primary">Priya Sharma</div>
                                <div class="text-sm text-ink-tertiary">Senior Software Engineer at Google</div>
                            </div>
                        </div>
                    </div>

                    <div class="anim-card reveal delay-300 rounded-xl p-6 border border-green-100" style="background:linear-gradient(135deg,#f0fdf4,#ecfdf5);">
                        <div class="flex items-center gap-1 mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 text-google-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-ink-secondary mb-4">"The interview AI helped me crack my Amazon SDE-2 interview. The behavioral prep was spot-on. It's like they knew exactly what Amazon would ask."</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-google-green-500 to-cyan-500 flex items-center justify-center text-white font-semibold text-sm">RM</div>
                            <div>
                                <div class="font-semibold text-ink-primary">Rahul Menon</div>
                                <div class="text-sm text-ink-tertiary">SDE-2 at Amazon</div>
                            </div>
                        </div>
                    </div>

                    <div class="anim-card reveal delay-500 rounded-xl p-6 border border-yellow-100" style="background:linear-gradient(135deg,#fffbeb,#fdf2f8);">
                        <div class="flex items-center gap-1 mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 text-google-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-ink-secondary mb-4">"As a recruiter, S.C.O.U.T. changed how we hire. Time-to-hire dropped 60%. Quality went up. And our diversity metrics finally moved."</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-google-yellow-500 to-orange-500 flex items-center justify-center text-white font-semibold text-sm">AV</div>
                            <div>
                                <div class="font-semibold text-ink-primary">Anjali Verma</div>
                                <div class="text-sm text-ink-tertiary">Head of Talent at Razorpay</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Company Logos --}}
                <div class="text-center">
                    <p class="text-sm text-ink-tertiary mb-6">Our users work at top companies</p>
                    <div class="flex flex-wrap items-center justify-center gap-8 grayscale opacity-60">
                        <img src="{{ asset('images/logos/google.svg') }}" alt="Google" class="h-8">
                        <img src="{{ asset('images/logos/amazon.svg') }}" alt="Amazon" class="h-8">
                        <img src="{{ asset('images/logos/microsoft.svg') }}" alt="Microsoft" class="h-8">
                        <img src="{{ asset('images/logos/razorpay.svg') }}" alt="Razorpay" class="h-8">
                        <img src="{{ asset('images/logos/flipkart.svg') }}" alt="Flipkart" class="h-8">
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================
            SECTION 5: FAQ
            Common questions answered
        ============================================ --}}
        <section class="py-20 lg:py-28" style="background:linear-gradient(160deg,#fff8f6 0%,#fdf4ff 50%,#f0f4ff 100%);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16 reveal">
                    <span class="text-xs font-semibold uppercase tracking-widest text-google-blue-600 mb-4 block">FAQ</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-ink-primary mb-4">
                        Questions? Answered.
                    </h2>
                    <p class="text-lg text-ink-secondary">Everything you need to know about putting your career on autopilot.</p>
                </div>

                <div class="max-w-3xl mx-auto space-y-4 reveal delay-200">
                    <details class="rounded-xl border border-indigo-100 group" style="background:linear-gradient(135deg,#f5f3ff,#eff6ff);" open>
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">What is an Autonomous Career OS?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            StudAI Hire is a unified system that manages your entire career lifecycle � from job discovery to salary negotiation � using AI agents that work 24/7 on your behalf. Think of it as having a dedicated career team that never sleeps.
                        </div>
                    </details>

                    <details class="rounded-xl border border-blue-100 group" style="background:linear-gradient(135deg,#eff6ff,#f0f9ff);">
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">Can the AI really apply to jobs for me?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            Yes. Once you set your preferences (role, salary, location, company type), our AI agent automatically finds and applies to matching jobs with tailored cover letters. You can review applications anytime, but you don't have to lift a finger.
                        </div>
                    </details>

                    <details class="rounded-xl border border-pink-100 group" style="background:linear-gradient(135deg,#fdf2f8,#fff1f2);">
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">How is StudAI Hire different from job boards?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            Job boards show listings. StudAI Hire actively works for you � finding jobs, applying, prepping interviews, and negotiating offers. It's the difference between a library and a personal assistant.
                        </div>
                    </details>

                    <details class="rounded-xl border border-green-100 group" style="background:linear-gradient(135deg,#f0fdf4,#ecfdf5);">
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">What is S.C.O.U.T. AI for employers?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            S.C.O.U.T. (Smart Candidate Optimization & Unified Tracking) is our AI-powered ATS for employers. It removes hiring bias, automates screening, and identifies top candidates faster � resulting in 60% faster time-to-hire and 73% more diverse teams.
                        </div>
                    </details>

                    <details class="rounded-xl border border-yellow-100 group" style="background:linear-gradient(135deg,#fffbeb,#fefce8);">
                        <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                            <h3 class="font-semibold text-ink-primary">Is there a free plan?</h3>
                            <svg class="w-5 h-5 text-ink-tertiary group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="px-6 pb-6 text-ink-secondary">
                            Yes. Our free tier includes unlimited job search, basic resume builder, and 5 AI interview practice sessions per month. No credit card required to start.
                        </div>
                    </details>
                </div>
            </div>
        </section>

        {{-- ============================================
            SECTION 6: CTA
            Final call to action
        ============================================ --}}
        <section class="py-20 lg:py-28 relative overflow-hidden" style="background:linear-gradient(135deg,#eef1ff 0%,#fdf4ff 40%,#eefff7 100%);">
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute" style="width:500px;height:500px;top:-80px;right:-60px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.2),transparent 70%);filter:blur(60px);animation:orb2 12s ease-in-out infinite;"></div>
                <div class="absolute" style="width:400px;height:400px;bottom:-60px;left:-50px;border-radius:50%;background:radial-gradient(circle,rgba(99,102,241,.18),transparent 70%);filter:blur(55px);animation:orb1 14s ease-in-out infinite;"></div>
                <div class="absolute" style="width:300px;height:300px;top:40%;left:45%;border-radius:50%;background:radial-gradient(circle,rgba(236,72,153,.12),transparent 70%);filter:blur(50px);animation:orb3 16s ease-in-out infinite;"></div>
                <div class="absolute inset-0" style="background-image:radial-gradient(circle,rgba(99,102,241,.12) 1px,transparent 1px);background-size:40px 40px;"></div>
            </div>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-scale relative">
                <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold mb-6" style="background:rgba(99,102,241,.1);color:#6366f1;border:1px solid rgba(99,102,241,.2);">No credit card required</div>
                <h2 class="text-3xl sm:text-4xl font-bold mb-6" style="color:#1a1a2e;">
                    Ready to put your career on <span style="background:linear-gradient(135deg,#6366f1,#8b5cf6,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">autopilot?</span>
                </h2>
                <p class="text-lg mb-8 max-w-2xl mx-auto" style="color:#4b5563;">
                    Join 50,000+ professionals who let AI manage their job search. Start free � no credit card required.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3.5 rounded-xl text-sm font-semibold text-white" style="background:linear-gradient(135deg,#6366f1,#7c3aed);box-shadow:0 4px 18px rgba(99,102,241,.4);">
                        Start Free � No Credit Card
                        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="{{ route('pricing') }}" class="inline-flex items-center px-8 py-3.5 rounded-xl text-sm font-semibold" style="background:rgba(255,255,255,.85);color:#6366f1;border:1.5px solid rgba(99,102,241,.25);">
                        View Pricing
                    </a>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="py-16 relative" style="background:linear-gradient(160deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);">
        <div class="absolute inset-0 pointer-events-none" style="background-image:radial-gradient(circle,rgba(99,102,241,.12) 1px,transparent 1px);background-size:36px 36px;"></div>
        <div class="absolute top-0 left-0 right-0 h-px" style="background:linear-gradient(90deg,transparent,rgba(99,102,241,.5),rgba(139,92,246,.5),rgba(236,72,153,.3),transparent);"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 mb-12">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <img src="/assets/logo/icon.png?v=3" alt="StudAI Hire" style="width:28px;height:28px;object-fit:contain">
                        <span class="text-lg font-semibold">StudAI Hire</span>
                    </div>
                    <p class="text-sm text-white/60 mb-4">Your Career. Autonomous.</p>
                    <p class="text-xs text-white/40">Building the future of career management. One AI-powered decision at a time.</p>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm text-white/60">
                        <li><a href="{{ route('jobs.search') }}" class="hover:text-white transition-colors">Job Search</a></li>
                        <li><a href="{{ route('agent.dashboard') }}" class="hover:text-white transition-colors">Autonomous Agent</a></li>
                        <li><a href="{{ route('features') }}#resume-studio" class="hover:text-white transition-colors">Resume Builder</a></li>
                        <li><a href="{{ route('interview.index') }}" class="hover:text-white transition-colors">Interview AI</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">For Employers</h4>
                    <ul class="space-y-2 text-sm text-white/60">
                        <li><a href="{{ route('employer.dashboard') }}" class="hover:text-white transition-colors">S.C.O.U.T. AI</a></li>
                        <li><a href="{{ route('employer.jobs.create') }}" class="hover:text-white transition-colors">Post Jobs</a></li>
                        <li><a href="{{ route('employer.talent-pool.index') }}" class="hover:text-white transition-colors">Talent Search</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Enterprise</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-sm text-white/60">
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About StudAI</a></li>
                        <li><a href="{{ route('about') }}#careers" class="hover:text-white transition-colors">Careers</a></li>
                        <li><a href="{{ route('blog') }}" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="{{ route('about') }}#press" class="hover:text-white transition-colors">Press</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm text-white/60">
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="{{ route('cookie-policy') }}" class="hover:text-white transition-colors">Cookie Policy</a></li>
                        <li><a href="{{ route('security') }}" class="hover:text-white transition-colors">Security</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-4">
                    <p class="text-sm text-white/60">� {{ date('Y') }} StudAI Technologies Pvt. Ltd. All rights reserved.</p>
                    <span class="text-sm text-white/40">Made with ?? in India</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="https://twitter.com/studaipath" target="_blank" class="text-white/60 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="https://linkedin.com/company/studai" target="_blank" class="text-white/60 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="https://instagram.com/studaipath" target="_blank" class="text-white/60 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/></svg>
                    </a>
                    <a href="https://youtube.com/@studaipath" target="_blank" class="text-white/60 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
    // -- Scroll-reveal IntersectionObserver ------------------
    (function(){
        var selectors = '.reveal, .reveal-left, .reveal-right, .reveal-scale';
        var els = document.querySelectorAll(selectors);
        if (!els.length) return;
        var obs = new IntersectionObserver(function(entries){
            entries.forEach(function(e){
                if(e.isIntersecting){
                    e.target.classList.add('visible');
                    obs.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 });
        els.forEach(function(el){ obs.observe(el); });
    })();

    // -- Animated number counters ----------------------------
    (function(){
        function animateNum(el){
            var target = parseFloat(el.dataset.target);
            var suffix = el.dataset.suffix || '';
            var duration = 1800;
            var start = performance.now();
            function step(now){
                var p = Math.min((now - start) / duration, 1);
                var ease = 1 - Math.pow(1 - p, 4);
                var val = target * ease;
                el.textContent = (val >= 1000 ? (val/1000).toFixed(val >= 10000 ? 0 : 1) + 'K' : Math.round(val)) + suffix;
                if(p < 1) requestAnimationFrame(step);
                else el.textContent = (target >= 1000 ? (target/1000).toFixed(0) + 'K+' : target) + suffix;
            }
            requestAnimationFrame(step);
        }
        var numEls = document.querySelectorAll('[data-target]');
        var numObs = new IntersectionObserver(function(entries){
            entries.forEach(function(e){
                if(e.isIntersecting){ animateNum(e.target); numObs.unobserve(e.target); }
            });
        }, {threshold: 0.5});
        numEls.forEach(function(el){ numObs.observe(el); });
    })();

    // -- Navbar glass effect on scroll -----------------------
    (function(){
        var nav = document.querySelector('nav');
        if(!nav) return;
        window.addEventListener('scroll', function(){
            if(window.scrollY > 20){
                nav.style.background = 'rgba(255,255,255,.96)';
                nav.style.boxShadow = '0 1px 24px rgba(0,0,0,.08)';
            } else {
                nav.style.background = 'rgba(255,255,255,.8)';
                nav.style.boxShadow = 'none';
            }
        }, {passive:true});
    })();

    // -- Mouse parallax on hero orbs -------------------------
    (function(){
        var orbs = document.querySelectorAll('.orb');
        document.addEventListener('mousemove', function(e){
            var cx = window.innerWidth / 2, cy = window.innerHeight / 2;
            var dx = (e.clientX - cx) / cx, dy = (e.clientY - cy) / cy;
            orbs.forEach(function(orb, i){
                var strength = (i + 1) * 12;
                orb.style.transform = 'translate(' + (dx * strength) + 'px,' + (dy * strength) + 'px)';
            });
        }, {passive: true});
    })();
    </script>
</body>
</html>


