@extends('layouts.dashboard')
@section('title', 'Test Result — ' . $round->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-fuchsia-50 to-indigo-50 py-8">
    <div class="max-w-3xl mx-auto px-4">

        {{-- Result Card --}}
        <div class="bg-white rounded-3xl shadow-lg border border-purple-100 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-purple-600 to-fuchsia-600 px-8 py-10 text-center text-white">
                <p class="text-sm font-bold uppercase tracking-widest text-purple-200 mb-2">{{ $round->job->title }} &mdash; Round {{ $round->round_order }}</p>
                <h1 class="text-3xl font-bold mb-1">{{ $round->name }}</h1>
                <p class="text-purple-200 text-sm">Test {{ $attempt->status === 'evaluated' ? 'Evaluated' : 'Submitted — evaluation in progress' }}</p>
            </div>

            <div class="p-8">
                @if($attempt->score !== null)
                {{-- Score donut --}}
                <div class="flex justify-center mb-8">
                    <div class="relative w-40 h-40">
                        <svg class="w-40 h-40 -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#EBF2FF" stroke-width="12"/>
                            <circle cx="60" cy="60" r="50" fill="none"
                                stroke="{{ $attempt->score >= 70 ? '#1B57C4' : ($attempt->score >= 50 ? '#E37400' : '#2D6CDF') }}"
                                stroke-width="12"
                                stroke-dasharray="{{ 2 * M_PI * 50 }}"
                                stroke-dashoffset="{{ 2 * M_PI * 50 * (1 - $attempt->score / 100) }}"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-4xl font-bold text-gray-900">{{ $attempt->score }}</span>
                            <span class="text-sm text-gray-500 font-medium">/ 100</span>
                        </div>
                    </div>
                </div>

                {{-- Badge --}}
                <div class="text-center mb-6">
                    @if($attempt->score >= 70)
                        <span class="inline-flex items-center gap-2 px-5 py-2 bg-green-100 text-green-800 font-bold rounded-full text-sm">
                            ✅ Passed — Great work!
                        </span>
                    @elseif($attempt->score >= 50)
                        <span class="inline-flex items-center gap-2 px-5 py-2 bg-amber-100 text-amber-800 font-bold rounded-full text-sm">
                            ⚠️ Average — Room to improve
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-5 py-2 bg-red-100 text-red-800 font-bold rounded-full text-sm">
                            ❌ Below threshold
                        </span>
                    @endif
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 font-medium">Your answers are being evaluated by AI...</p>
                    <p class="text-gray-400 text-sm mt-1">Check back in a few minutes</p>
                </div>
                @endif

                {{-- AI Feedback --}}
                @if($attempt->ai_feedback)
                <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5 mb-6">
                    <p class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-2">AI Feedback</p>
                    <p class="text-gray-700 text-sm leading-relaxed">{{ $attempt->ai_feedback }}</p>
                </div>
                @endif

                {{-- Answer Review --}}
                @if($attempt->answers)
                <div class="space-y-4">
                    <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Your Answers</h3>
                    @foreach($attempt->answers as $i => $a)
                    <div class="rounded-xl border p-4
                        @if(isset($a['correct_answer']))
                            {{ $a['correct_answer'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}
                        @else border-gray-100 bg-gray-50 @endif">
                        <p class="text-xs text-gray-500 font-semibold mb-1">Q{{ $i + 1 }}</p>
                        <p class="text-sm font-medium text-gray-900 mb-2">{{ $a['question'] }}</p>
                        @if($a['type'] === 'mcq')
                            <p class="text-sm">
                                Your answer: <strong>{{ $a['options'][$a['given'] ?? ''] ?? 'Not answered' }}</strong>
                                @if(isset($a['correct_answer']) && !$a['correct_answer'])
                                &mdash; Correct: <span class="text-green-700 font-semibold">{{ $a['options'][$a['correct']] ?? '' }}</span>
                                @endif
                            </p>
                        @else
                            <p class="text-sm text-gray-600 italic">{{ $a['given'] ?: 'Not answered' }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('jobs.show', $round->job_id) }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-purple-300 text-purple-700 font-bold rounded-xl hover:bg-purple-50 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Job
            </a>
        </div>
    </div>
</div>
@endsection
