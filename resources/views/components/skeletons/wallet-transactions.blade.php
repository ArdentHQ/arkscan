@props([
    'rowCount' => 10,
])

@php
    $targets = [
        'setIsReady',
        'filter',
        'selectAllFilters',
        '$set',
        'gotoPage',
        'page',
        'setPage',
        'setPerPage',
    ];
@endphp

@if (! $this->isReady)
    <div wire:key="skeleton:transactions:not-ready">
        <x-tables.desktop.skeleton.wallet-transactions :row-count="$rowCount" />

        <x-tables.mobile.skeleton.wallet-transactions />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:transactions:ready"
        display-type="block"
        :targets="$targets"
    >
        <x-tables.desktop.skeleton.wallet-transactions :row-count="$rowCount" />

        <x-tables.mobile.skeleton.wallet-transactions />
    </x-loading.visible>
@endif

<div wire:key="skeleton:transactions:hidden">
    <x-loading.hidden :targets="$targets">
        {{ $slot }}
    </x-loading.hidden>
</div>
