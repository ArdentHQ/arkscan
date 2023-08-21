@props(['blocks'])

<x-tables.toolbars.toolbar
    :result-count="$blocks->total()"
    :result-suffix="trans('pages.delegates.missed-blocks.results_suffix')"
    :breakpoint="false"
/>
