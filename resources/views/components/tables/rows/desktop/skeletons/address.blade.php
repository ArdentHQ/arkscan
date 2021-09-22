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
    <div class="flex justify-between items-center space-x-2 w-full md:flex-row md:justify-start md:space-x-4">
        <div>
            <div class="w-6 h-6 rounded-full md:w-11 md:h-11 avatar-wrapper loading-state"></div>
        </div>

        <x-loading.text />
    </div>
</x-ark-tables.cell>
