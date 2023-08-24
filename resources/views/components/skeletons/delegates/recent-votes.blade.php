@props([
    'rowCount' => 10,
])

@if (! $this->isReady)
    <div wire:key="skeleton:recent-votes:not-ready">
        <x-tables.desktop.skeleton.delegates.recent-votes :row-count="$rowCount" />

        <x-tables.mobile.skeleton.delegates.recent-votes />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:recent-votes:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.delegates.recent-votes :row-count="$rowCount" />

        <x-tables.mobile.skeleton.delegates.recent-votes />
    </x-loading.visible>
@endif

<div wire:key="skeleton:recent-votes:hidden">
    <x-loading.hidden>
        {{ $slot }}
    </x-loading.hidden>
</div>
