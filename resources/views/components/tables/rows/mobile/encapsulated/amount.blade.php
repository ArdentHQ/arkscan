@props([
    'model',
    'wallet' => null,
    'withoutFee' => false,
    'withNetworkCurrency' => false,
])

<x-tables.rows.mobile.encapsulated.cell
    :attributes="$attributes"
    :label="trans('tables.transactions.amount', ['currency' => Network::currency()])"
>
    <x-tables.rows.desktop.encapsulated.amount
        :model="$model"
        :wallet="$wallet"
        :without-fee="$withoutFee"
        :with-network-currency="$withNetworkCurrency"
    />
</x-tables.rows.mobile.encapsulated.cell>
