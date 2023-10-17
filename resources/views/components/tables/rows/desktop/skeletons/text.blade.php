@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'nestedDataBreakpoint' => null,
])

@if ($nestedDataBreakpoint)
    <x-tables.rows.desktop.skeletons.nested-data
        :responsive="$responsive"
        :breakpoint="$breakpoint"
        :first-on="$firstOn"
        :last-on="$lastOn"
        :nested-data-breakpoint="$nestedDataBreakpoint"
    />
@else
    <x-ark-tables.cell
        :responsive="$responsive"
        :breakpoint="$breakpoint"
        :first-on="$firstOn"
        :last-on="$lastOn"
    >
        <x-loading.text />
    </x-ark-tables.cell>
@endif
