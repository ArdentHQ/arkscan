<x-loading.visible>
    <x-tables.desktop.skeleton.blocks />

    <x-tables.mobile.skeleton.blocks />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
