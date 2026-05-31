@props([
    'variant' => 'primary',   // primary | secondary | ghost | destructive | link
    'size' => 'md',           // sm | md | lg
    'type' => 'button',
    'href' => null,
    'icon' => null,           // leading icon slot name (optional)
    'loading' => false,
    'disabled' => false,
])

@php
    // MERIDIAN buttons: rectangular (4px radius), no gradients, no full-round.
    $base = 'inline-flex items-center justify-center gap-2 font-ui font-medium rounded-sm '
          . 'transition-colors duration-150 select-none whitespace-nowrap '
          . 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 '
          . 'focus-visible:ring-offset-canvas disabled:opacity-50 disabled:pointer-events-none';

    $sizes = [
        'sm' => 'h-8 px-3 text-12',
        'md' => 'h-10 px-4 text-14',
        'lg' => 'h-12 px-6 text-16',
    ];

    $variants = [
        'primary'     => 'bg-accent text-white hover:bg-accent-hover',
        'secondary'   => 'bg-surface text-ink-1 border border-border-strong hover:bg-surface-raised',
        'ghost'       => 'bg-transparent text-ink-2 hover:bg-surface-raised hover:text-ink-1',
        // Destructive is tinted, not a loud filled red.
        'destructive' => 'bg-error-subtle text-error border border-error-border hover:bg-error hover:text-white',
        'link'        => 'bg-transparent text-accent hover:text-accent-hover underline underline-offset-4 px-0 h-auto',
    ];

    $sizeClass = $variant === 'link' ? '' : ($sizes[$size] ?? $sizes['md']);
    $variantClass = $variants[$variant] ?? $variants['primary'];
    $classes = trim("$base $sizeClass $variantClass");

    $tag = $href ? 'a' : 'button';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($loading)
            <span class="meridian-spinner" aria-hidden="true"></span>
        @endif
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        @disabled($disabled || $loading)
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if ($loading)
            <span class="meridian-spinner" aria-hidden="true"></span>
        @endif
        {{ $slot }}
    </button>
@endif
