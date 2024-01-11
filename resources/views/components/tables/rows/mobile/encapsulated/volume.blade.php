@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :attributes="$attributes"
    :label="trans('tables.blocks.volume', ['currency' => Network::currency()])"
>
    <x-tables.rows.desktop.encapsulated.volume :model="$model" />
</x-tables.rows.mobile.encapsulated.cell>
