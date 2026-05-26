<x-layouts.marketing>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- HERO SECTION                                    --}}
    {{-- ══════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden min-h-[90vh] flex items-center">
        {{-- Mesh grid + radial glow overlays --}}
        <div class="hero-grid"></div>
        <div class="hero-radial"></div>
        {{-- Background orbs - more, bigger, more colorful --}}
        <div class="orb orb-indigo w-[650px] h-[650px] -top-48 -left-28 opacity-35"></div>
        <div class="orb orb-pink   w-[500px] h-[500px] -bottom-28 right-0 opacity-30"></div>
        <div class="orb orb-cyan   w-[380px] h-[380px] top-1/3 right-1/4 opacity-25"></div>
        <div class="orb orb-violet w-[320px] h-[320px] top-8 right-8 opacity-20"></div>
        <div class="orb orb-amber  w-[240px] h-[240px] bottom-1/4 left-1/4 opacity-15"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32 w-full">

            {{-- Powered by badge — animated rainbow border --}}
            <div class="flex justify-center mb-8">
                <div class="badge-rainbow animate-float-slow">
                    <div class="badge-rainbow-inner flex items-center gap-2 text-xs font-medium text-indigo-200">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-300"></span>
                        </span>
                        Powered by <span class="font-bold bg-gradient-to-r from-indigo-300 via-fuchsia-300 to-cyan-300 bg-clip-text text-transparent">Orin™</span> — Dual-sided AI Platform
                    </div>
                </div>
            </div>

            {{-- Main headline --}}
            <div class="text-center max-w-5xl mx-auto mb-10 space-y-5">
                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold tracking-tight leading-tight">
                    <span class="gradient-text">Your Career.</span>
                    <br>
                    <span class="text-slate-100">On Autopilot.</span>
                </h1>
                <p class="text-lg sm:text-xl text-slate-300/90 max-w-3xl mx-auto leading-relaxed">
                    A <span class="text-white font-semibold">dual-sided AI platform</span> connecting job seekers with employers.
                    The <span class="text-indigo-300 font-semibold">AI Career Agent</span> for candidates.
                    The <span class="text-fuchsia-300 font-semibold">S.C.O.U.T Module</span> for companies.
                </p>
            </div>

            {{-- Dual CTA --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                <a href="{{ route('register') }}" class="btn-glow group inline-flex items-center justify-center gap-2 rounded-full px-8 py-3.5 text-sm font-bold text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    I'm a Job Seeker
                    <span class="group-hover:translate-x-1 transition-transform duration-200">→</span>
                </a>
                <a href="{{ route('register') }}?type=employer" class="animated-border group inline-flex items-center justify-center gap-2 rounded-full border border-fuchsia-500/50 bg-fuchsia-950/40 px-8 py-3.5 text-sm font-semibold text-fuchsia-200 hover:bg-fuchsia-900/50 hover:border-fuchsia-400 hover:-translate-y-1 transition-all duration-200 backdrop-blur-sm shadow-[0_0_30px_rgba(217,70,239,0.2)] hover:shadow-[0_0_55px_rgba(217,70,239,0.45)]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    I'm an Employer
                    <span class="group-hover:translate-x-1 transition-transform duration-200">→</span>
                </a>
            </div>

            {{-- Dual platform preview cards --}}
            <div class="grid lg:grid-cols-2 gap-6 max-w-5xl mx-auto">
                {{-- Candidate card --}}
                <div class="glass-panel rounded-2xl p-6 border border-indigo-500/20 hover:border-indigo-500/40 transition-colors">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-lg shadow-indigo-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                        </div>
                        <div>
                            <p class="text-[0.65rem] uppercase tracking-widest text-indigo-400 font-medium">For Candidates</p>
                            <p class="text-sm font-bold text-slate-100">AI Career Agent</p>
                        </div>
                        <span class="ml-auto text-[0.6rem] font-bold uppercase tracking-widest bg-indigo-500/20 text-indigo-300 rounded-full px-2.5 py-1 border border-indigo-500/30">Active</span>
                    </div>
                    <div class="space-y-2.5 text-sm">
                        <div class="flex items-center justify-between py-2 border-b border-slate-700/40">
                            <span class="text-slate-400 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>AI Match Score</span>
                            <span class="font-bold text-emerald-300">94%</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-700/40">
                            <span class="text-slate-400 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>Auto-Applied Today</span>
                            <span class="font-bold text-slate-100">7 jobs</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-700/40">
                            <span class="text-slate-400 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>Resume Optimized</span>
                            <span class="font-bold text-cyan-300">✓ Done</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-slate-400 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-fuchsia-400"></span>Interview Prep</span>
                            <span class="font-bold text-fuchsia-300">3 sessions</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-700/40">
                        <p class="text-[0.68rem] text-slate-400">Agent status: <span class="text-emerald-300 font-semibold">Scanning 2,400 jobs right now…</span></p>
                    </div>
                </div>

                {{-- Employer / SCOUT card --}}
                <div class="glass-panel rounded-2xl p-6 border border-fuchsia-500/20 hover:border-fuchsia-500/40 transition-colors">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-fuchsia-500 to-pink-600 shadow-lg shadow-fuchsia-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <p class="text-[0.65rem] uppercase tracking-widest text-fuchsia-400 font-medium">For Employers</p>
                            <p class="text-sm font-bold text-slate-100">S.C.O.U.T Module</p>
                        </div>
                        <span class="ml-auto text-[0.6rem] font-bold uppercase tracking-widest bg-fuchsia-500/20 text-fuchsia-300 rounded-full px-2.5 py-1 border border-fuchsia-500/30">Live</span>
                    </div>
                    <div class="space-y-2.5 text-sm">
                        <div class="flex items-center justify-between py-2 border-b border-slate-700/40">
                            <span class="text-slate-400 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-fuchsia-400"></span>Corporate DNA Score</span>
                            <span class="font-bold text-fuchsia-300">87/100</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-700/40">
                            <span class="text-slate-400 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-pink-400"></span>Culture-Fit Predicted</span>
                            <span class="font-bold text-slate-100">23 candidates</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-700/40">
                            <span class="text-slate-400 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Skills Verified</span>
                            <span class="font-bold text-amber-300">41 applicants</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-slate-400 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Hire Prediction Accuracy</span>
                            <span class="font-bold text-emerald-300">91%</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-700/40">
                        <p class="text-[0.68rem] text-slate-400">DNA analysis: <span class="text-fuchsia-300 font-semibold">Decoding your company culture…</span></p>
                    </div>
                </div>
            </div>

            {{-- Trust bar --}}
            <div class="flex flex-wrap items-center justify-center gap-8 mt-14 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <span class="text-emerald-400">✓</span> No credit card required
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-emerald-400">✓</span> Free 14-day trial
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-emerald-400">✓</span> Cancel anytime
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-emerald-400">✓</span> Orin™ AI Engine
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- CAPABILITIES SECTION                            --}}
    {{-- ══════════════════════════════════════════════ --}}
    <section id="features" class="relative py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            {{-- Section header --}}
            <div class="text-center mb-16 space-y-4">
                <span class="text-[0.65rem] uppercase tracking-[0.25em] text-slate-500 font-semibold">Capabilities</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight">
                    <span class="gradient-text">Powerful Features</span>
                    <span class="block text-slate-200 mt-2 text-2xl sm:text-3xl font-medium">Everything you need to transform your workflow with Career</span>
                </h2>
            </div>

            {{-- Features grid --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">

                {{-- 1: AI Career Agent --}}
                <article class="card-soft group p-6 hover:scale-[1.02] hover:border-indigo-500/40 transition-all duration-200 border border-transparent">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500/20 to-indigo-700/20 text-indigo-300 mb-4 shadow-lg shadow-indigo-500/10 group-hover:shadow-indigo-500/25 transition-shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-50 text-base mb-2">AI Career Agent</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Personal AI agent for job seekers — career planning, resume optimization, and autonomous job applications. Your career on autopilot.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">Career Planning</span>
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">Auto-Apply</span>
                    </div>
                </article>

                {{-- 2: SCOUT Module --}}
                <article class="card-soft group p-6 hover:scale-[1.02] hover:border-fuchsia-500/40 transition-all duration-200 border border-transparent">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-fuchsia-500/20 to-pink-700/20 text-fuchsia-300 mb-4 shadow-lg shadow-fuchsia-500/10 group-hover:shadow-fuchsia-500/25 transition-shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-50 text-base mb-2">S.C.O.U.T Module</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">For employers — decodes your Corporate DNA and predicts which candidates will succeed at your company. AI-powered hiring intelligence.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-fuchsia-500/10 text-fuchsia-300 border border-fuchsia-500/20">DNA Analysis</span>
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-fuchsia-500/10 text-fuchsia-300 border border-fuchsia-500/20">Hire Prediction</span>
                    </div>
                </article>

                {{-- 3: Corporate DNA --}}
                <article class="card-soft group p-6 hover:scale-[1.02] hover:border-amber-500/40 transition-all duration-200 border border-transparent">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500/20 to-orange-700/20 text-amber-300 mb-4 shadow-lg shadow-amber-500/10 group-hover:shadow-amber-500/25 transition-shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-50 text-base mb-2">Corporate DNA Analysis</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">AI analyzes company culture, values, and success patterns to improve hiring accuracy and reduce turnover through data-driven insights.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/20">Culture Decode</span>
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/20">Success Patterns</span>
                    </div>
                </article>

                {{-- 4: Skills Matching --}}
                <article class="card-soft group p-6 hover:scale-[1.02] hover:border-cyan-500/40 transition-all duration-200 border border-transparent">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500/20 to-sky-700/20 text-cyan-300 mb-4 shadow-lg shadow-cyan-500/10 group-hover:shadow-cyan-500/25 transition-shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-50 text-base mb-2">Skills Matching</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Deep skills verification beyond keywords — actual competency assessment and validation. Know exactly what a candidate can do, not just what they claim.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">Competency Test</span>
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">Validation</span>
                    </div>
                </article>

                {{-- 5: Culture Fit --}}
                <article class="card-soft group p-6 hover:scale-[1.02] hover:border-emerald-500/40 transition-all duration-200 border border-transparent">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/20 to-green-700/20 text-emerald-300 mb-4 shadow-lg shadow-emerald-500/10 group-hover:shadow-emerald-500/25 transition-shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-50 text-base mb-2">Culture Fit Prediction</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Predict candidate success based on culture alignment, not just technical skills. Reduce costly mis-hires before day one.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">Retention Score</span>
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">Fit Index</span>
                    </div>
                </article>

                {{-- 6: Autonomous Applications --}}
                <article class="card-soft group p-6 hover:scale-[1.02] hover:border-violet-500/40 transition-all duration-200 border border-transparent">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500/20 to-purple-700/20 text-violet-300 mb-4 shadow-lg shadow-violet-500/10 group-hover:shadow-violet-500/25 transition-shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-50 text-base mb-2">Autonomous Applications</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">AI agent applies to relevant jobs on behalf of candidates with personalized cover letters — while you sleep, network, or focus on life.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-violet-500/10 text-violet-300 border border-violet-500/20">24/7 Agent</span>
                        <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-violet-500/10 text-violet-300 border border-violet-500/20">Cover Letters</span>
                    </div>
                </article>

            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- HOW IT WORKS — DUAL FLOW                        --}}
    {{-- ══════════════════════════════════════════════ --}}
    <section class="relative py-24 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="orb orb-indigo w-96 h-96 -left-32 top-10 opacity-20"></div>
        <div class="orb orb-pink w-96 h-96 -right-32 bottom-10 opacity-15"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-16 space-y-3">
                <span class="text-[0.65rem] uppercase tracking-[0.25em] text-slate-500 font-semibold">How It Works</span>
                <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-100">One platform. Two superpowers.</h2>
                <p class="text-slate-400 text-base max-w-2xl mx-auto">Whether you're finding your dream job or building your dream team, Orin™ AI has you covered.</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-8">
                {{-- Job Seeker Flow --}}
                <div class="glass-panel rounded-2xl p-8 border border-indigo-500/20">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="h-10 w-10 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-[0.65rem] uppercase tracking-widest text-indigo-400 font-semibold">For Job Seekers</p>
                            <h3 class="font-bold text-white">AI Career Agent Flow</h3>
                        </div>
                    </div>
                    <div class="space-y-5">
                        @foreach([
                            ['step' => '01', 'title' => 'Upload your resume', 'desc' => 'AI parses and enhances your profile, extracting skills, experience, and competencies automatically.', 'color' => 'indigo'],
                            ['step' => '02', 'title' => 'Set your career goals', 'desc' => 'Tell Orin™ your target roles, salary, location preferences, and culture needs.', 'color' => 'indigo'],
                            ['step' => '03', 'title' => 'Agent takes over', 'desc' => 'AI autonomously finds matches, customizes applications, writes cover letters, and applies on your behalf.', 'color' => 'indigo'],
                            ['step' => '04', 'title' => 'Land interviews', 'desc' => 'Get interview prep, salary negotiation coaching, and real-time feedback to close the deal.', 'color' => 'indigo'],
                        ] as $item)
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-bold">{{ $item['step'] }}</div>
                            <div>
                                <p class="font-semibold text-slate-100 text-sm">{{ $item['title'] }}</p>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 hover:bg-indigo-500 px-6 py-2.5 text-sm font-semibold text-white transition-colors">
                            Start as Job Seeker
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Employer Flow --}}
                <div class="glass-panel rounded-2xl p-8 border border-fuchsia-500/20">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="h-10 w-10 rounded-xl bg-fuchsia-500/20 flex items-center justify-center text-fuchsia-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="text-[0.65rem] uppercase tracking-widest text-fuchsia-400 font-semibold">For Employers</p>
                            <h3 class="font-bold text-white">S.C.O.U.T Module Flow</h3>
                        </div>
                    </div>
                    <div class="space-y-5">
                        @foreach([
                            ['step' => '01', 'title' => 'Decode your Corporate DNA', 'desc' => 'AI analyzes your company culture, values, and the traits of your top performers to build a success blueprint.'],
                            ['step' => '02', 'title' => 'Post roles intelligently', 'desc' => 'AI-assisted job descriptions optimized to attract the right candidates who will thrive in your environment.'],
                            ['step' => '03', 'title' => 'Screen with deep intelligence', 'desc' => 'Beyond keywords — actual skills validation, culture-fit scoring, and success probability per candidate.'],
                            ['step' => '04', 'title' => 'Hire with confidence', 'desc' => 'Ranked shortlists with predicted retention scores. Make data-driven decisions and reduce bad hires.'],
                        ] as $item)
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-fuchsia-500/10 border border-fuchsia-500/30 text-fuchsia-300 text-xs font-bold">{{ $item['step'] }}</div>
                            <div>
                                <p class="font-semibold text-slate-100 text-sm">{{ $item['title'] }}</p>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('register') }}?type=employer" class="inline-flex items-center gap-2 rounded-full bg-fuchsia-600 hover:bg-fuchsia-500 px-6 py-2.5 text-sm font-semibold text-white transition-colors">
                            Start as Employer
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- USE CASES SECTION                               --}}
    {{-- ══════════════════════════════════════════════ --}}
    <section class="relative py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 space-y-3">
                <span class="text-[0.65rem] uppercase tracking-[0.25em] text-slate-500 font-semibold">Applications</span>
                <h2 class="text-3xl sm:text-4xl font-bold tracking-tight">
                    <span class="gradient-text">Use Cases</span>
                    <span class="block text-slate-200 mt-2 text-xl sm:text-2xl font-medium">How businesses use Career to achieve results</span>
                </h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">

                {{-- Campus Hiring --}}
                <div class="card-soft p-6 group hover:border-indigo-500/40 border border-transparent transition-all hover:scale-[1.02]">
                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center mb-5 shadow-lg shadow-indigo-500/30 group-hover:shadow-indigo-500/50 transition-shadow">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-100 mb-2">Campus Hiring</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Connect with fresh talent from colleges with AI-powered screening that identifies potential, not just experience.</p>
                    <div class="mt-4 pt-4 border-t border-slate-700/40">
                        <p class="text-[0.7rem] text-indigo-400 font-medium">→ Perfect for recruiters targeting fresh graduates</p>
                    </div>
                </div>

                {{-- Experienced Hiring --}}
                <div class="card-soft p-6 group hover:border-fuchsia-500/40 border border-transparent transition-all hover:scale-[1.02]">
                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-fuchsia-500 to-pink-600 flex items-center justify-center mb-5 shadow-lg shadow-fuchsia-500/30 group-hover:shadow-fuchsia-500/50 transition-shadow">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-100 mb-2">Experienced Hiring</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Find and validate experienced professionals with deep skill verification that goes beyond the resume.</p>
                    <div class="mt-4 pt-4 border-t border-slate-700/40">
                        <p class="text-[0.7rem] text-fuchsia-400 font-medium">→ Mid to senior level talent acquisition</p>
                    </div>
                </div>

                {{-- Culture-Fit Hiring --}}
                <div class="card-soft p-6 group hover:border-emerald-500/40 border border-transparent transition-all hover:scale-[1.02]">
                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mb-5 shadow-lg shadow-emerald-500/30 group-hover:shadow-emerald-500/50 transition-shadow">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-100 mb-2">Culture-Fit Hiring</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Hire candidates who will thrive in your company culture. Predict success and retention before you extend an offer.</p>
                    <div class="mt-4 pt-4 border-t border-slate-700/40">
                        <p class="text-[0.7rem] text-emerald-400 font-medium">→ Reduce turnover, increase team cohesion</p>
                    </div>
                </div>

                {{-- Career Services --}}
                <div class="card-soft p-6 group hover:border-amber-500/40 border border-transparent transition-all hover:scale-[1.02]">
                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center mb-5 shadow-lg shadow-amber-500/30 group-hover:shadow-amber-500/50 transition-shadow">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-100 mb-2">Career Services</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Scale career counseling for institutions with AI agents. Give every student a personal career advisor — 24/7.</p>
                    <div class="mt-4 pt-4 border-t border-slate-700/40">
                        <p class="text-[0.7rem] text-amber-400 font-medium">→ Universities, bootcamps, career centers</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- MARQUEE STRIP                                   --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div class="relative py-5 overflow-hidden border-y border-slate-800/40 bg-slate-950/70 backdrop-blur-sm">
        <div class="marquee-wrap">
            <div class="marquee-track">
                @php
                $marqueeItems = [
                    ['icon' => '🤖', 'label' => 'AI Career Agent'],
                    ['icon' => '🎯', 'label' => 'SCOUT Module'],
                    ['icon' => '🧬', 'label' => 'Corporate DNA'],
                    ['icon' => '⚡', 'label' => 'Auto-Apply'],
                    ['icon' => '📊', 'label' => 'Skills Matching'],
                    ['icon' => '💼', 'label' => 'Interview Prep'],
                    ['icon' => '🔮', 'label' => 'Culture Fit AI'],
                    ['icon' => '📈', 'label' => 'Salary Negotiation'],
                    ['icon' => '🌍', 'label' => 'Remote Jobs'],
                    ['icon' => '🏆', 'label' => 'Hire Prediction'],
                    ['icon' => '🤖', 'label' => 'AI Career Agent'],
                    ['icon' => '🎯', 'label' => 'SCOUT Module'],
                    ['icon' => '🧬', 'label' => 'Corporate DNA'],
                    ['icon' => '⚡', 'label' => 'Auto-Apply'],
                    ['icon' => '📊', 'label' => 'Skills Matching'],
                    ['icon' => '💼', 'label' => 'Interview Prep'],
                    ['icon' => '🔮', 'label' => 'Culture Fit AI'],
                    ['icon' => '📈', 'label' => 'Salary Negotiation'],
                    ['icon' => '🌍', 'label' => 'Remote Jobs'],
                    ['icon' => '🏆', 'label' => 'Hire Prediction'],
                ];
                @endphp
                @foreach($marqueeItems as $item)
                <span class="inline-flex items-center gap-2 mx-8 text-sm font-medium text-slate-400 whitespace-nowrap select-none">
                    <span class="text-base">{{ $item['icon'] }}</span>
                    <span class="hover:text-slate-200 transition-colors cursor-default">{{ $item['label'] }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-700 ml-4"></span>
                </span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- STATS CARDS                                     --}}
    {{-- ══════════════════════════════════════════════ --}}
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="orb orb-indigo w-72 h-72 -top-16 -left-8 opacity-15"></div>
        <div class="orb orb-cyan   w-72 h-72 -bottom-16 -right-8 opacity-12"></div>
        <div class="max-w-5xl mx-auto relative z-10">
            <div class="text-center mb-10 space-y-2">
                <span class="section-tag">📊 Platform Impact</span>
                <p class="text-slate-400 text-sm mt-3">Numbers that speak for themselves</p>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="stat-card stat-indigo shimmer-card">
                    <div class="text-4xl font-extrabold text-indigo-300 mb-2 tracking-tight">94%</div>
                    <p class="text-xs text-indigo-400/80 font-bold uppercase tracking-widest">AI Match Accuracy</p>
                    <p class="text-xs text-slate-500 mt-1.5">Industry-leading precision</p>
                </div>
                <div class="stat-card stat-fuchsia shimmer-card">
                    <div class="text-4xl font-extrabold text-fuchsia-300 mb-2 tracking-tight">3×</div>
                    <p class="text-xs text-fuchsia-400/80 font-bold uppercase tracking-widest">Faster Hiring</p>
                    <p class="text-xs text-slate-500 mt-1.5">vs. traditional methods</p>
                </div>
                <div class="stat-card stat-cyan shimmer-card">
                    <div class="text-4xl font-extrabold text-cyan-300 mb-2 tracking-tight">40%</div>
                    <p class="text-xs text-cyan-400/80 font-bold uppercase tracking-widest">Lower Turnover</p>
                    <p class="text-xs text-slate-500 mt-1.5">Culture-fit driven hiring</p>
                </div>
                <div class="stat-card stat-emerald shimmer-card">
                    <div class="text-4xl font-extrabold text-emerald-300 mb-2 tracking-tight">50k+</div>
                    <p class="text-xs text-emerald-400/80 font-bold uppercase tracking-widest">Careers Transformed</p>
                    <p class="text-xs text-slate-500 mt-1.5">And counting every day</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- FINAL CTA                                        --}}
    {{-- ══════════════════════════════════════════════ --}}
    <section class="relative py-28 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="hero-grid opacity-40"></div>
        <div class="orb orb-indigo w-[600px] h-[600px] -top-32 left-1/2 -translate-x-1/2 opacity-25"></div>
        <div class="orb orb-pink   w-[400px] h-[400px] bottom-0 left-0  opacity-15"></div>
        <div class="orb orb-cyan   w-[350px] h-[350px] bottom-0 right-0 opacity-15"></div>
        <div class="max-w-4xl mx-auto text-center space-y-8 relative z-10">

            {{-- Rainbow animated badge --}}
            <div class="flex justify-center">
                <div class="badge-rainbow animate-float-slow">
                    <div class="badge-rainbow-inner flex items-center gap-2 text-xs font-medium text-indigo-200">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-300"></span>
                        </span>
                        Powered by Orin™ AI Engine
                    </div>
                </div>
            </div>

            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                Ready to transform
                <span class="block gradient-text">how careers are built?</span>
            </h2>
            <p class="text-lg text-slate-300/90 max-w-2xl mx-auto leading-relaxed">
                Join the platform where AI agents work around the clock — finding jobs for seekers, finding talent for employers.
                <span class="text-indigo-300 font-semibold">No effort. Maximum results.</span>
            </p>

            {{-- CTA glowing container --}}
            <div class="relative inline-block w-full max-w-xl mx-auto">
                <div class="absolute -inset-4 rounded-3xl bg-gradient-to-r from-indigo-500/20 via-fuchsia-500/20 to-cyan-500/20 blur-2xl"></div>
                <div class="relative glass-panel rounded-2xl p-8">
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="btn-glow inline-flex items-center justify-center gap-2 rounded-full px-10 py-4 text-base font-bold text-white w-full sm:w-auto">
                            🚀 Get Started Free
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="{{ route('jobs.search') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-600/60 bg-slate-800/50 px-8 py-4 text-base font-medium text-slate-200 hover:border-slate-400 hover:bg-slate-700/60 transition-all duration-200 backdrop-blur-sm w-full sm:w-auto">
                            Browse Jobs →
                        </a>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-6 mt-6 pt-5 border-t border-slate-700/50 text-xs text-slate-500">
                        <span class="flex items-center gap-1.5"><span class="text-emerald-400">✓</span> No credit card required</span>
                        <span class="flex items-center gap-1.5"><span class="text-emerald-400">✓</span> Free 14-day trial</span>
                        <span class="flex items-center gap-1.5"><span class="text-emerald-400">✓</span> Cancel anytime</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layouts.marketing>
