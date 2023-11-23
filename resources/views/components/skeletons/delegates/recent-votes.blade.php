@props([
    'rowCount' => 10,
    'paginator' => null,
])

@if (! $this->isReady)
    <div wire:key="skeleton:recent-votes:not-ready">
        <x-tables.desktop.skeleton.delegates.recent-votes
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.delegates.recent-votes />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:recent-votes:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.delegates.recent-votes
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.delegates.recent-votes />
    </x-loading.visible>

    <div wire:key="skeleton:recent-votes:hidden">
        <x-loading.hidden>
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
