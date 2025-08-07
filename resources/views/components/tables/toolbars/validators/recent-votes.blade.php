@props(['votes'])

<x-tables.toolbars.toolbar
    :result-count="$votes->total()"
    :result-suffix="trans('pages.validators.recent-votes.results_suffix')"
    :breakpoint="false"
>
    <div class="flex space-x-3">
        <div class="flex-1">
            <x-tables.filters.validators.recent-votes />

            <x-tables.filters.validators.recent-votes mobile />
        </div>
    </div>
</x-general.encapsulated.table-header>
