@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => null,
    'badgeWidth' => null,
])

<x-ark-tables.cell
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :class="$class"
>
    <x-loading.text
        :width="$badgeWidth"
        height="h-[21px]"
    />
</x-ark-tables.cell>
