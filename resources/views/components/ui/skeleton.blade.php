@props([
    'width' => 'w-full',
    'height' => 'h-4',
    'rounded' => 'rounded-sm',
    'lines' => 1,        // when >1, renders stacked text-line skeletons
])

@php
    $base = "meridian-skeleton $height $rounded";
@endphp

@if ($lines > 1)
    <div {{ $attributes->merge(['class' => 'flex flex-col gap-2 ' . $width]) }}>
        @for ($i = 0; $i < $lines; $i++)
            <div class="{{ $base }}" style="width: {{ $i === $lines - 1 ? '70%' : '100%' }}"></div>
        @endfor
    </div>
@else
    <div {{ $attributes->merge(['class' => trim("$base $width")]) }}></div>
@endif
