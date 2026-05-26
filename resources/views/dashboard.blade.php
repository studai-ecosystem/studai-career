@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Your career command center')

@section('content')
@php
    $greeting = now()->format('H') < 12 ? 'Good morning' : (now()->format('H') < 17 ? 'Good afternoon' : 'Good evening');
    $name = auth()->user()->name ?? 'there';
    $completeness = 72;
    $xp = 3240;
    $xpMax = 5000;
    $level = 8;
@endphp

<div class="space-y-6 animate-fade-in">

    {{-- ═══ HERO GREETING + PROFILE COMPLETENESS RING ═══ --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-fuchsia-500 rounded-2xl p-6 text-white">
        {{-- Background mesh --}}
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,.3) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(255,255,255,.2) 0%, transparent 40%);"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            {{-- Left: greeting --}}
            <div class="flex items-center gap-4">
                {{-- Profile completeness ring (large) --}}
                <div class="relative flex-shrink-0 w-16 h-16">
                    <svg class="completeness-ring w-16 h-16" viewBox="0 0 64 64">
                        <circle cx="32" cy="32" r="28" fill="none" stroke="rgba(255,255,255,.25)" stroke-width="5"/>
                        <circle cx="32" cy="32" r="28" fill="none" stroke="white" stroke-width="5"
                            stroke-dasharray="175.93"
                            stroke-dashoffset="{{ 175.93 - (175.93 * $completeness / 100) }}"
                            stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-sm font-bold text-white">{{ $completeness }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-indigo-200 text-sm font-medium">{{ now()->format('l, F j') }}</p>
                    <h1 class="text-2xl font-bold">{{ $greeting }}, {{ explode(' ', $name)[0] }}! 👋</h1>
                    <p class="text-indigo-100 text-sm mt-0.5">Your profile is {{ $completeness }}% complete · <a href="{{ route('profile.edit') }}" class="underline hover:no-underline">Finish it</a> for 3× more matches</p>
                </div>
            </div>

            {{-- Right: quick actions --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('jobs.search') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50 transition-all hover:-translate-y-0.5 text-sm shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Find Jobs
                </a>
                <a href="{{ route('career-coach.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/15 text-white font-semibold rounded-xl hover:bg-white/25 transition-all hover:-translate-y-0.5 text-sm border border-white/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Career Coach
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ XP GAMIFICATION STRIP ═══ --}}
    <div class="bg-white rounded-2xl border border-surface-200 shadow-card px-5 py-3">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 flex-shrink-0">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                    <span class="text-xs font-bold text-white">{{ $level }}</span>
                </div>
                <div>
                    <div class="text-xs font-semibold text-ink-primary">Level {{ $level }}</div>
                    <div class="text-[10px] text-ink-tertiary">Career Pro</div>
                </div>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-ink-secondary font-medium">{{ number_format($xp) }} XP</span>
                    <span class="text-xs text-ink-tertiary">{{ number_format($xpMax) }} XP to Level {{ $level + 1 }}</span>
                </div>
                <div class="h-2 bg-surface-100 rounded-full overflow-hidden">
                    <div class="xp-bar h-full rounded-full" style="width: {{ round($xp / $xpMax * 100) }}%"></div>
                </div>
            </div>
            <a href="{{ route('gamification.dashboard') }}" class="flex-shrink-0 text-xs font-medium text-module-vantage-600 hover:text-module-vantage-700">View Badges →</a>
        </div>
    </div>

    {{-- ═══ COLORFUL STAT CARDS ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 stagger">
        {{-- Resume Score — blue --}}
        <div class="card-lift bg-white rounded-2xl border border-surface-200 shadow-card p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-module-market-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-module-market-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs font-medium text-module-jobs-600 bg-module-jobs-50 px-2 py-0.5 rounded-full">↑ 12%</span>
            </div>
            <div class="count-up text-3xl font-bold text-ink-primary" data-target="87">87<span class="text-lg font-semibold text-ink-tertiary">%</span></div>
            <div class="text-sm text-ink-secondary mt-1">Resume Score</div>
            <div class="mt-3 h-1.5 bg-module-market-100 rounded-full"><div class="h-full bg-module-market-600 rounded-full skill-bar-fill" style="width:87%"></div></div>
        </div>

        {{-- AI Match Rate — green --}}
        <div class="card-lift bg-white rounded-2xl border border-surface-200 shadow-card p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-module-jobs-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-module-jobs-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-xs font-medium text-module-jobs-600 bg-module-jobs-50 px-2 py-0.5 rounded-full">↑ 8%</span>
            </div>
            <div class="count-up text-3xl font-bold text-ink-primary" data-target="94">94<span class="text-lg font-semibold text-ink-tertiary">%</span></div>
            <div class="text-sm text-ink-secondary mt-1">AI Match Rate</div>
            <div class="mt-3 h-1.5 bg-module-jobs-100 rounded-full"><div class="h-full bg-module-jobs-600 rounded-full skill-bar-fill" style="width:94%"></div></div>
        </div>

        {{-- Applications — purple --}}
        <div class="card-lift bg-white rounded-2xl border border-surface-200 shadow-card p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-module-coach-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-module-coach-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-xs font-medium text-module-coach-600 bg-module-coach-50 px-2 py-0.5 rounded-full">3 this week</span>
            </div>
            <div class="count-up text-3xl font-bold text-ink-primary" data-target="24">24</div>
            <div class="text-sm text-ink-secondary mt-1">Applications</div>
            <div class="mt-3 flex gap-1">
                @foreach([1,1,1,1,1,1,1,0,0,0] as $filled)
                <div class="flex-1 h-1.5 rounded-full {{ $filled ? 'bg-module-coach-500' : 'bg-module-coach-100' }}"></div>
                @endforeach
            </div>
        </div>

        {{-- Interviews — orange --}}
        <div class="card-lift bg-white rounded-2xl border border-surface-200 shadow-card p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-module-interview-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-module-interview-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-medium text-module-interview-600 bg-module-interview-50 px-2 py-0.5 rounded-full">2 scheduled</span>
            </div>
            <div class="count-up text-3xl font-bold text-ink-primary" data-target="5">5</div>
            <div class="text-sm text-ink-secondary mt-1">Interviews</div>
            <div class="mt-3 flex items-center gap-1.5">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-module-interview-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-module-interview-500"></span></span>
                <span class="text-xs text-module-interview-600 font-medium">Next: Tomorrow 10AM</span>
            </div>
        </div>
    </div>

    {{-- ═══ MAIN GRID ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: AI Job Matches (2 cols) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- AI Job Matches --}}
            <div class="bg-white rounded-2xl border border-surface-200 shadow-card">
                <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-surface-100">
                    <div>
                        <h2 class="text-base font-semibold text-ink-primary">AI Job Matches</h2>
                        <p class="text-xs text-ink-tertiary mt-0.5">Ranked by compatibility with your profile</p>
                    </div>
                    <a href="{{ route('jobs.search') }}" class="text-sm font-medium text-module-jobs-600 hover:text-module-jobs-700 flex items-center gap-1">View all <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></a>
                </div>
                <div class="divide-y divide-surface-50">
                    @php $jobs = [
                        ['co'=>'Google','role'=>'Senior Software Engineer','loc'=>'Remote · $180k-$250k','score'=>96,'grad'=>'from-blue-500 to-purple-600','letter'=>'G'],
                        ['co'=>'Spotify','role'=>'Staff Product Designer','loc'=>'Hybrid · $160k-$220k','score'=>92,'grad'=>'from-green-500 to-teal-500','letter'=>'S'],
                        ['co'=>'Meta','role'=>'Engineering Manager','loc'=>'Remote · $200k-$300k','score'=>89,'grad'=>'from-orange-500 to-red-500','letter'=>'M'],
                        ['co'=>'Stripe','role'=>'Lead ML Engineer','loc'=>'SF · $220k-$320k','score'=>85,'grad'=>'from-indigo-500 to-purple-600','letter'=>'S'],
                    ]; @endphp
                    @foreach($jobs as $j)
                    <div class="group flex items-center gap-4 px-6 py-4 hover:bg-surface-50 transition-colors cursor-pointer">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $j['grad'] }} flex items-center justify-center text-white font-bold text-base flex-shrink-0">{{ $j['letter'] }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-ink-primary text-sm group-hover:text-module-jobs-600 transition-colors">{{ $j['role'] }}</div>
                            <div class="text-xs text-ink-tertiary mt-0.5">{{ $j['co'] }} · {{ $j['loc'] }}</div>
                        </div>
                        {{-- Match score ring --}}
                        <div class="relative flex-shrink-0 w-12 h-12">
                            <svg class="w-12 h-12" style="transform:rotate(-90deg)" viewBox="0 0 48 48">
                                <circle cx="24" cy="24" r="20" fill="none" stroke="#e2e8f0" stroke-width="4"/>
                                <circle cx="24" cy="24" r="20" fill="none" stroke="{{ $j['score']>=90 ? '#16a34a' : ($j['score']>=80 ? '#2563eb' : '#d97706') }}" stroke-width="4"
                                    stroke-dasharray="125.66"
                                    stroke-dashoffset="{{ round(125.66 - (125.66 * $j['score'] / 100)) }}"
                                    stroke-linecap="round"/>
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-[11px] font-bold {{ $j['score']>=90 ? 'text-module-jobs-600' : ($j['score']>=80 ? 'text-module-market-600' : 'text-module-negotiation-600') }}">{{ $j['score'] }}%</span>
                        </div>
                        <a href="{{ route('jobs.search') }}" class="flex-shrink-0 px-3 py-1.5 text-xs font-semibold text-module-jobs-600 bg-module-jobs-50 hover:bg-module-jobs-100 rounded-lg transition-colors">Apply</a>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Application Pipeline Kanban --}}
            <div class="bg-white rounded-2xl border border-surface-200 shadow-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-ink-primary">Application Pipeline</h2>
                    <a href="{{ route('dashboard.applications') }}" class="text-sm text-module-market-600 hover:text-module-market-700 font-medium">Full view →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php $pipeline = [
                        ['label'=>'Applied','count'=>12,'color'=>'bg-module-market-500','light'=>'bg-module-market-50','text'=>'text-module-market-600'],
                        ['label'=>'Screening','count'=>6,'color'=>'bg-module-negotiation-500','light'=>'bg-module-negotiation-50','text'=>'text-module-negotiation-600'],
                        ['label'=>'Interview','count'=>3,'color'=>'bg-module-interview-500','light'=>'bg-module-interview-50','text'=>'text-module-interview-600'],
                        ['label'=>'Offer','count'=>2,'color'=>'bg-module-jobs-500','light'=>'bg-module-jobs-50','text'=>'text-module-jobs-600'],
                    ]; @endphp
                    @foreach($pipeline as $stage)
                    <div class="rounded-xl {{ $stage['light'] }} p-4 text-center">
                        <div class="text-2xl font-bold {{ $stage['text'] }}">{{ $stage['count'] }}</div>
                        <div class="text-xs font-medium {{ $stage['text'] }} mt-1">{{ $stage['label'] }}</div>
                        <div class="mt-2 h-1 rounded-full bg-white/60">
                            <div class="h-full rounded-full {{ $stage['color'] }}" style="width:{{ min(100, $stage['count'] * 8) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Tools + AI Agent --}}
        <div class="space-y-5">

            {{-- AI Agent Status --}}
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-5 text-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-sm">AI Agent</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span></span>
                            <span class="text-xs text-indigo-200">Active · Scanning jobs</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-indigo-200">Jobs Analyzed</span><span class="font-semibold">147 today</span></div>
                    <div class="flex justify-between"><span class="text-indigo-200">Applications Sent</span><span class="font-semibold">12 today</span></div>
                    <div class="flex justify-between"><span class="text-indigo-200">Queue</span><span class="font-semibold">8 pending</span></div>
                </div>
                <a href="{{ route('agent.dashboard') }}" class="mt-4 flex items-center justify-center gap-1.5 text-sm font-medium text-white hover:text-indigo-200 transition-colors">
                    View Agent Dashboard <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Skill Gaps radar-style bars --}}
            <div class="bg-white rounded-2xl border border-surface-200 shadow-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-ink-primary">Skill Gap Radar</h3>
                    <a href="{{ route('resume.index') }}" class="text-xs text-module-coach-600 font-medium hover:underline">Improve →</a>
                </div>
                <div class="space-y-3">
                    @php $skills = [
                        ['name'=>'System Design','pct'=>72,'color'=>'bg-module-negotiation-500'],
                        ['name'=>'Kubernetes','pct'=>58,'color'=>'bg-module-scout-400'],
                        ['name'=>'GraphQL','pct'=>85,'color'=>'bg-module-jobs-500'],
                        ['name'=>'Python ML','pct'=>78,'color'=>'bg-module-coach-500'],
                    ]; @endphp
                    @foreach($skills as $s)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-ink-secondary">{{ $s['name'] }}</span>
                            <span class="text-xs font-semibold text-ink-primary">{{ $s['pct'] }}%</span>
                        </div>
                        <div class="h-1.5 bg-surface-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full skill-bar-fill {{ $s['color'] }}" style="width:{{ $s['pct'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Upcoming interviews --}}
            <div class="bg-white rounded-2xl border border-surface-200 shadow-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-ink-primary">Upcoming Interviews</h3>
                    <span class="text-xs font-semibold text-module-interview-600 bg-module-interview-50 px-2 py-0.5 rounded-full">2 this week</span>
                </div>
                <div class="space-y-3">
                    @foreach([['Google','Technical Round','May 15','10:00 AM','bg-module-market-100','text-module-market-600'],['Spotify','Culture Fit','May 18','2:30 PM','bg-module-jobs-100','text-module-jobs-600']] as $iv)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 {{ $iv[4] }} rounded-xl flex flex-col items-center justify-center flex-shrink-0">
                            <span class="text-[9px] font-bold {{ $iv[5] }} uppercase">{{ substr($iv[2], 4, 3) }}</span>
                            <span class="text-sm font-bold {{ $iv[5] }}">{{ substr($iv[2], 7) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-ink-primary">{{ $iv[0] }}</div>
                            <div class="text-xs text-ink-tertiary">{{ $iv[1] }} · {{ $iv[3] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ 6 CAREER TOOL TILES ═══ --}}
    <div>
        <h2 class="text-base font-semibold text-ink-primary mb-4">Career Tools</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 stagger">
            @php $tools = [
                ['name'=>'Career Coach','desc'=>'AI-powered guidance','icon'=>'zap','route'=>'career-coach.index','from'=>'from-purple-600','to'=>'to-violet-600','ring'=>'ring-purple-200'],
                ['name'=>'Interview Lab','desc'=>'Practice & improve','icon'=>'video','route'=>'interview.index','from'=>'from-orange-500','to'=>'to-red-500','ring'=>'ring-orange-200'],
                ['name'=>'Job Search','desc'=>'AI-ranked matches','icon'=>'search','route'=>'jobs.search','from'=>'from-green-500','to'=>'to-emerald-600','ring'=>'ring-green-200'],
                ['name'=>'Negotiation','desc'=>'Get paid more','icon'=>'cash','route'=>'negotiation.dashboard','from'=>'from-amber-500','to'=>'to-yellow-500','ring'=>'ring-amber-200'],
                ['name'=>'Achievements','desc'=>'XP & badges','icon'=>'star','route'=>'gamification.dashboard','from'=>'from-teal-500','to'=>'to-cyan-600','ring'=>'ring-teal-200'],
            ]; @endphp
            @foreach($tools as $t)
            <a href="{{ route($t['route']) }}" class="card-lift bg-white rounded-2xl border border-surface-200 p-4 flex flex-col items-center text-center gap-3 hover:border-surface-300 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $t['from'] }} {{ $t['to'] }} flex items-center justify-center shadow-soft ring-4 {{ $t['ring'] }} ring-opacity-40 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        @if($t['icon']==='zap')<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        @elseif($t['icon']==='video')<path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        @elseif($t['icon']==='search')<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        @elseif($t['icon']==='chart')<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        @elseif($t['icon']==='cash')<path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        @elseif($t['icon']==='star')<path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>@endif
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-semibold text-ink-primary">{{ $t['name'] }}</div>
                    <div class="text-xs text-ink-tertiary mt-0.5">{{ $t['desc'] }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ═══ ACTIVITY FEED ═══ --}}
    <div class="bg-white rounded-2xl border border-surface-200 shadow-card p-6">
        <h2 class="text-base font-semibold text-ink-primary mb-5">Recent Activity</h2>
        <div class="space-y-4">
            @php $activities = [
                ['icon'=>'check','text'=>'Application sent to <strong>Google</strong> — Senior SWE','time'=>'2h ago','color'=>'bg-module-jobs-100','icon_color'=>'text-module-jobs-600'],
                ['icon'=>'star','text'=>'Resume score improved to <strong>87%</strong>','time'=>'5h ago','color'=>'bg-module-negotiation-100','icon_color'=>'text-module-negotiation-600'],
                ['icon'=>'calendar','text'=>'Interview scheduled with <strong>Spotify</strong>','time'=>'Yesterday','color'=>'bg-module-interview-100','icon_color'=>'text-module-interview-600'],
                ['icon'=>'zap','text'=>'AI Agent found <strong>12 new matches</strong>','time'=>'Yesterday','color'=>'bg-module-coach-100','icon_color'=>'text-module-coach-600'],
                ['icon'=>'award','text'=>'Earned badge: <strong>Application Streak — 7 days</strong>','time'=>'2 days ago','color'=>'bg-module-vantage-100','icon_color'=>'text-module-vantage-600'],
            ]; @endphp
            @foreach($activities as $act)
            <div class="flex items-start gap-3 animate-fade-in">
                <div class="w-8 h-8 rounded-full {{ $act['color'] }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 {{ $act['icon_color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        @if($act['icon']==='check')<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @elseif($act['icon']==='star')<path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        @elseif($act['icon']==='calendar')<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        @elseif($act['icon']==='zap')<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        @elseif($act['icon']==='award')<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>@endif
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-ink-secondary">{!! $act['text'] !!}</p>
                    <p class="text-xs text-ink-tertiary mt-0.5">{{ $act['time'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

@push('scripts')
<script>
// Animate skill bar fills on load
document.addEventListener('DOMContentLoaded', () => {
    requestAnimationFrame(() => {
        document.querySelectorAll('.skill-bar-fill').forEach(el => {
            const w = el.style.width;
            el.style.width = '0';
            setTimeout(() => { el.style.width = w; }, 300);
        });
    });
});
</script>
@endpush

@endsection
