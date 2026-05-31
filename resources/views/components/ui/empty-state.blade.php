@props([
    'title' => 'Nothing here yet',
    'message' => null,
    'icon' => null,       // optional thin-line svg slot via $icon
])

{{-- MERIDIAN empty state: text-driven, restrained, no illustration clutter. --}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-16 px-6']) }}>
    @isset($icon)
        <div class="mb-4 text-ink-4">{{ $icon }}</div>
    @endisset

    <h3 class="text-16 font-semibold text-ink-1">{{ $title }}</h3>

    @if ($message)
        <p class="mt-1 text-14 text-ink-3 max-w-reading">{{ $message }}</p>
    @endif

    @isset($action)
        <div class="mt-6">{{ $action }}</div>
    @endisset
</div>
