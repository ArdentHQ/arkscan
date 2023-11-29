@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :attributes="$attributes"
    :label="trans('tables.transactions.fee', ['currency' => Network::currency()])"
>
    <x-tables.rows.desktop.encapsulated.fee :model="$model" />
</x-tables.rows.mobile.encapsulated.cell>
