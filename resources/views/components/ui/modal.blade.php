@props([
    'name' => null,          // Alpine/Livewire trigger name; uses x-data when null
    'title' => null,
    'subtitle' => null,
    'size' => 'md',          // sm | md | lg | xl
    'show' => 'false',       // Alpine expression controlling visibility
])

@php
    $widths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
    ];
    $width = $widths[$size] ?? $widths['md'];
@endphp

{{-- MERIDIAN modal: the ONLY place a shadow is permitted in the product UI. --}}
<div
    x-data="{ open: {{ $show }} }"
    @if ($name) x-on:open-{{ $name }}.window="open = true" x-on:close-{{ $name }}.window="open = false" @endif
    x-show="open"
    x-cloak
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display:none;"
>
    {{-- Scrim --}}
    <div
        x-show="open"
        x-transition.opacity
        @click="open = false"
        class="absolute inset-0 bg-ink-1/40"
    ></div>

    {{-- Panel --}}
    <div
        x-show="open"
        x-transition
        class="relative w-full {{ $width }} bg-surface border border-border rounded-lg shadow-overlay"
        role="dialog"
        aria-modal="true"
    >
        @if ($title || $subtitle)
            <div class="flex items-start justify-between gap-4 px-6 pt-6 pb-4 border-b border-border">
                <div class="min-w-0">
                    @if ($title)
                        <h2 class="text-18 font-semibold text-ink-1">{{ $title }}</h2>
                    @endif
                    @if ($subtitle)
                        <p class="mt-0.5 text-12 text-ink-3">{{ $subtitle }}</p>
                    @endif
                </div>
                <button type="button" @click="open = false" class="shrink-0 text-ink-3 hover:text-ink-1 transition-colors" aria-label="Close">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M5 5l10 10M15 5L5 15" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        @endif

        <div class="px-6 py-6">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="px-6 py-4 border-t border-border flex items-center justify-end gap-2">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
