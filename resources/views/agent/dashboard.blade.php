@extends('layouts.dashboard')

@section('title', 'AI Agent')
@section('page-title', 'Autonomous Agent')
@section('page-description', 'Your AI-powered job application assistant')

@push('styles')
<style>
/* ── Keyframes ─────────────────────────────────────── */
@keyframes agent-orb1    { 0%,100%{transform:translate(0,0) scale(1)}   50%{transform:translate(20px,-25px) scale(1.1)} }
@keyframes agent-orb2    { 0%,100%{transform:translate(0,0) scale(1)}   50%{transform:translate(-18px,22px) scale(.92)} }
@keyframes agent-orb3    { 0%,100%{transform:translate(0,0)}            33%{transform:translate(15px,8px)}  66%{transform:translate(-8px,-15px)} }
@keyframes agent-float   { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-10px) rotate(6deg)} }
@keyframes agent-pulse   { 0%,100%{box-shadow:0 0 0 0 rgba(139,92,246,.55)} 70%{box-shadow:0 0 0 18px rgba(139,92,246,0)} }
@keyframes agent-shimmer { to{background-position:-200% center} }
@keyframes agent-grad    { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
@keyframes agent-ring    { 0%{transform:scale(.8);opacity:.8} 100%{transform:scale(2.2);opacity:0} }
@keyframes agent-fadeUp  { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
@keyframes agent-glow    { 0%,100%{filter:drop-shadow(0 0 8px rgba(139,92,246,.6))} 50%{filter:drop-shadow(0 0 22px rgba(99,102,241,.9))} }
@keyframes agent-spin    { to{transform:rotate(360deg)} }
@keyframes card-enter    { from{opacity:0;transform:scale(.92) translateY(24px)} to{opacity:1;transform:scale(1) translateY(0)} }

/* ── Card wrapper ──────────────────────────────────── */
.agent-card-wrap {
    animation: card-enter .7s cubic-bezier(.34,1.56,.64,1) both;
}

/* ── Animated gradient border ──────────────────────── */
.agent-card {
    position: relative;
    border-radius: 2rem;
    padding: 2px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899, #3b82f6, #6366f1);
    background-size: 300% 300%;
    animation: agent-grad 4s ease infinite;
    box-shadow:
        0 0 0 1px rgba(139,92,246,.3),
        0 8px 32px rgba(99,102,241,.25),
        0 32px 80px rgba(139,92,246,.2),
        0 0 120px rgba(99,102,241,.1);
}
.agent-card-inner {
    border-radius: calc(2rem - 2px);
    background: linear-gradient(160deg, #0f0c29, #302b63, #24243e);
    position: relative;
    overflow: hidden;
    padding: 3.5rem 3rem;
}

/* ── Background orbs ───────────────────────────────── */
.agent-orb { position:absolute; border-radius:50%; filter:blur(64px); pointer-events:none; }
.agent-orb-1 { width:320px;height:320px; background:radial-gradient(circle,rgba(99,102,241,.45),transparent 70%); top:-80px; left:-60px; animation:agent-orb1 10s ease-in-out infinite; }
.agent-orb-2 { width:260px;height:260px; background:radial-gradient(circle,rgba(168,85,247,.4),transparent 70%);  bottom:-60px;right:-40px; animation:agent-orb2 12s ease-in-out infinite; }
.agent-orb-3 { width:180px;height:180px; background:radial-gradient(circle,rgba(236,72,153,.3),transparent 70%);  top:40%;left:55%;  animation:agent-orb3 8s ease-in-out infinite; }

/* ── Central icon ──────────────────────────────────── */
.agent-icon-wrap {
    position: relative;
    width: 96px; height: 96px;
    margin: 0 auto 2rem;
}
.agent-icon-ring {
    position: absolute; inset: -8px;
    border-radius: 50%;
    border: 2px solid rgba(139,92,246,.6);
    animation: agent-ring 2s ease-out infinite;
}
.agent-icon-ring-2 {
    position: absolute; inset: -8px;
    border-radius: 50%;
    border: 2px solid rgba(99,102,241,.5);
    animation: agent-ring 2s ease-out .7s infinite;
}
.agent-icon-btn {
    width: 96px; height: 96px;
    border-radius: 28px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed, #a855f7);
    background-size: 200%;
    animation: agent-grad 3s ease infinite, agent-pulse 2.5s ease-in-out infinite, agent-float 4s ease-in-out infinite;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 8px 32px rgba(99,102,241,.5), 0 0 0 0 rgba(139,92,246,.4);
    position: relative; z-index: 1;
}
.agent-icon-btn svg { animation: agent-glow 3s ease-in-out infinite; }

/* ── Text ──────────────────────────────────────────── */
.agent-title {
    background: linear-gradient(135deg, #e0e7ff, #c4b5fd, #f9a8d4, #93c5fd);
    background-size: 300%;
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: agent-grad 5s ease infinite;
}

/* ── Feature mini-cards ────────────────────────────── */
.agent-feat {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 1.25rem;
    padding: 1.25rem 1rem;
    transition: transform .3s cubic-bezier(.22,1,.36,1), background .3s, border-color .3s, box-shadow .3s;
    animation: agent-fadeUp .6s cubic-bezier(.22,1,.36,1) both;
    backdrop-filter: blur(8px);
    cursor: default;
}
.agent-feat:hover {
    transform: translateY(-6px) scale(1.03);
    background: rgba(255,255,255,.12);
    border-color: rgba(139,92,246,.5);
    box-shadow: 0 12px 40px rgba(139,92,246,.25), 0 0 24px rgba(99,102,241,.15);
}
.agent-feat:nth-child(1) { animation-delay: .25s; }
.agent-feat:nth-child(2) { animation-delay: .38s; }
.agent-feat:nth-child(3) { animation-delay: .51s; }

.agent-feat-icon {
    width: 44px; height: 44px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 0.75rem;
    transition: transform .3s, box-shadow .3s;
}
.agent-feat:hover .agent-feat-icon {
    transform: scale(1.15) rotate(-5deg);
    box-shadow: 0 0 20px rgba(139,92,246,.5);
}

/* ── CTA button ────────────────────────────────────── */
.agent-cta {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .85rem 2.5rem;
    border-radius: 999px;
    font-weight: 700; font-size: .95rem;
    color: #fff;
    background: linear-gradient(135deg, #4f46e5, #7c3aed, #a855f7, #4f46e5);
    background-size: 300% auto;
    animation: agent-shimmer 2.5s linear infinite;
    box-shadow: 0 4px 20px rgba(99,102,241,.5), 0 0 40px rgba(139,92,246,.2);
    transition: transform .25s cubic-bezier(.22,1,.36,1), box-shadow .25s;
    text-decoration: none;
    border: none;
}
.agent-cta:hover {
    transform: translateY(-3px) scale(1.04);
    box-shadow: 0 12px 40px rgba(99,102,241,.65), 0 0 60px rgba(139,92,246,.35);
}
.agent-cta svg { animation: agent-spin 4s linear infinite; }

/* ── Floating particles ────────────────────────────── */
@keyframes particle { 0%{opacity:1;transform:translate(0,0) scale(1)} 100%{opacity:0;transform:translate(var(--tx),var(--ty)) scale(0)} }
.agent-particle { position:absolute; border-radius:50%; pointer-events:none; animation:particle var(--dur,4s) ease-out var(--del,0s) infinite; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    @if(!$configured)
        {{-- Not Configured - Onboarding State --}}
        <div class="flex items-center justify-center min-h-[60vh]">
            <div class="agent-card-wrap max-w-2xl w-full">
                <div class="agent-card">
                    <div class="agent-card-inner">
                        {{-- Background orbs --}}
                        <div class="agent-orb agent-orb-1"></div>
                        <div class="agent-orb agent-orb-2"></div>
                        <div class="agent-orb agent-orb-3"></div>

                        {{-- Floating particles --}}
                        <div class="agent-particle" style="width:5px;height:5px;background:#818cf8;top:15%;left:10%;--tx:-30px;--ty:-50px;--dur:5s;--del:0s"></div>
                        <div class="agent-particle" style="width:4px;height:4px;background:#c084fc;top:70%;left:15%;--tx:20px;--ty:-60px;--dur:6s;--del:1s"></div>
                        <div class="agent-particle" style="width:6px;height:6px;background:#f472b6;top:20%;right:12%;--tx:30px;--ty:40px;--dur:4.5s;--del:.5s"></div>
                        <div class="agent-particle" style="width:4px;height:4px;background:#60a5fa;bottom:20%;right:10%;--tx:-25px;--ty:-45px;--dur:5.5s;--del:2s"></div>
                        <div class="agent-particle" style="width:3px;height:3px;background:#34d399;top:50%;left:8%;--tx:40px;--ty:30px;--dur:7s;--del:1.5s"></div>

                        {{-- Central icon --}}
                        <div class="agent-icon-wrap" style="animation:agent-fadeUp .5s ease both">
                            <div class="agent-icon-ring"></div>
                            <div class="agent-icon-ring-2"></div>
                            <div class="agent-icon-btn">
                                <svg class="w-11 h-11 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Heading --}}
                        <div class="text-center mb-3 relative z-10" style="animation:agent-fadeUp .55s ease .1s both">
                            <h2 class="agent-title text-3xl font-extrabold mb-3 leading-tight">Configure Your AI Agent</h2>
                            <p class="text-base max-w-md mx-auto leading-relaxed" style="color:rgba(199,210,254,.75)">
                                Set up your autonomous assistant to discover, analyze, and apply to<br>jobs that match your preferences — <span style="color:#c4b5fd;font-weight:600">24/7.</span>
                            </p>
                        </div>

                        {{-- Feature cards --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 relative z-10 mt-8">
                            <div class="agent-feat text-center">
                                <div class="agent-feat-icon" style="background:linear-gradient(135deg,rgba(99,102,241,.3),rgba(79,70,229,.5));border:1px solid rgba(99,102,241,.4)">
                                    <svg class="w-5 h-5" style="color:#a5b4fc" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-sm mb-1.5" style="color:#e0e7ff">Auto Discovery</h4>
                                <p class="text-xs leading-relaxed" style="color:rgba(199,210,254,.6)">Scans job boards hourly</p>
                            </div>
                            <div class="agent-feat text-center" style="border-color:rgba(168,85,247,.3);background:rgba(168,85,247,.08)">
                                <div class="agent-feat-icon" style="background:linear-gradient(135deg,rgba(168,85,247,.3),rgba(139,92,246,.5));border:1px solid rgba(168,85,247,.4)">
                                    <svg class="w-5 h-5" style="color:#d8b4fe" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-sm mb-1.5" style="color:#e0e7ff">AI Analysis</h4>
                                <p class="text-xs leading-relaxed" style="color:rgba(199,210,254,.6)">Smart job matching</p>
                            </div>
                            <div class="agent-feat text-center" style="border-color:rgba(236,72,153,.3);background:rgba(236,72,153,.07)">
                                <div class="agent-feat-icon" style="background:linear-gradient(135deg,rgba(236,72,153,.3),rgba(219,39,119,.4));border:1px solid rgba(236,72,153,.4)">
                                    <svg class="w-5 h-5" style="color:#f9a8d4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-sm mb-1.5" style="color:#e0e7ff">Auto Apply</h4>
                                <p class="text-xs leading-relaxed" style="color:rgba(199,210,254,.6)">Submits applications 24/7</p>
                            </div>
                        </div>

                        {{-- CTA --}}
                        <div class="text-center relative z-10" style="animation:agent-fadeUp .6s ease .5s both">
                            <a href="{{ route('agent.configure') }}" class="agent-cta">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Get Started
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Agent Header with Status --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-14 h-14 bg-gradient-to-br from-studai-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @if($config->is_active && !$config->is_paused)
                        <span class="absolute -top-1 -right-1 flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-green-500 border-2 border-white dark:border-gray-900"></span>
                        </span>
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">AI Agent</h1>
                    <div class="flex items-center gap-2 mt-1">
                        @if($config->is_active && !$config->is_paused)
                            <x-studai.badge color="green" dot>Active</x-studai.badge>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Running since {{ $config->activated_at?->format('M d') ?? 'recently' }}</span>
                        @elseif($config->is_paused)
                            <x-studai.badge color="amber" dot>Paused</x-studai.badge>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Temporarily suspended</span>
                        @else
                            <x-studai.badge color="gray">Inactive</x-studai.badge>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Not running</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if(!$config->is_active)
                    <form action="{{ route('agent.activate') }}" method="POST">
                        @csrf
                        <x-studai.button type="submit" variant="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Activate
                        </x-studai.button>
                    </form>
                @elseif($config->is_paused)
                    <form action="{{ route('agent.resume') }}" method="POST">
                        @csrf
                        <x-studai.button type="submit" variant="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            </svg>
                            Resume
                        </x-studai.button>
                    </form>
                @else
                    <form action="{{ route('agent.pause') }}" method="POST">
                        @csrf
                        <x-studai.button type="submit" variant="secondary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pause
                        </x-studai.button>
                    </form>
                @endif
                <x-studai.button href="{{ route('agent.configure') }}" variant="ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </x-studai.button>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-studai.stat-card 
                title="Jobs Analyzed" 
                value="{{ $statistics['total_analyzed'] ?? 0 }}" 
                change="{{ $statistics['today_applications'] ?? 0 }} today"
                icon="heroicon-o-magnifying-glass"
                iconColor="blue"
            />
            <x-studai.stat-card 
                title="Applications Sent" 
                value="{{ $statistics['total_applications'] }}" 
                change="{{ $statistics['today_applications'] }} today"
                icon="heroicon-o-paper-airplane"
                iconColor="green"
            />
            <x-studai.stat-card 
                title="Success Rate" 
                value="{{ $statistics['success_rate'] }}" 
                suffix="%" 
                change="{{ $statistics['successful_applications'] }} interviews"
                icon="heroicon-o-check-circle"
                iconColor="purple"
            />
            <x-studai.stat-card 
                title="Daily Limit" 
                value="{{ $limits['daily_remaining'] }}/{{ $limits['daily_limit'] }}" 
                change="Remaining today"
                icon="heroicon-o-clock"
                iconColor="yellow"
            />
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Job Queue (2 columns) --}}
            <div class="lg:col-span-2">
                <x-studai.card>
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Application Queue</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Jobs pending application</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-studai.chip size="sm" color="amber">{{ $statistics['pending_applications'] ?? 8 }} pending</x-studai.chip>
                            <a href="{{ route('agent.applications') }}" class="text-sm font-medium text-studai-blue-600 hover:text-studai-blue-700">
                                View all →
                            </a>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentApplications->take(5) as $application)
                            <div class="group flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-studai-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($application->job?->company_name ?? '?', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-medium text-gray-900 dark:text-white truncate">{{ $application->job?->title ?? 'Unknown Position' }}</h4>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $application->job?->company_name ?? 'Unknown Company' }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($application->status === 'submitted')
                                        <x-studai.badge color="blue" size="sm">Submitted</x-studai.badge>
                                    @elseif($application->status === 'pending')
                                        <x-studai.badge color="amber" size="sm">Pending</x-studai.badge>
                                    @elseif($application->status === 'pending_approval')
                                        <x-studai.badge color="purple" size="sm">Needs Review</x-studai.badge>
                                    @elseif($application->status === 'failed')
                                        <x-studai.badge color="red" size="sm">Failed</x-studai.badge>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $application->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">No applications yet</p>
                                <p class="text-sm text-gray-400 mt-1">Your agent will start applying once activated</p>
                            </div>
                        @endforelse
                    </div>
                </x-studai.card>
            </div>

            {{-- Right Sidebar --}}
            <div class="space-y-6">
                {{-- AI Activity Feed --}}
                <x-studai.card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white">AI Activity</h3>
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentApplications->take(4) as $recentApp)
                            <div class="flex gap-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-900 dark:text-white">Applied to {{ $recentApp->job?->company_name ?? 'a company' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $recentApp->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">No activity yet</p>
                                <p class="text-xs text-gray-400 mt-1">Activate your agent to start applying</p>
                            </div>
                        @endforelse
                    </div>
                </x-studai.card>

                {{-- Daily Insights --}}
                <x-studai.card>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Today's Insights</h3>
                    <div class="space-y-4">
                        <div class="p-3 bg-studai-blue-50 dark:bg-studai-blue-900/20 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-studai-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                <span class="text-sm font-medium text-studai-blue-700 dark:text-studai-blue-300">Trending Skill</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">"Kubernetes" appears in 73% of your matched jobs</p>
                        </div>
                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium text-green-700 dark:text-green-300">Best Performance</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Remote positions have 2x higher response rate</p>
                        </div>
                    </div>
                </x-studai.card>

                {{-- Quick Actions --}}
                <x-studai.card>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('agent.applications') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                            <div class="w-9 h-9 bg-studai-blue-100 dark:bg-studai-blue-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-studai-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">View All Applications</span>
                        </a>
                        <a href="{{ route('agent.metrics') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                            <div class="w-9 h-9 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Performance Metrics</span>
                        </a>
                        @if($config->enable_learning)
                            <a href="{{ route('agent.learning') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                                <div class="w-9 h-9 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">AI Learning Insights</span>
                            </a>
                        @endif
                    </div>
                </x-studai.card>
            </div>
        </div>

        {{-- ── INTERNAL PLATFORM JOB MATCHES ─────────────────────────────── --}}
        <div class="mt-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg" style="background:#e8f0fe;">
                            <svg class="w-4 h-4" style="color:#1a73e8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </span>
                        Platform Job Matches
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Jobs on this platform that match your profile — your agent can apply to these for you.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex gap-2 text-xs">
                        @if($internalStats['pending'] > 0)
                        <span class="px-2 py-1 rounded-full font-medium" style="background:#fff8e1;color:#f57f17;">{{ $internalStats['pending'] }} pending</span>
                        @endif
                        @if($internalStats['applied'] > 0)
                        <span class="px-2 py-1 rounded-full font-medium" style="background:#e8f5e9;color:#2e7d32;">{{ $internalStats['applied'] }} applied</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('agent.internal.rescore') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors" style="background:#f1f3f4;color:#3c4043;border:1px solid #dadce0;" title="Recalculate match scores for all existing matches">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Rescore
                            </button>
                        </form>
                        <form method="POST" action="{{ route('agent.internal.scan') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors" style="background:#1a73e8;color:#fff;" title="Scan platform jobs now">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Scan Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if($internalMatches->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-10 text-center">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#e8f0fe;">
                        <svg class="w-7 h-7" style="color:#1a73e8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No matches yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Click <strong>Scan Now</strong> to find platform jobs that match your agent configuration.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($internalMatches as $match)
                    @php
                        $scoreColor = match(true) {
                            $match->match_score >= 80 => ['bg'=>'#e8f5e9','text'=>'#2e7d32'],
                            $match->match_score >= 60 => ['bg'=>'#e8f0fe','text'=>'#1a73e8'],
                            $match->match_score >= 40 => ['bg'=>'#fff8e1','text'=>'#f57f17'],
                            default                   => ['bg'=>'#fce4ec','text'=>'#c62828'],
                        };
                        $statusBg = match($match->status) {
                            'applied'  => 'background:#e8f5e9;color:#2e7d32;',
                            'skipped'  => 'background:#f5f5f5;color:#757575;',
                            default    => 'background:#fff8e1;color:#f57f17;',
                        };
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 flex flex-col gap-3">
                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 dark:text-white text-sm leading-snug truncate">{{ $match->job->title }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $match->job->company?->name ?? 'Company' }}</p>
                            </div>
                            <div class="flex-shrink-0 text-center px-2.5 py-1 rounded-xl text-sm font-bold" style="background:{{ $scoreColor['bg'] }};color:{{ $scoreColor['text'] }};">
                                {{ $match->match_score }}%
                            </div>
                        </div>

                        {{-- Tags --}}
                        <div class="flex flex-wrap gap-1.5">
                            @if($match->job->location)
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:#f5f5f5;color:#424242;">
                                📍 {{ Str::limit($match->job->location, 20) }}
                            </span>
                            @endif
                            @if($match->job->salary_min || $match->job->salary_max)
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:#f5f5f5;color:#424242;">
                                💰 {{ $match->job->salary_currency ?? '₹' }}{{ number_format((float)($match->job->salary_min ?? 0) / 1000) }}k–{{ number_format((float)($match->job->salary_max ?? 0) / 1000) }}k
                            </span>
                            @endif
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="{{ $statusBg }}">
                                {{ $match->status_label }}
                            </span>
                        </div>

                        {{-- AI reasoning --}}
                        @if($match->ai_reasoning)
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ $match->ai_reasoning }}</p>
                        @endif

                        {{-- Score breakdown mini bars --}}
                        @if($match->score_breakdown)
                        <div class="space-y-1">
                            @foreach(['role' => 'Role', 'skills' => 'Skills', 'salary' => 'Salary', 'location' => 'Location'] as $key => $label)
                            @if(isset($match->score_breakdown[$key]))
                            @php
                                $maxes = ['role' => 35, 'skills' => 30, 'salary' => 15, 'location' => 12];
                                $pct = round(($match->score_breakdown[$key] / $maxes[$key]) * 100);
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400 w-12 flex-shrink-0">{{ $label }}</span>
                                <div class="flex-1 h-1.5 rounded-full" style="background:#f0f0f0;">
                                    <div class="h-full rounded-full" style="width:{{ $pct }}%;background:#1a73e8;"></div>
                                </div>
                                <span class="text-xs text-gray-400 w-6 text-right">{{ $pct }}%</span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @endif

                        {{-- Actions --}}
                        @if($match->status === 'pending')
                        <div class="flex gap-2 mt-auto pt-1">
                            <form method="POST" action="{{ route('agent.internal.approve', $match) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full py-2 text-xs font-semibold rounded-xl transition-colors" style="background:#1a73e8;color:#fff;">
                                    ✓ Apply Now
                                </button>
                            </form>
                            <form method="POST" action="{{ route('agent.internal.skip', $match) }}">
                                @csrf
                                <button type="submit" class="px-3 py-2 text-xs font-semibold rounded-xl transition-colors" style="background:#f5f5f5;color:#424242;">
                                    Skip
                                </button>
                            </form>
                        </div>
                        @elseif($match->status === 'applied')
                        <div class="mt-auto pt-1">
                            <a href="{{ route('jobs.show', $match->job_id) }}" class="block text-center py-2 text-xs font-semibold rounded-xl" style="background:#e8f5e9;color:#2e7d32;">
                                ✓ Applied — View Job
                            </a>
                        </div>
                        @else
                        <div class="mt-auto pt-1">
                            <span class="block text-center py-2 text-xs font-medium rounded-xl" style="background:#f5f5f5;color:#9e9e9e;">Skipped</span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
        {{-- ── END INTERNAL MATCHES ────────────────────────────────────────── --}}

    @endif
</div>
@endsection
