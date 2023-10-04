@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'generic' => false,
])

<x-ark-tables.cell
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
>
    <div class="md-lg:flex md-lg:space-x-9 space-y-2 sm:space-y-1 md:space-y-2 md-lg:space-y-0">
        <div class="flex space-x-2">
            <x-loading.text width="w-[39px]" height="h-[21px]" />
            <x-loading.text height="h-[21px]" />
        </div>

        @if ($generic)
            <div class="flex space-x-2">
                <x-loading.text width="w-[39px]" height="h-[21px]" />
                <x-loading.text height="h-[21px]" />
            </div>
        @endif
    </div>
</x-ark-tables.cell>
