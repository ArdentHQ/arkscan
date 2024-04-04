@props(['model'])

<x-tables.rows.mobile.encapsulated.cell :label="trans('tables.validators.missed_blocks')">
    <span class="inline-block">
        <x-tables.rows.desktop.encapsulated.validators.missed-blocks :model="$model" />
    </span>
</x-tables.rows.mobile.encapsulated.cell>
