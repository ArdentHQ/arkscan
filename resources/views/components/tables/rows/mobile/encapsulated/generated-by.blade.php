@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :label="trans('tables.blocks.generated_by')"
    :attributes="$attributes"
>
    <x-general.identity
        :model="$model"
        without-reverse
    />
</x-tables.rows.mobile.encapsulated.cell>
