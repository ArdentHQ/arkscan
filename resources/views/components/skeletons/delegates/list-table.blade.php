@props([
    'rowCount' => 10,
    'paginator' => null,
])

@if (! $this->isReady)
    <div wire:key="skeleton:delegates:not-ready">
        <x-tables.desktop.skeleton.delegates.list-table
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.delegates.list-table />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:delegates:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.delegates.list-table
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.delegates.list-table />
    </x-loading.visible>

    <div wire:key="skeleton:delegates:hidden">
        <x-loading.hidden>
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
