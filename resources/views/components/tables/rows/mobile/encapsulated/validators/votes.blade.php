@props(['model'])

<x-tables.rows.mobile.encapsulated.cell :label="trans('tables.validators.votes', ['currency' => Network::currency()])">
    {{ number_format($model->votes()) }}
</x-tables.rows.mobile.encapsulated.cell>
