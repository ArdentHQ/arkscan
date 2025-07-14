@props([
    'rowCount' => 10,
    'paginator' => null,
    'isReady' => null,
])

@php
    if ($isReady === null) {
        $isReady = $this->isReady;
    }
@endphp

@if (! $isReady)
    <div wire:key="skeleton:missed-blocks:not-ready">
        <x-tables.desktop.skeleton.validators.missed-blocks
            :row-count="$rowCount"
            :paginator="$paginator"
            :is-ready="$isReady"
        />

        <x-tables.mobile.skeleton.validators.missed-blocks />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:missed-blocks:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.validators.missed-blocks
            :row-count="$rowCount"
            :paginator="$paginator"
            :is-ready="$isReady"
        />

        <x-tables.mobile.skeleton.validators.missed-blocks />
    </x-loading.visible>

    <div wire:key="skeleton:missed-blocks:hidden">
        <x-loading.hidden>
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
