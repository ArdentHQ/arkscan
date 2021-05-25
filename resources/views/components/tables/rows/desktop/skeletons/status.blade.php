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
>
    <div class="flex flex-row-reverse justify-between items-center md:flex-row md:space-x-3 md:justify-start">
        <div class="w-6 h-6 rounded-full md:w-11 md:h-11 loading-state"></div>
        <div class="w-6 h-6 rounded-full md:w-11 md:h-11 loading-state"></div>
        <div class="w-6 h-6 rounded-full md:w-11 md:h-11 loading-state"></div>
        <div class="w-6 h-6 rounded-full md:w-11 md:h-11 loading-state"></div>
        <div class="w-6 h-6 rounded-full md:w-11 md:h-11 loading-state"></div>
    </div>
</x-ark-tables.cell>
