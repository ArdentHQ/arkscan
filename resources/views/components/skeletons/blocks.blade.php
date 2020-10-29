<x-loading.visible>
    <x-blocks.table-desktop-skeleton />

    <x-blocks.table-mobile-skeleton />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
