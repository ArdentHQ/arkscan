@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :attributes="$attributes"
    :label="trans('tables.blocks.total_reward', ['currency' => Network::currency()])"
>
    <x-tables.rows.desktop.encapsulated.reward :model="$model" />
</x-tables.rows.mobile.encapsulated.cell>
