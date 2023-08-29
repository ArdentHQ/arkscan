@props(['transactions'])

<x-tables.toolbars.toolbar
    :result-count="$transactions->total()"
    :breakpoint="false"
>
    <div class="flex space-x-3">
        <div class="flex-1">
            {{-- <x-tables.filters.transactions />

            <x-tables.filters.transactions mobile /> --}}
        </div>
    </div>
</x-general.encapsulated.table-header>
