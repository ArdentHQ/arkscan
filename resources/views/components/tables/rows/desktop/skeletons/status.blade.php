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
    <div class="flex flex-row justify-end items-center space-x-2 w-full lg:justify-start lg:space-x-3">
        <div class="w-5 h-5 rounded-full lg:w-11 lg:h-11 loading-state status-circle"></div>
        <div class="hidden w-5 h-5 rounded-full sm:block lg:w-11 lg:h-11 loading-state status-circle"></div>
        <div class="hidden w-5 h-5 rounded-full sm:block lg:w-11 lg:h-11 loading-state status-circle"></div>
        <div class="hidden w-5 h-5 rounded-full sm:block lg:w-11 lg:h-11 loading-state status-circle"></div>
        <div class="hidden w-5 h-5 rounded-full sm:block lg:w-11 lg:h-11 loading-state status-circle"></div>
    </div>
</x-ark-tables.cell>
