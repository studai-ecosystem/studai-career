@extends('layouts.dashboard')

@section('title', 'Negotiation Tactics Library')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
                <a href="{{ route('negotiation.dashboard') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Negotiation</a>
                <span>/</span>
                <span class="text-gray-900 dark:text-white font-medium">Tactics Library</span>
            </nav>
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Negotiation Tactics Library</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Proven strategies to maximise your compensation package</p>
                </div>
                <a href="{{ route('negotiation.dashboard') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white shadow"
                   style="background:#2D6CDF">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Strategy
                </a>
            </div>
        </div>

        @if($tactics->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-16 text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6" style="background:#EBF2FF">
                    <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Tactics Yet</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">The tactics library will be populated as you use the negotiation strategist.</p>
                <a href="{{ route('negotiation.dashboard') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white shadow"
                   style="background:#2D6CDF">
                    Go to Negotiation Dashboard
                </a>
            </div>
        @else
            {{-- Stats Bar --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tactics->flatten()->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Tactics</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tactics->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Categories</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-green-600">{{ $tactics->flatten()->where('risk_level', 'low')->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Low Risk</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold" style="color:#2D6CDF">{{ $tactics->flatten()->where('is_active', true)->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active</p>
                </div>
            </div>

            {{-- Tactics by Category --}}
            @foreach($tactics as $category => $categoryTactics)
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#2D6CDF22">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $category) }}</h2>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">{{ $categoryTactics->count() }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($categoryTactics as $tactic)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm hover:shadow-md transition-shadow">
                        {{-- Risk badge + active status --}}
                        <div class="flex items-center justify-between mb-3">
                            @php
                                $riskColors = [
                                    'low'    => 'bg-green-100 text-green-700',
                                    'medium' => 'bg-yellow-100 text-yellow-700',
                                    'high'   => 'bg-red-100 text-red-700',
                                ];
                                $riskClass = $riskColors[$tactic->risk_level] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $riskClass }}">
                                {{ ucfirst($tactic->risk_level ?? 'unknown') }} Risk
                            </span>
                            @if($tactic->is_active)
                                <span class="flex items-center gap-1 text-xs text-green-600 font-medium">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span>Active
                                </span>
                            @endif
                        </div>

                        {{-- Name --}}
                        <h3 class="font-bold text-gray-900 dark:text-white text-base mb-2">{{ $tactic->tactic_name }}</h3>

                        {{-- Description --}}
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 leading-relaxed">{{ Str::limit($tactic->description, 120) }}</p>

                        {{-- When to use --}}
                        @if($tactic->when_to_use)
                        <div class="mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <p class="text-xs font-semibold text-blue-700 dark:text-blue-400 mb-1">When to use</p>
                            <p class="text-xs text-blue-600 dark:text-blue-300">{{ Str::limit($tactic->when_to_use, 100) }}</p>
                        </div>
                        @endif

                        {{-- Effectiveness --}}
                        @if($tactic->average_effectiveness)
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="width:{{ min(100, $tactic->average_effectiveness * 10) }}%;background:#2D6CDF"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ number_format($tactic->average_effectiveness, 1) }}/10</span>
                        </div>
                        @endif

                        {{-- Example phrases --}}
                        @if(!empty($tactic->example_phrases) && is_array($tactic->example_phrases))
                        <details class="mt-3">
                            <summary class="text-xs font-semibold text-purple-600 cursor-pointer hover:text-purple-700 select-none">
                                Example phrases ({{ count($tactic->example_phrases) }})
                            </summary>
                            <ul class="mt-2 space-y-1">
                                @foreach(array_slice($tactic->example_phrases, 0, 3) as $phrase)
                                <li class="text-xs text-gray-600 dark:text-gray-400 pl-3 border-l-2 border-purple-200 italic">"{{ $phrase }}"</li>
                                @endforeach
                            </ul>
                        </details>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        @endif

        {{-- Back to Dashboard --}}
        <div class="mt-8 text-center">
            <a href="{{ route('negotiation.dashboard') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Negotiation Dashboard
            </a>
        </div>

    </div>
</div>
@endsection
