@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :label="trans('tables.blocks.generated_by')"
    :attributes="$attributes"
>
    <x-general.identity :model="$model">
        <x-truncate-middle>
            {{ $model->address() }}
        </x-truncate-middle>
    </x-general.identity>
</x-tables.rows.mobile.encapsulated.cell>
