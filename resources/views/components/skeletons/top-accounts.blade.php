@props([
    'rowCount' => 10,
    'paginator' => null,
])

@if (! $this->isReady)
    <div wire:key="skeleton:top-accounts:not-ready">
        <x-tables.desktop.skeleton.top-accounts
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.top-accounts />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:top-accounts:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.top-accounts
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.top-accounts />
    </x-loading.visible>

    <div wire:key="skeleton:top-accounts:hidden">
        <x-loading.hidden>
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
