@props([
    'blocks' => null,
])

<x-tables.toolbars.toolbar
    :result-count="$blocks?->total() ?? 0"
    :result-suffix="trans('pages.validators.missed-blocks.results_suffix')"
    :breakpoint="false"
/>
