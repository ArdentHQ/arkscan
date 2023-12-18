@props(['exchange'])

<x-tables.rows.mobile.encapsulated.cell
    :attributes="$attributes"
    :label="trans('tables.exchanges.top_pairs')"
>
    <x-exchanges.pairs :exchange="$exchange" />
</x-tables.rows.mobile.encapsulated.cell>
