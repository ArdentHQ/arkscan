@props([
    'rowCount' => 10,
    'paginator' => null,
])

@if (! $this->missedBlocksIsReady)
    <div wire:key="skeleton:missed-blocks:not-ready">
        <x-tables.desktop.skeleton.validators.missed-blocks
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.validators.missed-blocks />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:missed-blocks:ready"
        display-type="block"
        wire:target="setPage,gotoPage,setPerPage"
    >
        <x-tables.desktop.skeleton.validators.missed-blocks
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.validators.missed-blocks />
    </x-loading.visible>

    <div wire:key="skeleton:missed-blocks:hidden">
        <x-loading.hidden wire:target="setPage,gotoPage,setPerPage">
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
