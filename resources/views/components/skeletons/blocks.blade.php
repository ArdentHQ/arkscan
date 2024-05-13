@props([
    'rowCount' => 10,
    'paginator' => null,
])

@if (! $this->isReady)
    <div wire:key="skeleton:blocks:not-ready">
        <x-tables.desktop.skeleton.blocks
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.blocks />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:blocks:ready"
        display-type="block"
        wire:target="setPage,gotoPage"
    >
        <x-tables.desktop.skeleton.blocks
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.blocks />
    </x-loading.visible>

    <div wire:key="skeleton:blocks:hidden">
        <x-loading.hidden wire:target="setPage,gotoPage">
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
