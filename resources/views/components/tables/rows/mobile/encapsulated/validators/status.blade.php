@props([
    'model',
    'withoutLabel' => false,
])

<x-tables.rows.mobile.encapsulated.cell
    :label="$withoutLabel ? null : trans('tables.validators.status')"
    :attributes="$attributes"
>
    <span class="inline-block">
        <x-tables.rows.desktop.encapsulated.validators.validator-status :model="$model" />
    </span>
</x-tables.rows.mobile.encapsulated.cell>
