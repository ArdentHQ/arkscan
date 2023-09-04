@props([
    'model',
    'withoutLabel' => false,
])

<x-tables.rows.mobile.encapsulated.cell
    :label="$withoutLabel ? null : trans('tables.delegates.status')"
    :attributes="$attributes"
>
    <span class="inline-block">
        <x-tables.rows.desktop.encapsulated.delegates.delegate-status :model="$model" />
    </span>
</x-tables.rows.mobile.encapsulated.cell>
