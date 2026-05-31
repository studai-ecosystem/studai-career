@props(['name' => null, 'show' => false, 'maxWidth' => 'lg'])
{{-- RETIRED → forwards to MERIDIAN <x-ui.modal>. Migrate call sites to x-ui.modal. --}}
<x-ui.modal :name="$name" :show="$show ? 'true' : 'false'" :size="$maxWidth === '2xl' ? 'xl' : ($maxWidth === 'lg' ? 'lg' : 'md')" {{ $attributes }}>
    {{ $slot }}
</x-ui.modal>
