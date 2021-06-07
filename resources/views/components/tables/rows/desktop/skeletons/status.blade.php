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
    <div class="flex flex-row items-center space-x-2 md:space-x-3">
        <div class="w-6 h-6 rounded-full md:w-11 md:h-11 loading-state status-circle"></div>
        <div class="hidden w-6 h-6 rounded-full sm:block md:w-11 md:h-11 loading-state status-circle"></div>
        <div class="hidden w-6 h-6 rounded-full sm:block md:w-11 md:h-11 loading-state status-circle"></div>
        <div class="hidden w-6 h-6 rounded-full sm:block md:w-11 md:h-11 loading-state status-circle"></div>
        <div class="hidden w-6 h-6 rounded-full sm:block md:w-11 md:h-11 loading-state status-circle"></div>
    </div>
</x-ark-tables.cell>
