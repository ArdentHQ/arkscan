@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :label="trans('tables.delegate-monitor.status')"
    :attributes="$attributes"
>
    <x-tables.rows.desktop.encapsulated.delegates.monitor.forging-status
        :model="$model"
        width=""
    />
</x-tables.rows.mobile.encapsulated.cell>
