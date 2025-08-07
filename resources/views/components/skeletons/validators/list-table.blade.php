@props([
    'rowCount' => 10,
    'paginator' => null,
])

@if (! $this->validatorsIsReady)
    <div wire:key="skeleton:validators:not-ready">
        <x-tables.desktop.skeleton.validators.list-table
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.validators.list-table />
    </div>
@else
    <x-loading.visible
        wire:key="skeleton:validators:ready"
        display-type="block"
        wire:target="setPage,gotoPage,setPerPage,filters"
    >
        <x-tables.desktop.skeleton.validators.list-table
            :row-count="$rowCount"
            :paginator="$paginator"
        />

        <x-tables.mobile.skeleton.validators.list-table />
    </x-loading.visible>

    <div wire:key="skeleton:validators:hidden">
        <x-loading.hidden wire:target="setPage,gotoPage,setPerPage,filters">
            {{ $slot }}
        </x-loading.hidden>
    </div>
@endif
