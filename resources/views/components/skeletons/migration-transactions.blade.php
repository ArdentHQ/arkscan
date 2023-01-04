<x-loading.visible>
    <x-tables.desktop.skeleton.migration-transactions />

    <x-tables.mobile.skeleton.migration-transactions />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
