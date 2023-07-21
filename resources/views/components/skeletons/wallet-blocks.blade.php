@props([
    'rowCount' => 10,
])

@if (! $this->isReady)
    <div wire:key="skeleton:blocks:not-ready">
        <x-tables.desktop.skeleton.wallet-blocks :row-count="$rowCount" />

        <x-tables.mobile.skeleton.wallet-blocks />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:blocks:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.wallet-blocks :row-count="$rowCount" />

        <x-tables.mobile.skeleton.wallet-blocks />
    </x-loading.visible>
@endif

<div wire:key="skeleton:blocks:hidden">
    <x-loading.hidden>
        {{ $slot }}
    </x-loading.hidden>
</div>
