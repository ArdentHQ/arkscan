<x-loading.visible>
    <x-transactions.table-desktop-skeleton />

    <x-transactions.table-mobile-skeleton />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
