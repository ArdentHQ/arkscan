@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :label="trans('tables.validator-monitor.status')"
    :attributes="$attributes"
>
    <x-tables.rows.desktop.encapsulated.validators.monitor.forging-status
        :model="$model"
        width=""
    />
</x-tables.rows.mobile.encapsulated.cell>
