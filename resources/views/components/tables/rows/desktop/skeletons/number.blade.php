@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'nestedDataBreakpoint' => null,
])

@if ($nestedDataBreakpoint)
    <x-tables.rows.desktop.skeletons.nested-data
        :responsive="$responsive"
        :breakpoint="$breakpoint"
        :first-on="$firstOn"
        :last-on="$lastOn"
        :nested-data-breakpoint="$nestedDataBreakpoint"
        class="text-right"
    />
@else
    <x-ark-tables.cell
        :responsive="$responsive"
        :breakpoint="$breakpoint"
        :first-on="$firstOn"
        :last-on="$lastOn"
        :class="'text-right ' . $class"
    >
        <x-loading.text />
    </x-ark-tables.cell>
@endif
