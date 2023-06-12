@props([
    'rowCount' => 10,
])

<x-loading.visible display-type="block">
    <x-tables.desktop.skeleton.top-accounts :row-count="$rowCount" />

    <x-tables.mobile.skeleton.top-accounts />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
