@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :label="trans('tables.delegates.no_of_voters')"
    :attributes="$attributes"
>
    {{-- TODO --}}
    <x-number>{{ 0 }}</x-number>
</x-tables.rows.mobile.encapsulated.cell>
