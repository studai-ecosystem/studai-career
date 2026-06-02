@extends('layouts.dashboard')

@section('title', 'AI Decision Explanation')
@section('page-title', 'How this decision was made')

@section('content')
<div style="background:#EBF2FF; min-height:100%; padding:1.5rem;">
    <div class="max-w-3xl mx-auto space-y-5">

        {{-- Header --}}
        <div class="bg-white rounded-2xl border border-blue-100 p-6">
            <h1 class="text-xl font-bold text-gray-900">
                AI Decision Explanation
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Application for
                <span class="font-semibold text-gray-700">{{ $application->job->title ?? 'this role' }}</span>
            </p>
            <p class="text-xs text-gray-400 mt-2">
                You have the right to an explanation of automated decisions affecting you.
                The summary below describes the factors our AI evaluation engine (Orin™) considered.
            </p>
        </div>

        @if($decision === null)
            <div class="bg-white rounded-2xl border border-blue-100 p-6 text-sm text-gray-600">
                An automated decision explanation is not yet available for this application.
                If you believe this is an error, please contact support.
            </div>
        @else
            {{-- Headline --}}
            <div class="bg-white rounded-2xl border border-blue-100 p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700">Outcome</span>
                    <span class="text-sm font-bold"
                          style="color:#2D6CDF;">
                        {{ ucfirst($decision->final_decision ?? $decision->ai_recommendation ?? $decision->decision_type) }}
                    </span>
                </div>

                @if($decision->ai_score !== null)
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700">Overall score</span>
                    <span class="text-sm font-bold text-gray-900">
                        {{ round((float) $decision->ai_score, 1) }}/100
                        <span class="text-xs font-medium text-gray-400">({{ $decision->score_label }})</span>
                    </span>
                </div>
                @endif

                @if($decision->confidence !== null)
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700">Confidence</span>
                    <span class="text-sm text-gray-700">{{ round((float) $decision->confidence * 100, 0) }}%</span>
                </div>
                @endif
            </div>

            {{-- Natural language explanation --}}
            @if(!empty($decision->natural_language_explanation))
            <div class="bg-white rounded-2xl border border-blue-100 p-6">
                <h2 class="text-sm font-bold text-gray-800 mb-2">Summary</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $decision->natural_language_explanation }}
                </p>
            </div>
            @endif

            {{-- Contributing factors --}}
            @if($factors->isNotEmpty())
            <div class="bg-white rounded-2xl border border-blue-100 p-6">
                <h2 class="text-sm font-bold text-gray-800 mb-3">Contributing factors</h2>
                <div class="space-y-2">
                    @foreach($factors as $factor)
                    <div class="flex items-center justify-between border-b border-gray-50 pb-2 last:border-0">
                        <span class="text-sm text-gray-600">{{ $factor['label'] }}</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $factor['value'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <p class="text-xs text-gray-400 px-2">
                Evaluated by model: {{ $decision->model_used ?? 'Orin™' }}@if($decision->prompt_version) ({{ $decision->prompt_version }})@endif.
                If you would like a human review of this decision, please contact support.
            </p>
        @endif

    </div>
</div>
@endsection
