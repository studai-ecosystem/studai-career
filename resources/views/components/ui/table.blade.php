@props([
    'headers' => [],     // array of column labels, or use the <x-slot:head> for custom markup
    'loading' => false,
    'rows' => 5,         // skeleton row count when loading
    'empty' => 'No records to display.',
    'isEmpty' => false,
])

@php
    // MERIDIAN tables: horizontal rules only, no vertical lines, no zebra.
    $colCount = count($headers) ?: 4;
@endphp

<div {{ $attributes->merge(['class' => 'w-full overflow-x-auto bg-surface border border-border rounded-lg']) }}>
    <table class="w-full border-collapse text-14">
        <thead>
            <tr class="border-b border-border">
                @isset($head)
                    {{ $head }}
                @else
                    @foreach ($headers as $header)
                        <th class="px-4 h-11 text-left text-11 font-semibold uppercase tracking-wide text-ink-3 whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                @endisset
            </tr>
        </thead>
        <tbody class="divide-y divide-border">
            @if ($loading)
                @for ($i = 0; $i < $rows; $i++)
                    <tr>
                        @for ($c = 0; $c < $colCount; $c++)
                            <td class="px-4 h-12">
                                <div class="meridian-skeleton h-3 rounded-sm" style="width: {{ [60, 80, 45, 70][$c % 4] }}%"></div>
                            </td>
                        @endfor
                    </tr>
                @endfor
            @elseif ($isEmpty)
                <tr>
                    <td colspan="{{ $colCount }}" class="px-4 py-12 text-center text-14 text-ink-3">
                        {{ $empty }}
                    </td>
                </tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>

@once
    @push('styles')
    <style>
        /* Row defaults — referenced by <x-ui.table> rows. */
        .meridian-row { transition: background-color 120ms ease; }
        .meridian-row:hover { background-color: var(--color-surface-raised); }
        .meridian-row[data-selected="true"] { background-color: var(--color-accent-subtle); }
        .meridian-cell { padding: 0 16px; height: 48px; color: var(--color-ink-1); }
    </style>
    @endpush
@endonce
