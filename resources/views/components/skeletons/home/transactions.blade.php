@props([
    'rowCount' => 10,
])

@if (! $this->isReady)
    <div wire:key="skeleton:transactions:not-ready">
        <x-tables.desktop.skeleton.home.transactions :row-count="$rowCount" />

        <x-tables.mobile.skeleton.home.transactions />
    </div>
@else
    {{-- No loading state as it shows every 8-10 seconds --}}
    {{ $slot }}
@endif
