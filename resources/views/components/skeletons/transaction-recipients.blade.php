@props([
    'rowCount' => 10,
    'paginator' => null,
])

@if (! $this->isReady)
    <div wire:key="skeleton:transaction-recipients:not-ready">
        <x-tables.desktop.skeleton.transaction-recipients
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.transaction-recipients />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:transaction-recipients:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.transaction-recipients
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.transaction-recipients />
    </x-loading.visible>

    <div wire:key="skeleton:transaction-recipients:hidden">
        <x-loading.hidden>
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
