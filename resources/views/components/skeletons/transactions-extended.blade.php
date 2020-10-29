<x-loading.visible>
    <x-transactions.table-desktop-skeleton use-confirmations use-direction />

    <x-transactions.table-mobile-skeleton use-confirmations use-direction />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
