{{-- RETIRED → forwards to MERIDIAN <x-ui.button>. Migrate call sites to x-ui.button. --}}
@props(['variant' => 'primary'])
<x-ui.button :variant="$variant" {{ $attributes->except('variant') }}>{{ $slot }}</x-ui.button>
