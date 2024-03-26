@props(['blocks'])

<x-tables.toolbars.toolbar
    :result-count="$blocks->total()"
    :result-suffix="trans('pages.validators.missed-blocks.results_suffix')"
    :breakpoint="false"
/>
