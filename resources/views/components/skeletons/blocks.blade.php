<x-loading.visible>
    @isset($withoutGenerator)
        <x-tables.desktop.skeleton.blocks without-generator />

        <x-tables.mobile.skeleton.blocks without-generator />
    @else
        <x-tables.desktop.skeleton.blocks />

        <x-tables.mobile.skeleton.blocks />
    @endif
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
