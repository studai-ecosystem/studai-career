@props([
    'label' => '',
    'value' => '',
    'change' => null,        // numeric %, positive/negative; renders ▲/▼
    'changeLabel' => null,   // e.g. "vs last month"
    'unit' => null,          // optional suffix shown next to value
])

@php
    // No icon. No gradient. The number is the hero — DM Mono, 32px.
    $hasChange = $change !== null && $change !== '';
    $changeNum = is_numeric($change) ? (float) $change : null;
    $isUp = $changeNum !== null ? $changeNum >= 0 : null;

    $changeColor = $isUp === null
        ? 'text-ink-3'
        : ($isUp ? 'text-success' : 'text-error');

    $arrow = $isUp === null ? '' : ($isUp ? '▲' : '▼');
    $changeDisplay = $changeNum !== null ? abs($changeNum) . '%' : $change;
@endphp

<div {{ $attributes->merge(['class' => 'bg-surface border border-border rounded-lg p-6']) }}>
    <p class="text-11 font-medium uppercase tracking-wide text-ink-3">{{ $label }}</p>

    <div class="mt-3 flex items-baseline gap-1">
        <span class="font-mono text-32 leading-none text-ink-1">{{ $value }}</span>
        @if ($unit)
            <span class="font-mono text-16 text-ink-3">{{ $unit }}</span>
        @endif
    </div>

    @if ($hasChange)
        <div class="mt-2 flex items-center gap-1.5 text-12 {{ $changeColor }}">
            @if ($arrow)
                <span aria-hidden="true">{{ $arrow }}</span>
            @endif
            <span class="font-medium">{{ $changeDisplay }}</span>
            @if ($changeLabel)
                <span class="text-ink-3">{{ $changeLabel }}</span>
            @endif
        </div>
    @endif
</div>
