@props([
    'variant' => 'default',  // default | accent | success | warning | error | info
    'pill' => false,         // true => 100px rounded tag (use for skills/categories)
    'status' => null,        // application status string => auto-maps variant
])

@php
    // Application status → variant mapping (single source of truth).
    $statusMap = [
        'pending'     => 'default',
        'reviewing'   => 'accent',
        'shortlisted' => 'accent',
        'interview'   => 'accent',
        'offer sent'  => 'warning',
        'offer'       => 'warning',
        'rejected'    => 'error',
        'declined'    => 'error',
        'hired'       => 'success',
        'accepted'    => 'success',
    ];

    if ($status !== null) {
        $variant = $statusMap[strtolower(trim($status))] ?? 'default';
    }

    $variants = [
        'default' => 'bg-surface-raised text-ink-2 border-border',
        'accent'  => 'bg-accent-subtle text-accent-text border-accent-muted',
        'success' => 'bg-success-subtle text-success border-success-border',
        'warning' => 'bg-warning-subtle text-warning border-warning-border',
        'error'   => 'bg-error-subtle text-error border-error-border',
        'info'    => 'bg-info-subtle text-info border-info-border',
    ];

    // Status badges are rectangular (4px); tag/pill variant is fully rounded.
    $shape = $pill ? 'rounded-full px-2.5 py-0.5' : 'rounded-sm px-2 py-0.5';
    $variantClass = $variants[$variant] ?? $variants['default'];

    $classes = trim(
        "inline-flex items-center gap-1 font-ui text-11 font-medium border $shape $variantClass"
    );
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot ?: ($status ? ucfirst($status) : '') }}
</span>
