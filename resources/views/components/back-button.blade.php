{{--
    Back Button Component
    Usage: <x-back-button /> or <x-back-button label="Go Back" class="..." />
    Props:
        $label  - button text (default: "Back")
        $class  - additional Tailwind classes
--}}
@props([
    'label' => 'Back',
    'class' => '',
])

<button
    type="button"
    onclick="history.back()"
    title="{{ $label }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 text-sm font-medium transition-colors duration-150 ' . $class]) }}
    style="color:#6366f1"
    onmouseover="this.style.color='#4338ca'"
    onmouseout="this.style.color='#6366f1'"
>
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
    </svg>
    <span>{{ $label }}</span>
</button>
