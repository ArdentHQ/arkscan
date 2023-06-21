@props([
    'rowCount' => 10,
])

<x-loading.visible display-type="block">
    <x-tables.desktop.skeleton.wallet-transactions :row-count="$rowCount" />

    <x-tables.mobile.skeleton.wallet-transactions />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
