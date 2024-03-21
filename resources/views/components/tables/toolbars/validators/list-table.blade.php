@props(['validators'])

<x-tables.toolbars.toolbar
    :result-count="$validators->total()"
    :breakpoint="false"
>
    <div class="flex space-x-3">
        <div class="flex-1">
            <x-tables.filters.validators />

            <x-tables.filters.validators mobile />
        </div>
    </div>
</x-general.encapsulated.table-header>
