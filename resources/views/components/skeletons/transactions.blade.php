<x-loading.visible>
    <x-tables.desktop.skeleton.transactions />

    <x-tables.mobile.skeleton.transactions />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
