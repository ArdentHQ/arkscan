@props([
    'rowCount' => 10,
    'paginator' => null,
])

@if (! $this->isReady)
    <div wire:key="skeleton:transactions:not-ready">
        <x-tables.desktop.skeleton.wallet.transactions
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.wallet.transactions />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:transactions:ready"
        display-type="block"
        wire:target="setPage,gotoPage,setPerPage"
    >
        <x-tables.desktop.skeleton.wallet.transactions
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.wallet.transactions />
    </x-loading.visible>

    <div wire:key="skeleton:transactions:hidden">
        <x-loading.hidden wire:target="setPage,gotoPage,setPerPage">
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
