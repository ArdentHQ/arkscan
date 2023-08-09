@props(['blocks'])

<x-tables.toolbars.toolbar
    :result-count="$blocks->total()"
    :breakpoint="false"
/>
