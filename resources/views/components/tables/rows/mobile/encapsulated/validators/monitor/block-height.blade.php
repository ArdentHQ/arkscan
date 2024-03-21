@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :label="trans('tables.validator-monitor.block_height')"
    :attributes="$attributes"
>
    <x-tables.rows.desktop.encapsulated.validators.monitor.block-height :model="$model" />
</x-tables.rows.mobile.encapsulated.cell>
