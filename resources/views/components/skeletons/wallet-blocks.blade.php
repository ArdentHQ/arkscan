@props([
    'rowCount' => 10,
    'paginator' => null,
])

@if (! $this->isReady)
    <div wire:key="skeleton:blocks:not-ready">
        <x-tables.desktop.skeleton.wallet-blocks
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.wallet-blocks />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:blocks:ready"
        display-type="block"
    >
        <x-tables.desktop.skeleton.wallet-blocks
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.wallet-blocks />
    </x-loading.visible>

    <div wire:key="skeleton:blocks:hidden">
        <x-loading.hidden>
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
