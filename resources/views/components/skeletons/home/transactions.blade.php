@props([
    'rowCount' => 10,
])

@if (! $this->isReady)
    <div wire:key="skeleton:transactions:not-ready">
        <x-tables.desktop.skeleton.home.transactions :row-count="$rowCount" />

        <x-tables.mobile.skeleton.home.transactions />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:transactions:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.home.transactions :row-count="$rowCount" />

        <x-tables.mobile.skeleton.home.transactions />
    </x-loading.visible>

    <div wire:key="skeleton:transactions:hidden">
        <x-loading.hidden>
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
