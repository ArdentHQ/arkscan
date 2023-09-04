@props(['model'])

<x-tables.rows.mobile.encapsulated.cell :label="trans('tables.delegates.percentage')">
    <x-percentage>{{ $model->votesPercentage() }}</x-percentage>
</x-tables.rows.mobile.encapsulated.cell>
