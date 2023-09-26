@props([
    'rowCount' => 10,
])

@if (! $this->isReady)
    <div wire:key="skeleton:blocks:not-ready">
        <x-tables.desktop.skeleton.home.blocks :row-count="$rowCount" />

        <x-tables.mobile.skeleton.home.blocks />
    </div>
@else
    {{-- No loading state as it shows every 8-10 seconds --}}
    {{ $slot }}
@endif
