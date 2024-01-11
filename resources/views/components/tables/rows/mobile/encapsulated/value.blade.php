@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :attributes="$attributes"
    :label="trans('tables.blocks.value', ['currency' => Settings::currency()])"
>
    <x-tables.rows.desktop.encapsulated.value :model="$model" />
</x-tables.rows.mobile.encapsulated.cell>
