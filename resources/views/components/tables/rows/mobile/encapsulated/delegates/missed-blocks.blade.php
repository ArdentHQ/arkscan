@props(['model'])

<x-tables.rows.mobile.encapsulated.cell :label="trans('tables.delegates.missed_blocks')">
    <span class="inline-block">
        <x-tables.rows.desktop.encapsulated.delegates.missed-blocks :model="$model" />
    </span>
</x-tables.rows.mobile.encapsulated.cell>
