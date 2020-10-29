<x-loading.visible>
    <x-wallets.table-desktop-skeleton />

    <x-wallets.table-mobile-skeleton />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
