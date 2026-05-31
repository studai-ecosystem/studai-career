@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'id' => null,
    'hint' => null,         // helper text below the field
    'error' => null,        // error message (overrides hint styling)
    'required' => false,
    'optional' => false,
])

@php
    $fieldId = $id ?? $name ?? 'field-' . uniqid();
    $hasError = filled($error);

    // MERIDIAN inputs: label above, 40px height, 8px radius, 1px border,
    // accent focus ring via box-shadow. No floating labels, no pills.
    $fieldBase = 'w-full h-10 px-3 font-ui text-14 text-ink-1 bg-surface rounded-md '
               . 'border transition-shadow duration-150 placeholder:text-ink-4 '
               . 'focus:outline-none focus:ring-4 disabled:opacity-50 disabled:bg-surface-raised';

    $fieldState = $hasError
        ? 'border-error focus:border-error focus:ring-error-subtle'
        : 'border-border focus:border-accent focus:ring-accent-subtle';

    $fieldClass = trim("$fieldBase $fieldState");
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'flex flex-col gap-1.5']) }}>
    @if ($label)
        <label for="{{ $fieldId }}" class="flex items-center gap-1 text-12 font-medium text-ink-2">
            <span>{{ $label }}</span>
            @if ($required)
                <span class="text-error" aria-hidden="true">*</span>
            @elseif ($optional)
                <span class="text-ink-4 font-normal">(optional)</span>
            @endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea
            id="{{ $fieldId }}"
            name="{{ $name }}"
            @required($required)
            {{ $attributes->except('class')->merge(['class' => $fieldClass . ' h-auto min-h-[96px] py-2.5 resize-y']) }}
        >{{ $slot }}</textarea>
    @else
        <input
            type="{{ $type }}"
            id="{{ $fieldId }}"
            name="{{ $name }}"
            @required($required)
            {{ $attributes->except('class')->merge(['class' => $fieldClass]) }}
        />
    @endif

    @if ($hasError)
        <p class="text-12 text-error">{{ $error }}</p>
    @elseif ($hint)
        <p class="text-12 text-ink-3">{{ $hint }}</p>
    @endif
</div>
