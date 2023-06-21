@props([
    'rowCount' => 10,
])

<x-loading.visible display-type="block">
    <x-tables.desktop.skeleton.wallet-blocks :row-count="$rowCount" />

    <x-tables.mobile.skeleton.wallet-blocks />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
