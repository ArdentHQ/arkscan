@props(['model'])

<x-tables.rows.mobile.encapsulated.cell :label="trans('tables.delegates.percentage')">
    <x-percentage>{{ $model->votePercentage() }}</x-percentage>
</x-tables.rows.mobile.encapsulated.cell>
