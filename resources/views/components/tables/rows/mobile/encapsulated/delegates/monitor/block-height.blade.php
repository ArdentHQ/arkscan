@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :label="trans('tables.delegate-monitor.block_height')"
    :attributes="$attributes"
>
    <x-tables.rows.desktop.encapsulated.delegates.monitor.block-height :model="$model" />
</x-tables.rows.mobile.encapsulated.cell>
