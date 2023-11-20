@props([
    'model',
    'wallet' => null,
    'withoutFee' => false,
    'withNetworkCurrency' => false,
])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center leading-4.25') }}>
    <div class="text-sm font-semibold leading-4.25 dark:text-theme-dark-500">
        @lang('tables.transactions.amount', ['currency' => Network::currency()])
    </div>

    <div class="inline-block leading-4.25">
        <x-tables.rows.desktop.encapsulated.amount
            :model="$model"
            :wallet="$wallet"
            :without-fee="$withoutFee"
            :with-network-currency="$withNetworkCurrency"
        />
    </div>
</div>
