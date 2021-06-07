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
    <div class="flex justify-between items-center space-x-2 w-full md:space-x-3 md:flex-row md:justify-start">
        <div>
            <div class="w-6 h-6 rounded-full avatar-wrapper md:w-11 md:h-11 loading-state"></div>
        </div>

        <x-loading.text />
    </div>
</x-ark-tables.cell>
