{{--
    AI Disclaimer Component
    Props:
        - $context    (string)  : employer_screening | candidate_result | admin | global
        - $subjectType(string)  : optional — e.g. 'App\Models\Application'
        - $subjectId  (int|null): optional
--}}
@php
    use App\Services\ResponsibleAI\AIDisclaimerService;

    $service = app(AIDisclaimerService::class);

    $disclaimers = collect();
    try {
        $disclaimers = $service->getForCurrentUser($context ?? 'global');
    } catch (\Throwable $e) {
        // Silently fail — disclaimers are advisory, never blocking
    }

    $severityClasses = [
        'info'     => 'border-blue-400 bg-blue-50 text-blue-800',
        'warning'  => 'border-yellow-400 bg-yellow-50 text-yellow-900',
        'critical' => 'border-red-500 bg-red-50 text-red-900',
    ];
    $iconClasses = [
        'info'     => 'heroicon-o-information-circle text-blue-500',
        'warning'  => 'heroicon-o-exclamation-triangle text-yellow-600',
        'critical' => 'heroicon-o-shield-exclamation text-red-600',
    ];
@endphp

@foreach ($disclaimers as $disclaimer)
    @php
        $severityKey = $disclaimer->severity ?? 'info';
        $alreadyAcked = false;
        if ($disclaimer->requires_acknowledgment) {
            try {
                $alreadyAcked = $disclaimer->hasBeenAcknowledgedBy(
                    auth()->id(),
                    $subjectType ?? null,
                    $subjectId ?? null
                );
            } catch (\Throwable) {}
        }
    @endphp

    @if (!$alreadyAcked || !$disclaimer->requires_acknowledgment)
        <div class="ai-disclaimer mb-3 rounded-lg border-l-4 p-4 {{ $severityClasses[$severityKey] ?? $severityClasses['info'] }}"
             data-disclaimer-id="{{ $disclaimer->id }}"
             role="alert">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex-shrink-0">
                    @if ($severityKey === 'critical')
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.194-.833-2.964 0L4.268 16.5C3.498 18.333 4.46 20 6 20z"/>
                        </svg>
                    @elseif ($severityKey === 'warning')
                        <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    @else
                        <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold">{{ $disclaimer->title }}</p>
                    <p class="mt-1 text-sm">{{ $disclaimer->body }}</p>

                    @if ($disclaimer->requires_acknowledgment)
                        <div class="mt-3">
                            <button type="button"
                                    onclick="acknowledgeDisclaimer({{ $disclaimer->id }}, this)"
                                    class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                I Understand &amp; Acknowledge
                            </button>
                        </div>
                    @endif
                </div>
                <div class="ml-auto pl-3">
                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                          style="background:rgba(0,0,0,0.08)">
                        AI Advisory
                    </span>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script>
function acknowledgeDisclaimer(disclaimerId, btn) {
    btn.disabled = true;
    btn.textContent = 'Acknowledging…';
    fetch('/ai-disclaimers/' + disclaimerId + '/acknowledge', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            subject_type: '{{ addslashes($subjectType ?? '') }}',
            subject_id:   {{ $subjectId ?? 'null' }},
        }),
    }).then(function (res) {
        if (res.ok) {
            var wrapper = btn.closest('.ai-disclaimer');
            if (wrapper) {
                wrapper.style.transition = 'opacity .4s';
                wrapper.style.opacity = '0';
                setTimeout(function () { wrapper.remove(); }, 400);
            }
        } else {
            btn.disabled = false;
            btn.textContent = 'I Understand & Acknowledge';
        }
    }).catch(function () {
        btn.disabled = false;
        btn.textContent = 'I Understand & Acknowledge';
    });
}
</script>
