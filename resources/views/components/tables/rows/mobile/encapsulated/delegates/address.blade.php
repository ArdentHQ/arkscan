@props([
    'model',
    'withoutLabel' => false,
])

<x-tables.rows.mobile.encapsulated.cell
    :label="$withoutLabel ? null : trans('tables.delegates.delegate')"
    :attributes="$attributes"
>
    <x-general.identity
        :model="$model"
        without-reverse
        without-icon
    />
</x-tables.rows.mobile.encapsulated.cell>
