@props([
    'rowCount' => 10,
    'paginator' => null,
])

@if (! $this->isReady)
    <div wire:key="skeleton:voters:not-ready">
        <x-tables.desktop.skeleton.wallet-voters
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.wallet-voters />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:voters:ready"
        display-type="block"
        wire:target="setPage,gotoPage,setPerPage"
    >
        <x-tables.desktop.skeleton.wallet-voters
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.wallet-voters />
    </x-loading.visible>

    <div wire:key="skeleton:voters:hidden">
        <x-loading.hidden wire:target="setPage,gotoPage,setPerPage">
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
