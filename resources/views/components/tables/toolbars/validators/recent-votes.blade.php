@props([
    'votes' => null,
])

<x-tables.toolbars.toolbar
    :result-count="$votes?->total() ?? 0"
    :result-suffix="trans('pages.validators.recent-votes.results_suffix')"
    :breakpoint="false"
>
    <div class="flex space-x-3">
        <div class="flex-1">
            @if ($votes === null)
                <x-general.dropdown.filter
                    without-text
                    disabled
                />
            @else
                <x-tables.filters.recent-votes />

                <x-tables.filters.recent-votes mobile />
            @endif
        </div>
    </div>
</x-general.encapsulated.table-header>
