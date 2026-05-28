{{--
    AI Score Explanation Component
    Props:
        - $log         : App\Models\AIDecisionLog
        - $showFactors : bool (default true)
--}}
@php
    $showFactors ??= true;

    $recommendationColor = match ($log->recommendation ?? '') {
        'shortlist' => '#137333',  // green
        'reject'    => '#c5221f',  // red
        'review'    => '#e37400',  // amber
        'hold'      => '#5f6368',  // gray
        default     => '#1a73e8',
    };

    $scorePercent = round(($log->ai_score ?? 0) * 100);
    $confidencePercent = round(($log->confidence_score ?? 0) * 100);

    $effectiveDecision = $log->effective_decision ?? $log->recommendation;

    $factors = $log->xai_factors ?? [];
@endphp

<div class="ai-score-explanation rounded-xl border border-gray-200 bg-white shadow-sm">
    {{-- Header bar --}}
    <div class="flex items-center justify-between rounded-t-xl px-4 py-3"
         style="background: #f8f9fa; border-bottom: 1px solid #e2e8f0;">
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.346.346A3.51 3.51 0 0114 18.5H10a3.51 3.51 0 01-2.492-1.031l-.346-.346z"/>
            </svg>
            <span class="text-sm font-semibold text-gray-700">AI Assessment</span>
            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-700">
                Advisory Only — Human Judgment Applies
            </span>
        </div>
        <span class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
    </div>

    <div class="p-4">
        {{-- Score + Recommendation row --}}
        <div class="flex flex-wrap items-center gap-4">
            {{-- Score circle --}}
            <div class="flex flex-col items-center">
                <div class="relative flex h-16 w-16 items-center justify-center rounded-full"
                     style="background: conic-gradient({{ $recommendationColor }} {{ $scorePercent }}%, #e2e8f0 0%);">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white">
                        <span class="text-lg font-bold" style="color: {{ $recommendationColor }};">
                            {{ $scorePercent }}
                        </span>
                    </div>
                </div>
                <span class="mt-1 text-xs text-gray-500">AI Score</span>
            </div>

            {{-- Recommendation + confidence --}}
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold text-white"
                          style="background: {{ $recommendationColor }};">
                        {{ ucfirst($effectiveDecision) }}
                    </span>
                    @if ($log->was_overridden)
                        <span class="inline-flex items-center rounded-full border border-orange-300 bg-orange-50 px-2 py-0.5 text-xs text-orange-700">
                            Human Override Applied
                        </span>
                    @endif
                    @if ($log->bias_flag)
                        <span class="inline-flex items-center rounded-full border border-red-300 bg-red-50 px-2 py-0.5 text-xs text-red-700">
                            ⚠ Bias Flag
                        </span>
                    @endif
                </div>

                <div class="mt-2 text-xs text-gray-500">
                    Confidence:
                    <span class="font-medium text-gray-700">{{ $confidencePercent }}%</span>
                    <span class="ml-2 text-gray-400">
                        · Decision Type: {{ ucwords(str_replace('_', ' ', $log->decision_type ?? '')) }}
                    </span>
                </div>

                {{-- Confidence bar --}}
                <div class="mt-1.5 h-1.5 w-40 overflow-hidden rounded-full bg-gray-200">
                    <div class="h-full rounded-full"
                         style="width: {{ $confidencePercent }}%; background: {{ $recommendationColor }};"></div>
                </div>
            </div>
        </div>

        {{-- Natural language explanation --}}
        @if ($log->explanation)
            <div class="mt-3 rounded-lg bg-gray-50 p-3 text-sm text-gray-700">
                {{ $log->explanation }}
            </div>
        @endif

        {{-- XAI Factors --}}
        @if ($showFactors && ! empty($factors))
            <div class="mt-3">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Why This Score</p>
                <div class="space-y-1.5">
                    @foreach ($factors as $factor)
                        @php
                            $weight    = round(($factor['weight'] ?? 0) * 100);
                            $direction = ($factor['direction'] ?? 'positive') === 'positive' ? '#137333' : '#c5221f';
                            $label     = $factor['label'] ?? $factor['factor'] ?? 'Unknown';
                            $detail    = $factor['detail'] ?? null;
                        @endphp
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-36 truncate text-gray-600" title="{{ $label }}">{{ $label }}</span>
                            <div class="flex-1 overflow-hidden rounded-full bg-gray-200" style="height: 6px;">
                                <div class="h-full rounded-full"
                                     style="width: {{ $weight }}%; background: {{ $direction }};"></div>
                            </div>
                            <span class="w-8 text-right font-medium" style="color: {{ $direction }};">
                                {{ $weight }}%
                            </span>
                            @if ($detail)
                                <span class="text-gray-400">— {{ $detail }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Override info --}}
        @if ($log->was_overridden && $log->override)
            @php $ov = $log->override; @endphp
            <div class="mt-3 rounded-lg border border-orange-200 bg-orange-50 p-3 text-xs text-orange-800">
                <strong>Human Override:</strong>
                {{ $ov->overrider->name ?? 'A reviewer' }} changed the decision to
                <strong>{{ ucfirst($ov->override_decision) }}</strong>.
                @if ($ov->is_bias_correction)
                    <span class="ml-1 font-semibold text-red-700">[Bias Correction]</span>
                @endif
                <br>
                <em>Reason: {{ $ov->reason }}</em>
            </div>
        @endif

        {{-- Bias flag detail --}}
        @if ($log->bias_flag && !empty($log->bias_indicators))
            <div class="mt-3 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-800">
                <strong>⚠ Bias Indicators Detected:</strong>
                <ul class="mt-1 list-disc pl-4">
                    @foreach ($log->bias_indicators as $indicator)
                        <li>{{ $indicator }}</li>
                    @endforeach
                </ul>
                <p class="mt-1 text-red-700">Human review is recommended before acting on this score.</p>
            </div>
        @endif

        {{-- Footer disclaimer --}}
        <div class="mt-3 border-t border-gray-100 pt-2 text-center text-xs text-gray-400">
            This AI assessment is advisory. The final hiring decision must always be made by a qualified human.
        </div>
    </div>
</div>
