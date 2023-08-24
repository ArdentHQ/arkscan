@props([
    'rowCount' => 10,
])

@if (! $this->isReady)
    <div wire:key="skeleton:missed-blocks:not-ready">
        <x-tables.desktop.skeleton.delegates.missed-blocks :row-count="$rowCount" />

        <x-tables.mobile.skeleton.delegates.missed-blocks />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:missed-blocks:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.delegates.missed-blocks :row-count="$rowCount" />

        <x-tables.mobile.skeleton.delegates.missed-blocks />
    </x-loading.visible>
@endif

<div wire:key="skeleton:missed-blocks:hidden">
    <x-loading.hidden>
        {{ $slot }}
    </x-loading.hidden>
</div>
