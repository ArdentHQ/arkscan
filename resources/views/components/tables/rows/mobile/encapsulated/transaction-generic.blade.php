@props(['model'])

<x-tables.rows.mobile.encapsulated.cell :attributes="$attributes">
    <x-slot name="label">
        <x-general.encapsulated.transaction-type :transaction="$model" />
    </x-slot>

    <x-tables.rows.desktop.encapsulated.addressing-generic :model="$model" />
</x-tables.rows.mobile.encapsulated.cell>
