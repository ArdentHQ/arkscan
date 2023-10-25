@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => 'w-10',
    'badgeWidth' => null,
    'badgeHeight' => null,
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
        :height="$badgeHeight"
    />
</x-ark-tables.cell>
