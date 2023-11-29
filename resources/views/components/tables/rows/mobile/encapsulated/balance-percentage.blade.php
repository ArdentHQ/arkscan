@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :attributes="$attributes"
    :label="trans('labels.percentage')"
>
    <x-percentage>{{ $model->balancePercentage() }}</x-percentage>
</x-tables.rows.mobile.encapsulated.cell>
