@props(['delegates'])

<x-tables.toolbars.toolbar
    :result-count="$delegates->total()"
    :breakpoint="false"
>
    <div class="flex space-x-3">
        <div class="flex-1">
            <x-tables.filters.delegates />

            <x-tables.filters.delegates mobile />
        </div>
    </div>
</x-general.encapsulated.table-header>
