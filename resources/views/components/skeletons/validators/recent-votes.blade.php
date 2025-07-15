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
    <x-tables.toolbars.validators.recent-votes />

    <div wire:key="skeleton:recent-votes:not-ready">
        <x-tables.desktop.skeleton.validators.recent-votes
            :row-count="$rowCount"
            :paginator="$paginator"
            :is-ready="$isReady"
        />

        <x-tables.mobile.skeleton.validators.recent-votes />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:recent-votes:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.validators.recent-votes
            :row-count="$rowCount"
            :paginator="$paginator"
            :is-ready="$isReady"
        />

        <x-tables.mobile.skeleton.validators.recent-votes />
    </x-loading.visible>

    <div wire:key="skeleton:recent-votes:hidden">
        <x-loading.hidden>
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
