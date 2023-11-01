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
    <div class="flex space-x-2">
        <x-loading.text width="w-5" height="h-5" />

        <x-loading.text height="h-5" />
    </div>
</x-ark-tables.cell>
