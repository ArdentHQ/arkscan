@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :label="trans('tables.delegate-monitor.time_to_forge')"
    :attributes="$attributes"
>
    <x-tables.rows.desktop.encapsulated.delegates.monitor.time-to-forge :model="$model" />
</x-tables.rows.mobile.encapsulated.cell>
