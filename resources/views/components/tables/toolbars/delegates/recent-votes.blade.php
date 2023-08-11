@props(['votes'])

<x-tables.toolbars.toolbar
    :result-count="$votes->total()"
    :breakpoint="false"
>
    <div class="flex space-x-3">
        <div class="flex-1">
            <x-tables.filters.recent-votes />

            <x-tables.filters.recent-votes mobile />
        </div>
    </div>
</x-general.encapsulated.table-header>
