<x-loading.visible>
    <x-tables.desktop.skeleton.wallets />

    <x-tables.mobile.skeleton.wallets />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
