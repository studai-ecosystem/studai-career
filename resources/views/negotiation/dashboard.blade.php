@extends('layouts.dashboard')

@section('title', 'AI Negotiation Strategist')
@section('page-title', 'Negotiation Strategist')
@section('page-description', 'Transform job offers into competitive packages')

@section('content')
<div class="space-y-6">

    {{-- HERO --}}
    <div class="relative overflow-hidden rounded-2xl p-6 text-white" style="background:linear-gradient(135deg,#4c1d95 0%,#6d28d9 40%,#a855f7 80%,#ec4899 100%);">
        <div class="absolute inset-0" style="background-image:radial-gradient(circle at 80% 50%,rgba(255,255,255,.15) 0%,transparent 60%);"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:rgba(255,255,255,.2);color:#fff;">AI STRATEGIST</span>
                <h1 class="text-2xl font-bold mt-2 text-white">AI Negotiation Strategist</h1>
                <p class="text-sm mt-1" style="color:rgba(255,255,255,.85);">Transform job offers into competitive compensation packages.</p>
            </div>
            <button onclick="openNewStrategyModal()" class="inline-flex items-center gap-2 px-5 py-2.5 font-semibold rounded-xl transition-all shadow-sm text-sm flex-shrink-0" style="background:#fff;color:#6d28d9;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Strategy
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl shadow-sm p-5" style="background:linear-gradient(135deg,#fdf4ff,#ede9fe);border:1.5px solid #c084fc">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:rgba(168,85,247,.18);">
                <svg class="w-5 h-5" style="color:#7c3aed;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="text-3xl font-bold" style="color:#4c1d95" id="active-strategies-count">{{ $activeStrategies }}</div>
            <div class="text-sm mt-1" style="color:#7c3aed">Active Strategies</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#dcfce7;">
                <svg class="w-5 h-5" style="color:#16a34a;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $completedStrategiesCount }}</div>
            <div class="text-sm text-gray-600 mt-1">Completed</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#e3f2fd;">
                <svg class="w-5 h-5" style="color:#1976d2;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $totalValueGained ?? '0' }}</div>
            <div class="text-sm text-gray-600 mt-1">Value Gained</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:#fce4ec;">
                <svg class="w-5 h-5" style="color:#c62828;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $successRate ?? '0' }}<span class="text-lg text-gray-500 font-semibold">%</span></div>
            <div class="text-sm text-gray-600 mt-1">Success Rate</div>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">

            {{-- Strategies list --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Active Strategies</h2>
                    <button onclick="openNewStrategyModal()" class="px-3 py-1.5 text-white text-xs font-semibold rounded-xl transition-colors" style="background:linear-gradient(135deg,#7c3aed,#a855f7);" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">+ New</button>
                </div>
                @forelse($strategies ?? [] as $strategy)
                <div class="mb-3 p-4 rounded-xl transition-colors" style="background:#fdf4ff;border:1px solid #e9d5ff;" onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='#fdf4ff'">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $strategy->company_name }}</h3>
                            <p class="text-xs text-gray-500">{{ $strategy->position_title }} &middot; {{ $strategy->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="flex-shrink-0 px-2 py-0.5 text-xs font-semibold rounded-full
                            @if($strategy->status === 'completed') bg-green-100 text-green-700
                            @elseif($strategy->status === 'active') bg-violet-100 text-violet-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($strategy->status) }}
                        </span>
                    </div>
                    <div class="mt-2 flex items-center gap-3 text-xs text-gray-500">
                        <span>Offer: <strong class="text-gray-900">{{ $strategy->current_offer ?? 'N/A' }}</strong></span>
                        @if($strategy->target_salary)<span>Target: <strong style="color:#16a34a;">{{ $strategy->target_salary }}</strong></span>@endif
                    </div>
                    <div class="mt-3 flex gap-2">
                        <a href="{{ route('negotiation.strategy', $strategy) }}" class="px-3 py-1 text-white text-xs font-medium rounded-lg transition-colors" style="background:linear-gradient(135deg,#7c3aed,#a855f7);" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">View Strategy</a>
                        <a href="{{ route('negotiation.coaching', $strategy) }}" class="px-3 py-1 text-gray-600 text-xs font-medium rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">AI Coaching</a>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
                        <svg class="w-6 h-6" style="color:#8b5cf6;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-900">No strategies yet</p>
                    <p class="text-xs text-gray-500 mt-1 mb-4">Create your first negotiation strategy to get started</p>
                    <button onclick="openNewStrategyModal()" class="px-4 py-2 text-white text-sm font-semibold rounded-xl transition-colors" style="background:linear-gradient(135deg,#7c3aed,#a855f7);">Create Strategy</button>
                </div>
                @endforelse
            </div>

            {{-- Quick Tools --}}
            @php
                $firstStrategy = ($strategies instanceof \Illuminate\Pagination\LengthAwarePaginator ? $strategies->getCollection() : collect($strategies))->first();
                $scenariosHref = $firstStrategy ? route('negotiation.scenarios', $firstStrategy->id) : '#';
                $scriptsHref   = $firstStrategy ? route('negotiation.scripts',   $firstStrategy->id) : '#';
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <a href="{{ $scenariosHref }}" class="bg-white rounded-2xl border border-gray-200 p-5 text-center transition-all" onmouseover="this.style.background='#fdf4ff'" onmouseout="this.style.background='#fff'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-2" style="background:#fef3c7;"><span class="text-xl">&#x1F3AF;</span></div>
                    <div class="text-sm font-semibold text-gray-900">Practice Scenarios</div>
                    <div class="text-xs text-gray-500 mt-0.5">Simulate negotiations</div>
                </a>
                <a href="{{ $scriptsHref }}" class="bg-white rounded-2xl border border-gray-200 p-5 text-center transition-all" onmouseover="this.style.background='#e3f2fd'" onmouseout="this.style.background='#fff'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-2" style="background:#e3f2fd;"><span class="text-xl">&#x1F4DD;</span></div>
                    <div class="text-sm font-semibold text-gray-900">Negotiation Scripts</div>
                    <div class="text-xs text-gray-500 mt-0.5">Email &amp; call templates</div>
                </a>
                <a href="{{ route('negotiation.coaching.active') }}" class="bg-white rounded-2xl border border-gray-200 p-5 text-center transition-all" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#fff'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-2" style="background:#dcfce7;"><span class="text-xl">&#x1F4AC;</span></div>
                    <div class="text-sm font-semibold text-gray-900">Coaching Sessions</div>
                    <div class="text-xs text-gray-500 mt-0.5">1:1 AI coaching</div>
                </a>
                <a href="{{ route('negotiation.chatbot') }}" class="rounded-2xl p-5 text-center transition-all" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);border:1.5px solid #c084fc;" onmouseover="this.style.background='linear-gradient(135deg,#ede9fe,#ddd6fe)'" onmouseout="this.style.background='linear-gradient(135deg,#f5f3ff,#ede9fe)'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-2" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);"><span class="text-xl text-white">&#x20B9;</span></div>
                    <div class="text-sm font-semibold" style="color:#4c1d95;">AI Salary Coach</div>
                    <div class="text-xs mt-0.5" style="color:#7c3aed;">Chat &amp; get advice</div>
                </a>
            </div>
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-5">

            {{-- ═══ NEGOTIATION AGENT TOGGLE PANEL ═══ --}}
            <div x-data="{ open: true, loading: false, question: '', messages: [] }" class="rounded-2xl overflow-hidden shadow-sm" style="border:1.5px solid #7c3aed;">

                {{-- Toggle header --}}
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3.5 transition-colors"
                        style="background:linear-gradient(135deg,#4c1d95,#7c3aed);">
                    <div class="flex items-center gap-2.5">
                        {{-- Animated pulse dot --}}
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background:#86efac;"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5" style="background:#4ade80;"></span>
                        </span>
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm font-bold text-white" style="background:rgba(255,255,255,.2);">₹</div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-white leading-none">Negotiation Agent</p>
                            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,.7);">AI-powered salary advisor</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-white transition-transform duration-200"
                         :class="open ? 'rotate-180' : ''"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Collapsible body --}}
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="bg-white">

                    {{-- Mini chat history --}}
                    <div class="px-4 pt-3 pb-2 space-y-2 max-h-52 overflow-y-auto" id="agent-mini-chat"
                         style="min-height:60px;">
                        {{-- Default greeting --}}
                        <template x-if="messages.length === 0">
                            <div class="flex gap-2 items-start">
                                <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold text-white mt-0.5" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">₹</div>
                                <div class="text-xs text-gray-700 rounded-xl rounded-tl-none px-3 py-2 leading-relaxed" style="background:#f5f3ff;border:1px solid #ede9fe;">
                                    Hi! I'm your AI Negotiation Agent. Ask me anything about your offer, salary ranges, or negotiation tactics. 💜
                                </div>
                            </div>
                        </template>
                        {{-- Dynamic messages --}}
                        <template x-for="(msg, idx) in messages" :key="idx">
                            <div>
                                <div x-show="msg.role === 'user'" class="flex justify-end">
                                    <div class="text-xs text-white rounded-xl rounded-tr-none px-3 py-2 max-w-[85%] leading-relaxed" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);" x-text="msg.content"></div>
                                </div>
                                <div x-show="msg.role === 'agent'" class="flex gap-2 items-start">
                                    <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold text-white mt-0.5" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">₹</div>
                                    <div class="text-xs text-gray-700 rounded-xl rounded-tl-none px-3 py-2 leading-relaxed max-w-[85%]" style="background:#f5f3ff;border:1px solid #ede9fe;" x-text="msg.content"></div>
                                </div>
                            </div>
                        </template>
                        {{-- Loading dots --}}
                        <div x-show="loading" class="flex gap-2 items-start">
                            <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold text-white" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">₹</div>
                            <div class="px-3 py-2 rounded-xl rounded-tl-none" style="background:#f5f3ff;border:1px solid #ede9fe;">
                                <span class="flex gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background:#7c3aed;animation:chatDot .9s infinite ease-in-out;animation-delay:0s"></span>
                                    <span class="w-1.5 h-1.5 rounded-full" style="background:#7c3aed;animation:chatDot .9s infinite ease-in-out;animation-delay:.2s"></span>
                                    <span class="w-1.5 h-1.5 rounded-full" style="background:#7c3aed;animation:chatDot .9s infinite ease-in-out;animation-delay:.4s"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Quick prompts --}}
                    <div class="px-4 pb-3 flex flex-wrap gap-1.5">
                        <button @click="question = 'How do I counter a low offer?'; $nextTick(() => sendAgentMessage())"
                                class="text-xs px-2.5 py-1 rounded-full border transition-colors hover:text-white"
                                style="border-color:#c4b5fd;color:#7c3aed;"
                                onmouseover="this.style.background='#7c3aed';this.style.color='#fff'" onmouseout="this.style.background='';this.style.color='#7c3aed'">
                            Counter low offer
                        </button>
                        <button @click="question = 'What is a good salary for my role?'; $nextTick(() => sendAgentMessage())"
                                class="text-xs px-2.5 py-1 rounded-full border transition-colors"
                                style="border-color:#c4b5fd;color:#7c3aed;"
                                onmouseover="this.style.background='#7c3aed';this.style.color='#fff'" onmouseout="this.style.background='';this.style.color='#7c3aed'">
                            Market salary?
                        </button>
                        <button @click="question = 'Give me a negotiation script'; $nextTick(() => sendAgentMessage())"
                                class="text-xs px-2.5 py-1 rounded-full border transition-colors"
                                style="border-color:#c4b5fd;color:#7c3aed;"
                                onmouseover="this.style.background='#7c3aed';this.style.color='#fff'" onmouseout="this.style.background='';this.style.color='#7c3aed'">
                            Get script
                        </button>
                    </div>

                    {{-- Input bar --}}
                    <div class="px-3 pb-3">
                        <form @submit.prevent="sendAgentMessage()" class="flex gap-2">
                            <input x-model="question"
                                   type="text"
                                   placeholder="Ask the agent..."
                                   class="flex-1 text-xs px-3 py-2 rounded-xl border bg-gray-50 focus:outline-none focus:border-violet-400 focus:ring-1 focus:ring-violet-200"
                                   style="border-color:#ddd6fe;"
                                   :disabled="loading">
                            <button type="submit"
                                    :disabled="loading || !question.trim()"
                                    class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-opacity disabled:opacity-40"
                                    style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    {{-- Full agent link --}}
                    <div class="px-3 pb-4">
                        <a href="{{ route('negotiation.chatbot') }}"
                           class="w-full flex items-center justify-center gap-1.5 py-2 text-xs font-semibold rounded-xl transition-colors"
                           style="background:linear-gradient(135deg,#4c1d95,#7c3aed);color:#fff;">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Open Full Agent
                        </a>
                    </div>
                </div>
            </div>
            {{-- ═══ END NEGOTIATION AGENT TOGGLE PANEL ═══ --}}

            <div class="rounded-2xl p-5 text-white" style="background:linear-gradient(135deg,#4c1d95,#7c3aed,#a855f7);">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(255,255,255,.2);">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div class="text-sm font-semibold text-white">AI Tips</div>
                </div>
                <div class="space-y-2.5 text-sm">
                    <div class="rounded-xl p-3 text-white" style="background:rgba(255,255,255,.15);">&#x1F4A1; <span class="font-medium">Research first</span> &mdash; Know the market rate before any conversation.</div>
                    <div class="rounded-xl p-3 text-white" style="background:rgba(255,255,255,.15);">&#x1F3AF; <span class="font-medium">Anchor high</span> &mdash; State your number first to set the frame.</div>
                    <div class="rounded-xl p-3 text-white" style="background:rgba(255,255,255,.15);">&#x1F4CB; <span class="font-medium">Total package</span> &mdash; Negotiate equity, PTO &amp; remote too.</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Market Benchmarks</h3>
                @foreach($marketData ?? [] as $role => $data)
                <div class="mb-3 last:mb-0">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-gray-900">{{ $role }}</span>
                        <span class="font-semibold" style="color:#16a34a;">{{ $data }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full"><div class="h-full rounded-full" style="width:70%;background:linear-gradient(90deg,#7c3aed,#a855f7);"></div></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- NEW STRATEGY MODAL --}}
    <div id="new-strategy-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-xl mx-4 max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-900">New Negotiation Strategy</h2>
                <button onclick="closeNewStrategyModal()" class="text-gray-400 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="new-strategy-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Job Title <span class="text-red-500">*</span></label>
                        <input type="text" name="role" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:border-violet-400" placeholder="e.g. Senior Engineer">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="company_name" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:border-violet-400" placeholder="e.g. Google">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Location <span class="text-red-500">*</span></label>
                        <input type="text" name="location" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:border-violet-400" placeholder="e.g. Bangalore">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Years of Experience <span class="text-red-500">*</span></label>
                        <input type="number" name="experience_years" required min="0" max="50" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:border-violet-400" placeholder="e.g. 5">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Offered Salary (LPA) <span class="text-red-500">*</span></label>
                        <input type="number" name="offered_salary" required min="0" step="0.1" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:border-violet-400" placeholder="e.g. 20">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Current Salary (LPA)</label>
                        <input type="number" name="current_salary" min="0" step="0.1" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:border-violet-400" placeholder="e.g. 15 (optional)">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Education Level</label>
                        <select name="education_level" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:border-violet-400">
                            <option value="">Select (optional)</option>
                            <option value="high_school">High School</option>
                            <option value="associate">Associate</option>
                            <option value="bachelor">Bachelor's</option>
                            <option value="master">Master's</option>
                            <option value="mba">MBA</option>
                            <option value="phd">PhD</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 justify-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_currently_employed" class="rounded border-gray-300 text-violet-500" value="1">
                            <span class="text-sm text-gray-700">Currently employed</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="has_other_offers" class="rounded border-gray-300 text-violet-500" value="1">
                            <span class="text-sm text-gray-700">Have other offers</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Key Skills</label>
                    <input type="text" name="skills" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:border-violet-400" placeholder="e.g. Python, AWS, React (comma-separated)">
                </div>
                <div id="strategy-error" class="hidden p-3 rounded-xl text-sm text-red-700 bg-red-50 border border-red-200"></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeNewStrategyModal()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" id="submit-strategy-btn" class="flex-1 px-4 py-2.5 text-white text-sm font-semibold rounded-xl transition-colors flex items-center justify-center gap-2" style="background:linear-gradient(135deg,#7c3aed,#a855f7);" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <svg id="loading-spinner" class="hidden animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        <span id="submit-strategy-text">Generate Strategy</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openNewStrategyModal() {
    const modal = document.getElementById('new-strategy-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeNewStrategyModal() {
    const modal = document.getElementById('new-strategy-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('new-strategy-form').reset();
    document.getElementById('strategy-error').classList.add('hidden');
}

document.getElementById('new-strategy-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('submit-strategy-btn');
    const spinner = document.getElementById('loading-spinner');
    const text = document.getElementById('submit-strategy-text');
    const errorBox = document.getElementById('strategy-error');

    submitBtn.disabled = true;
    spinner.classList.remove('hidden');
    text.textContent = 'Generating�';
    errorBox.classList.add('hidden');

    const formData = new FormData(this);
    const skillsRaw = formData.get('skills') || '';

    const data = {
        role:                 formData.get('role'),
        company_name:         formData.get('company_name'),
        location:             formData.get('location'),
        offered_salary:       parseFloat(formData.get('offered_salary')) || 0,
        current_salary:       formData.get('current_salary') ? parseFloat(formData.get('current_salary')) : null,
        experience_years:     parseInt(formData.get('experience_years')) || 0,
        education_level:      formData.get('education_level') || null,
        skills:               skillsRaw ? skillsRaw.split(',').map(s => s.trim()).filter(Boolean) : [],
        is_currently_employed: formData.get('is_currently_employed') === '1',
        has_other_offers:      formData.get('has_other_offers') === '1',
    };

    try {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 110000); // 110s client timeout
        const response = await fetch('/api/negotiation/strategy', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data),
            signal: controller.signal,
        });
        clearTimeout(timeout);

        const result = await response.json();

        if (result.success && result.strategy) {
            window.location.href = '/negotiation/strategy/' + result.strategy.id;
        } else {
            let msg = result.message || 'Failed to generate strategy.';
            if (result.errors) { msg = Object.values(result.errors).flat().join(' '); }
            errorBox.textContent = msg;
            errorBox.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Strategy error:', error);
        errorBox.textContent = 'Request timed out or network error. The AI may be slow — please try again.';
        errorBox.classList.remove('hidden');
    } finally {
        submitBtn.disabled = false;
        spinner.classList.add('hidden');
        text.textContent = 'Generate Strategy';
        submitBtn.onmouseout = () => submitBtn.style.background = 'linear-gradient(135deg,#7c3aed,#a855f7)';
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeNewStrategyModal();
});
</script>
@endpush

{{-- Floating AI Coach Button --}}
<a href="{{ route('negotiation.chatbot') }}"
   title="AI Salary Coach"
   class="fixed z-50 flex items-center gap-2 px-4 py-3 rounded-full shadow-2xl font-semibold text-sm text-white transition-all hover:scale-105 hover:shadow-purple-400/50"
   style="bottom:28px;right:28px;background:linear-gradient(135deg,#7c3aed,#4f46e5);">
    <span class="text-lg leading-none">₹</span>
    <span>AI Salary Coach</span>
    <span class="ml-1 w-2 h-2 rounded-full animate-pulse" style="background:#a5f3a5;"></span>
</a>

@endsection
