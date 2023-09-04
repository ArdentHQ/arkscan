@props([
    'rowCount' => 10,
])

@if (! $this->isReady)
    <div wire:key="skeleton:blocks:not-ready">
        <x-tables.desktop.skeleton.blocks :row-count="$rowCount" />

        <x-tables.mobile.skeleton.blocks />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:blocks:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.blocks :row-count="$rowCount" />

        <x-tables.mobile.skeleton.blocks />
    </x-loading.visible>

    <div wire:key="skeleton:blocks:hidden">
        <x-loading.hidden>
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
