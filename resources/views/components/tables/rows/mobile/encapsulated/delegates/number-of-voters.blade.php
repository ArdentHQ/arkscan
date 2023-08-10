@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :label="trans('tables.delegates.no_of_voters')"
    :attributes="$attributes"
>
    <x-number>{{ $model->voterCount() }}</x-number>
</x-tables.rows.mobile.encapsulated.cell>
