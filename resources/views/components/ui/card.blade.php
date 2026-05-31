@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,       // set false for flush content (e.g. tables)
    'interactive' => false,  // adds hover border treatment for clickable cards
])

@php
    // MERIDIAN cards: 12px radius, 1px border, no shadow, flat surface.
    $base = 'bg-surface border border-border rounded-lg';
    $hover = $interactive ? ' transition-colors duration-150 hover:border-border-strong cursor-pointer' : '';
    $classes = trim($base . $hover);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if ($title || $subtitle || isset($header))
        <div class="flex items-start justify-between gap-4 px-6 pt-6 {{ $padding ? '' : 'pb-4 border-b border-border' }}">
            <div class="min-w-0">
                @if ($title)
                    <h3 class="text-16 font-semibold text-ink-1 truncate">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="mt-0.5 text-12 text-ink-3">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($header)
                <div class="flex items-center gap-2 shrink-0">{{ $header }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding ? 'p-6' : '' }} {{ ($title || $subtitle || isset($header)) && $padding ? 'pt-4' : '' }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="px-6 py-4 border-t border-border">{{ $footer }}</div>
    @endisset
</div>
