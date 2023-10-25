@props([
    'rowCount' => 51,
])

@if (! $this->isReady || empty($this->delegates))
    <div wire:key="skeleton:delegates:not-ready">
        <x-tables.desktop.skeleton.delegates.monitor :row-count="$rowCount" />

        <x-tables.mobile.skeleton.delegates.monitor />
    </div>
@else
    {{ $slot }}
@endif
