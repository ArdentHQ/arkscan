<x-loading.visible display-type="block">
    <x-tables.desktop.skeleton.wallet-transactions />

    <x-tables.mobile.skeleton.wallet-transactions />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
