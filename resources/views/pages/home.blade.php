@extends('layouts.marketing')

@section('title', 'StudAI Hire - India\'s First Autonomous Career OS | Your Career, On Autopilot')

@section('meta')
<meta name="description" content="StudAI Hire - The AI that finds jobs, applies for you, and lands interviews while you sleep. India's first autonomous career operating system.">
<meta property="og:title" content="StudAI Hire - Your Career, On Autopilot">
<meta property="og:description" content="AI that applies to 100+ jobs daily, preps you for interviews, and negotiates your salary. Fully autonomous. Fully free to start.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url('/') }}">
<meta name="twitter:card" content="summary_large_image">
<link rel="canonical" href="{{ url('/') }}">
@endsection

@section('content')

<style>
    /* -- HOME PAGE DESIGN TOKENS ------------------- */
    :root {
        --brand:       #6366f1;
        --brand-dark:  #4f46e5;
        --brand-light: #f0f0ff;
        --bg:          #f7f7fc;
        --surface:     #ffffff;
        --border:      #ebebf5;
        --text:        #1a1a2e;
        --text-2:      #4b5563;
        --text-3:      #9ca3af;
    }

    /* -- KEYFRAMES --------------------------------- */
    @keyframes fadeUp      { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeIn      { from{opacity:0} to{opacity:1} }
    @keyframes ticker      { from{transform:translateX(0)} to{transform:translateX(-50%)} }
    @keyframes floatY      { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
    @keyframes pingDot     { 0%{transform:scale(1);opacity:.8} 80%,100%{transform:scale(2.2);opacity:0} }
    @keyframes shimmer     { to{background-position:-200% center} }
    @keyframes gradShift   { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
    @keyframes blobMove    { 0%,100%{transform:translate(0,0)scale(1)} 33%{transform:translate(30px,-40px)scale(1.05)} 66%{transform:translate(-20px,20px)scale(.97)} }

    .animate-fade-up   { animation:fadeUp .7s ease both; }
    .delay-1 { animation-delay:.1s }
    .delay-2 { animation-delay:.2s }
    .delay-3 { animation-delay:.35s }
    .delay-4 { animation-delay:.5s }
    .delay-5 { animation-delay:.65s }

    .ticker-track { animation:ticker 32s linear infinite; }
    .ticker-track:hover { animation-play-state:paused; }

    .float-anim { animation:floatY 4s ease-in-out infinite; }
    .ping-dot   { animation:pingDot 2s cubic-bezier(0,0,.2,1) infinite; }
    .blob       { animation:blobMove 10s ease-in-out infinite; }
    .blob-2     { animation-delay:3s; }
    .blob-3     { animation-delay:6s; }

    /* -- GRADIENT TEXT ------------------------------ */
    .grad-text {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
        background-size: 200%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: gradShift 4s ease infinite;
    }

    /* -- HERO CARD ---------------------------------- */
    .hero-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,.04), 0 24px 64px rgba(99,102,241,.08);
    }

    /* -- FEATURE CARDS ------------------------------ */
    .feat-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 20px;
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }
    .feat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 48px rgba(99,102,241,.14);
        border-color: rgba(99,102,241,.3);
    }

    /* -- TESTIMONIAL CARDS --------------------------- */
    .testi-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 20px;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .testi-card:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(99,102,241,.12); }

    /* -- FAQ ---------------------------------------- */
    details summary { list-style:none; cursor:pointer; }
    details summary::-webkit-details-marker { display:none; }
    details[open] .faq-chevron { transform:rotate(180deg); }
    .faq-chevron { transition:transform .25s ease; }

    /* -- STAT NUMBER ------------------------------- */
    .stat-num {
        font-size: 2.5rem;
        font-weight: 800;
        letter-spacing: -0.04em;
        background: linear-gradient(135deg, #6366f1, #a855f7);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* -- SUBTLE GRID BG ---------------------------- */
    .dot-grid {
        background-image: radial-gradient(rgba(99,102,241,.12) 1px, transparent 1px);
        background-size: 28px 28px;
    }

    /* -- CTA SECTION ------------------------------- */
    .cta-section {
        background: linear-gradient(135deg, #6366f1 0%, #7c3aed 50%, #a855f7 100%);
        background-size: 200%;
        animation: gradShift 6s ease infinite;
    }

    /* -- BRAND GLOW BTN ----------------------------- */
    .btn-brand {
        background: linear-gradient(135deg, #6366f1, #7c3aed);
        color: white;
        font-weight: 700;
        border-radius: 12px;
        padding: 14px 28px;
        transition: transform .2s ease, box-shadow .2s ease;
        box-shadow: 0 4px 20px rgba(99,102,241,.35);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
    }
    .btn-brand:hover { transform:translateY(-3px); box-shadow:0 8px 32px rgba(99,102,241,.5); }
    .btn-brand:active { transform:translateY(0) scale(.98); }

    .btn-ghost {
        background: white;
        color: var(--text);
        font-weight: 600;
        border-radius: 12px;
        padding: 14px 28px;
        border: 1.5px solid var(--border);
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
    }
    .btn-ghost:hover { transform:translateY(-2px); border-color:rgba(99,102,241,.4); box-shadow:0 4px 16px rgba(0,0,0,.06); }
</style>

{{-- =====================================================
     HERO
===================================================== --}}
<section class="relative overflow-hidden dot-grid" style="background:#f7f7fc; min-height:100vh; display:flex; flex-direction:column; justify-content:center;">

    {{-- Soft gradient blobs --}}
    <div class="blob absolute -top-48 -left-48 w-[700px] h-[700px] rounded-full pointer-events-none" style="background:radial-gradient(circle,rgba(99,102,241,.12) 0%,transparent 70%); filter:blur(40px);"></div>
    <div class="blob blob-2 absolute top-1/3 -right-48 w-[600px] h-[600px] rounded-full pointer-events-none" style="background:radial-gradient(circle,rgba(168,85,247,.10) 0%,transparent 70%); filter:blur(40px);"></div>
    <div class="blob blob-3 absolute -bottom-32 left-1/4 w-[500px] h-[500px] rounded-full pointer-events-none" style="background:radial-gradient(circle,rgba(236,72,153,.08) 0%,transparent 70%); filter:blur(40px);"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Left --}}
            <div class="space-y-8">
                {{-- Badge --}}
                <div class="animate-fade-up delay-1 inline-flex items-center gap-2.5 rounded-full px-4 py-2" style="background:#f0f0ff; border:1.5px solid rgba(99,102,241,.25)">
                    <span class="relative flex h-2 w-2">
                        <span class="ping-dot absolute inline-flex h-full w-full rounded-full bg-emerald-400"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    </span>
                    <span class="text-sm font-semibold" style="color:#6366f1">India's First Autonomous Career OS</span>
                </div>

                {{-- Headline --}}
                <div class="animate-fade-up delay-2 space-y-2">
                    <p class="text-xs font-bold tracking-[0.2em] uppercase" style="color:#9ca3af">AI-Powered</p>
                    <h1 class="font-extrabold leading-[1.04] tracking-tight" style="font-size:clamp(3rem,7vw,5.5rem); color:#1a1a2e">
                        Your Career.<br>
                        <span class="grad-text">On Autopilot.</span>
                    </h1>
                </div>

                {{-- Sub --}}
                <p class="animate-fade-up delay-3 text-xl leading-relaxed" style="color:#4b5563; max-width:480px">
                    The AI that finds jobs, applies for you, and lands interviews � <strong style="color:#1a1a2e">while you sleep.</strong>
                    StudAI Hire manages your entire career journey, end to end.
                </p>

                {{-- Search --}}
                <div class="animate-fade-up delay-4" style="max-width:520px">
                    <div class="flex items-center gap-0 rounded-2xl overflow-hidden" style="background:white; border:1.5px solid var(--border); box-shadow:0 4px 20px rgba(99,102,241,.08)">
                        <div class="pl-5">
                            <svg class="w-4.5 h-4.5" style="color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" placeholder="Search jobs, skills, companies, or ask AI anything..."
                            class="flex-1 px-4 py-4 text-sm bg-transparent outline-none"
                            style="color:#1a1a2e">
                        <a href="{{ route('jobs.search') }}"
                            class="m-1.5 px-5 py-3 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                            style="background:linear-gradient(135deg,#6366f1,#7c3aed)">Search</a>
                    </div>
                    <p class="text-xs mt-2.5 ml-1" style="color:#9ca3af">Try: <span style="color:#6366f1">"Find remote React jobs in Bangalore paying 20L+"</span></p>
                </div>

                {{-- CTAs --}}
                <div class="animate-fade-up delay-5 flex flex-wrap items-center gap-4">
                    <a href="{{ route('register') }}" class="btn-brand">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Start Free � No Credit Card
                    </a>
                    <a href="#demo" class="btn-ghost">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Watch Demo
                    </a>
                </div>

                {{-- Social proof --}}
                <div class="animate-fade-up flex items-center gap-3">
                    <div class="flex -space-x-2">
                        @foreach(['#6366f1','#a855f7','#f97316','#22c55e','#3b82f6'] as $c)
                        <div class="w-8 h-8 rounded-full border-2 border-white flex items-center justify-center text-[10px] font-bold text-white" style="background:{{ $c }}">{{ chr(65 + $loop->index) }}</div>
                        @endforeach
                    </div>
                    <p class="text-sm font-medium" style="color:#4b5563">Trusted by <strong style="color:#1a1a2e">50,000+</strong> professionals across India</p>
                </div>
            </div>

            {{-- Right: Dashboard card --}}
            <div class="hidden lg:block float-anim">
                <div class="hero-card p-6 max-w-md ml-auto">
                    {{-- Card header --}}
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-widest" style="color:#9ca3af">app.studaipath.com/dashboard</div>
                            <div class="text-base font-bold mt-0.5" style="color:#1a1a2e">Your AI Dashboard</div>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                            <span class="ping-dot w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                            Live
                        </div>
                    </div>

                    {{-- Stats row --}}
                    <div class="grid grid-cols-3 gap-3 mb-5">
                        @foreach([['89%','AI Match Score','#6366f1'],['24','Applications Today','#a855f7'],['12','Interviews','#22c55e']] as $s)
                        <div class="rounded-2xl p-3 text-center" style="background:#f7f7fc; border:1px solid var(--border)">
                            <div class="text-xl font-extrabold" style="color:{{ $s[2] }}">{{ $s[0] }}</div>
                            <div class="text-[10px] font-medium mt-0.5" style="color:#9ca3af">{{ $s[1] }}</div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Agent status --}}
                    <div class="rounded-2xl p-4 mb-4" style="background:linear-gradient(135deg,#6366f1,#7c3aed)">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-white">Autonomous Agent</div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-xs text-indigo-200">Active</span>
                                </div>
                            </div>
                        </div>
                        {{-- Activity feed --}}
                        @foreach([['Applied to 5 jobs at Google','Just now','#a5b4fc'],['Resume optimized for Microsoft roles','2 min ago','#c4b5fd']] as $act)
                        <div class="flex items-start gap-2 mb-2 last:mb-0">
                            <div class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" style="background:{{ $act[2] }}"></div>
                            <div>
                                <div class="text-xs text-white/90">{{ $act[0] }}</div>
                                <div class="text-[10px] mt-0.5" style="color:{{ $act[2] }}">{{ $act[1] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Job match row --}}
                    <div class="space-y-2">
                        @foreach([['Google','Senior SWE � Remote','96%','#6366f1'],['Stripe','Lead ML Engineer � SF','91%','#a855f7']] as $j)
                        <div class="flex items-center gap-3 rounded-xl p-3" style="background:#f7f7fc; border:1px solid var(--border)">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-white text-sm" style="background:{{ $j[3] }}">{{ substr($j[0],0,1) }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-semibold truncate" style="color:#1a1a2e">{{ $j[0] }}</div>
                                <div class="text-[10px] truncate" style="color:#9ca3af">{{ $j[1] }}</div>
                            </div>
                            <span class="text-xs font-bold" style="color:{{ $j[3] }}">{{ $j[2] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- =====================================================
     STATS TICKER
===================================================== --}}
<div class="relative overflow-hidden py-5" style="background:white; border-top:1px solid var(--border); border-bottom:1px solid var(--border)">
    <div class="ticker-track flex items-center gap-16 whitespace-nowrap" style="width:max-content">
        @php $stats = ['50K+ Careers Launched','2.5M Jobs Indexed','94% Interview Success','40% Avg. Salary Increase','12M+ AI Applications Sent','?8L Avg. Salary Hike','60% Faster Time-to-Hire','4.9? User Rating']; @endphp
        @foreach(array_merge($stats, $stats) as $s)
        <div class="flex items-center gap-2.5">
            <div class="w-1.5 h-1.5 rounded-full" style="background:#6366f1"></div>
            <span class="text-sm font-semibold" style="color:#4b5563">{{ $s }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- =====================================================
     STATS GRID
===================================================== --}}
<section class="py-20" style="background:#f7f7fc">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([['50K+','Careers Launched','#6366f1'],['2.5M','Jobs Indexed','#a855f7'],['94%','Interview Success','#22c55e'],['40%','Avg. Salary Increase','#f97316']] as $s)
            <div class="text-center rounded-2xl p-8" style="background:white; border:1px solid var(--border); box-shadow:0 1px 4px rgba(0,0,0,.04)">
                <div class="stat-num">{{ $s[0] }}</div>
                <div class="text-sm font-medium mt-1" style="color:#6b7280">{{ $s[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =====================================================
     FEATURES
===================================================== --}}
<section class="py-24" style="background:white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 mb-4 text-xs font-semibold uppercase tracking-widest" style="background:#f0f0ff; color:#6366f1; border:1px solid rgba(99,102,241,.2)">The Career OS</div>
            <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight" style="color:#1a1a2e">One platform. Your entire career.</h2>
            <p class="mt-4 text-xl max-w-2xl mx-auto" style="color:#6b7280">From job discovery to salary negotiation � every step automated.</p>
        </div>

        {{-- Feature grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $features = [
                [
                    'name'    => 'Autonomous Agent',
                    'desc'    => 'Set your preferences. Our AI applies to 100+ matching jobs daily � while you sleep.',
                    'cta'     => 'Activate Agent',
                    'route'   => 'register',
                    'grad'    => 'linear-gradient(135deg,#6366f1,#7c3aed)',
                    'light'   => '#f0f0ff',
                    'icon'    => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    'badge'   => 'Most Popular',
                ],
                [
                    'name'    => 'Smart Job Search',
                    'desc'    => 'AI-powered matching. No endless scrolling. Just perfect-fit opportunities.',
                    'cta'     => 'Find Jobs',
                    'route'   => 'jobs.search',
                    'grad'    => 'linear-gradient(135deg,#22c55e,#16a34a)',
                    'light'   => '#f0fdf4',
                    'icon'    => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                    'badge'   => null,
                ],
                [
                    'name'    => 'Resume Studio',
                    'desc'    => 'ATS-optimized resumes that actually get callbacks. Built in 5 minutes.',
                    'cta'     => 'Build Resume',
                    'route'   => 'resume.index',
                    'grad'    => 'linear-gradient(135deg,#8b5cf6,#6366f1)',
                    'light'   => '#faf5ff',
                    'icon'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'badge'   => null,
                ],
                [
                    'name'    => 'Interview AI',
                    'desc'    => 'Practice with AI interviewers. Get real-time feedback. Walk in confident.',
                    'cta'     => 'Start Practice',
                    'route'   => 'interview.index',
                    'grad'    => 'linear-gradient(135deg,#f97316,#ef4444)',
                    'light'   => '#fff7ed',
                    'icon'    => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                    'badge'   => null,
                ],
                [
                    'name'    => 'For Employers: S.C.O.U.T. AI',
                    'desc'    => 'Bias-free AI hiring. Find the best talent faster. Reduce time-to-hire by 60%.',
                    'cta'     => 'For Employers',
                    'route'   => 'employer.dashboard',
                    'grad'    => 'linear-gradient(135deg,#14b8a6,#0d9488)',
                    'light'   => '#f0fdfa',
                    'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                    'badge'   => 'Employer',
                ],
            ];
            @endphp

            @foreach($features as $f)
            <div class="feat-card p-7 relative">
                @if($f['badge'])
                <span class="absolute top-5 right-5 text-[10px] font-bold px-2.5 py-1 rounded-full text-white" style="background:linear-gradient(135deg,#6366f1,#a855f7)">{{ $f['badge'] }}</span>
                @endif
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style="background:{{ $f['grad'] }}">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color:#1a1a2e">{{ $f['name'] }}</h3>
                <p class="text-sm leading-relaxed mb-6" style="color:#6b7280">{{ $f['desc'] }}</p>
                <a href="{{ route($f['route']) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold transition-colors hover:gap-2.5"
                    style="color:#6366f1">
                    {{ $f['cta'] }}
                    <svg class="w-4 h-4 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =====================================================
     WHY STUDAI
===================================================== --}}
<section class="py-24 dot-grid" style="background:#f7f7fc">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Left: visual --}}
            <div class="relative">
                <div class="hero-card p-8">
                    <div class="text-xs font-bold uppercase tracking-widest mb-6" style="color:#9ca3af">Why StudAI Hire</div>
                    <div class="space-y-5">
                        @foreach([
                            ['Zero Manual Applications','Our AI agent handles everything. From finding matches to submitting applications � fully autonomous.','#6366f1'],
                            ['Interview-Ready in Hours','AI mock interviews tailored to your target role. Real questions. Real-time coaching.','#a855f7'],
                            ['Salary Negotiation AI','Know your worth. Our AI analyzes market data and coaches you through negotiations.','#22c55e'],
                            ['One Profile. Infinite Reach.','Apply across platforms, companies, and countries � with a single unified career profile.','#f97316'],
                        ] as $w)
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center mt-0.5" style="background:{{ $w[2] }}20">
                                <div class="w-3 h-3 rounded-full" style="background:{{ $w[2] }}"></div>
                            </div>
                            <div>
                                <div class="font-bold text-sm mb-1" style="color:#1a1a2e">{{ $w[0] }}</div>
                                <div class="text-sm leading-relaxed" style="color:#6b7280">{{ $w[1] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right: text --}}
            <div>
                <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 mb-6 text-xs font-semibold uppercase tracking-widest" style="background:#f0f0ff; color:#6366f1; border:1px solid rgba(99,102,241,.2)">Why StudAI Hire</div>
                <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-6" style="color:#1a1a2e">
                    Why 50,000+ Professionals Choose StudAI Hire
                </h2>
                <div class="space-y-4">
                    @foreach([
                        ['Zero Manual Applications','Our AI agent handles everything � fully autonomous.'],
                        ['Interview-Ready in Hours','AI mock interviews tailored to your exact target role.'],
                        ['Salary Negotiation AI','Know your worth. Coach yourself through the negotiation.'],
                        ['One Profile. Infinite Reach.','Apply everywhere with a single unified career profile.'],
                    ] as $reason)
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0" style="background:#6366f1">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <span class="font-semibold text-sm" style="color:#1a1a2e">{{ $reason[0] }}</span>
                            <span class="text-sm" style="color:#6b7280"> � {{ $reason[1] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-10 flex gap-4">
                    <a href="{{ route('register') }}" class="btn-brand">Get Started Free</a>
                    <a href="{{ route('login') }}" class="btn-ghost">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =====================================================
     TESTIMONIALS
===================================================== --}}
<section class="py-24" style="background:white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 mb-4 text-xs font-semibold uppercase tracking-widest" style="background:#f0f0ff; color:#6366f1; border:1px solid rgba(99,102,241,.2)">Success Stories</div>
            <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight" style="color:#1a1a2e">Careers Transformed</h2>
            <p class="mt-4 text-lg" style="color:#6b7280">Real people. Real results. Real careers on autopilot.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
            $testimonials = [
                [
                    'quote' => '"StudAI Hire got me 3 offers in 2 weeks. The autonomous agent is like having a full-time job search assistant. I didn\'t fill a single form myself."',
                    'name'  => 'Priya Sharma',
                    'title' => 'Senior Software Engineer at Google',
                    'init'  => 'PS',
                    'color' => '#6366f1',
                    'stars' => 5,
                ],
                [
                    'quote' => '"The interview AI helped me crack my Amazon SDE-2 interview. The behavioral prep was spot-on. It\'s like they knew exactly what Amazon would ask."',
                    'name'  => 'Rahul Menon',
                    'title' => 'SDE-2 at Amazon',
                    'init'  => 'RM',
                    'color' => '#a855f7',
                    'stars' => 5,
                ],
                [
                    'quote' => '"As a recruiter, S.C.O.U.T. changed how we hire. Time-to-hire dropped 60%. Quality went up. And our diversity metrics finally moved."',
                    'name'  => 'Anjali Verma',
                    'title' => 'Head of Talent at Razorpay',
                    'init'  => 'AV',
                    'color' => '#14b8a6',
                    'stars' => 5,
                ],
            ];
            @endphp
            @foreach($testimonials as $t)
            <div class="testi-card p-7 flex flex-col gap-5">
                {{-- Stars --}}
                <div class="flex gap-1">
                    @for($i=0;$i<5;$i++)
                    <svg class="w-4 h-4" style="color:#f59e0b" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-sm leading-relaxed flex-1" style="color:#374151">{{ $t['quote'] }}</p>
                <div class="flex items-center gap-3 pt-4 border-t" style="border-color:var(--border)">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background:{{ $t['color'] }}">{{ $t['init'] }}</div>
                    <div>
                        <div class="text-sm font-bold" style="color:#1a1a2e">{{ $t['name'] }}</div>
                        <div class="text-xs" style="color:#9ca3af">{{ $t['title'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Company logos --}}
        <div class="mt-14">
            <p class="text-center text-xs font-semibold uppercase tracking-widest mb-8" style="color:#9ca3af">Our users work at top companies</p>
            <div class="flex flex-wrap items-center justify-center gap-8">
                @foreach(['Google','Amazon','Microsoft','Razorpay','Flipkart','Swiggy','Zerodha','Meesho'] as $co)
                <div class="px-5 py-2.5 rounded-xl text-sm font-bold" style="background:#f7f7fc; border:1px solid var(--border); color:#6b7280">{{ $co }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- =====================================================
     FAQ
===================================================== --}}
<section class="py-24 dot-grid" style="background:#f7f7fc">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 mb-4 text-xs font-semibold uppercase tracking-widest" style="background:#f0f0ff; color:#6366f1; border:1px solid rgba(99,102,241,.2)">FAQ</div>
            <h2 class="text-4xl font-extrabold tracking-tight" style="color:#1a1a2e">Questions? Answered.</h2>
        </div>

        <div class="space-y-3">
            @php $faqs = [
                ['What is an Autonomous Career OS?', 'StudAI Hire is a unified system that manages your entire career lifecycle � from job discovery to salary negotiation � using AI agents that work 24/7 on your behalf. Think of it as having a dedicated career team that never sleeps.'],
                ['Is the autonomous job application really automatic?', 'Yes. Once you set your preferences (role, location, salary), our AI agent scans thousands of job boards, filters by your criteria, tailors your resume, and submits applications � all without any input from you. You only step in to confirm interviews.'],
                ['How is this different from LinkedIn or Naukri?', 'LinkedIn and Naukri are job boards � you browse, you apply. StudAI Hire is an operating system. It applies for you, coaches you for interviews, analyzes salary data, and tracks your entire pipeline � all powered by AI agents running in the background.'],
                ['What is S.C.O.U.T.?', 'S.C.O.U.T. (Smart Candidate Optimization & Unified Tracking) is our enterprise AI hiring tool for employers. It shortlists candidates based on skills and culture fit, eliminates bias, and reduces time-to-hire by up to 60%.'],
                ['Is there a free plan?', 'Absolutely. You can start completely free � no credit card required. Free users get access to job search, resume analysis, and limited AI applications. Upgrade anytime to unlock unlimited autonomous applications and all premium features.'],
            ]; @endphp

            @foreach($faqs as $faq)
            <details class="group rounded-2xl overflow-hidden" style="background:white; border:1px solid var(--border)">
                <summary class="flex items-center justify-between px-6 py-5 cursor-pointer">
                    <span class="font-semibold text-sm pr-4" style="color:#1a1a2e">{{ $faq[0] }}</span>
                    <div class="faq-chevron flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" style="background:#f0f0ff">
                        <svg class="w-4 h-4" style="color:#6366f1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </summary>
                <div class="px-6 pb-5 text-sm leading-relaxed" style="color:#6b7280">{{ $faq[1] }}</div>
            </details>
            @endforeach
        </div>
    </div>
</section>

{{-- =====================================================
     CTA
===================================================== --}}
<section class="py-24 cta-section relative overflow-hidden">
    {{-- Decorative orbs --}}
    <div class="absolute -top-24 -right-24 w-64 h-64 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-64 h-64 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight text-white mb-4">
            Ready to put your career on autopilot?
        </h2>
        <p class="text-xl text-indigo-200 mb-10 max-w-xl mx-auto">
            Join 50,000+ professionals who let AI manage their job search. Start free � no credit card required.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('register') }}"
                class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl font-bold text-base bg-white transition-all hover:-translate-y-1 hover:shadow-2xl active:scale-98"
                style="color:#6366f1; box-shadow:0 4px 20px rgba(0,0,0,.2)">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Start Free � No Credit Card
            </a>
            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl font-bold text-base text-white border-2 border-white/30 transition-all hover:border-white/60 hover:-translate-y-1">
                View Pricing
            </a>
        </div>
    </div>
</section>

@endsection
