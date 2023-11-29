@props(['model'])

<x-tables.rows.mobile.encapsulated.cell
    :attributes="$attributes"
    :label="trans('tables.wallets.balance_currency', ['currency' => Network::currency()])"
>
    <x-tables.rows.desktop.encapsulated.balance :model="$model" />
</x-tables.rows.mobile.encapsulated.cell>
