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
    <div class="flex space-x-9">
        <div class="flex space-x-2">
            <x-loading.text width="w-[40px]" />
            <x-loading.text />
        </div>

        @if ($generic)
            <div class="flex space-x-2">
                <x-loading.text width="w-[40px]" />
                <x-loading.text />
            </div>
        @endif
    </div>
</x-ark-tables.cell>
