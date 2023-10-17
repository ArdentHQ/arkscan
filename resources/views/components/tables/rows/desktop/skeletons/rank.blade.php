@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
])

<x-ark-tables.cell
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    class="w-12"
>
    <x-loading.text width="w-8" />
</x-ark-tables.cell>
