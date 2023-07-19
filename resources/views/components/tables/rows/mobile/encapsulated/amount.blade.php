@props([
    'model',
    'wallet' => null,
    'withoutFee' => false,
    'withNetworkCurrency' => false,
])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center') }}>
    <div class="text-sm font-semibold leading-[17px] dark:text-theme-secondary-500">
        @lang('tables.transactions.amount', ['currency' => Network::currency()])
    </div>

    <div class="inline-block">
        <x-tables.rows.desktop.encapsulated.amount
            :model="$model"
            :wallet="$wallet"
            :without-fee="$withoutFee"
            :with-network-currency="$withNetworkCurrency"
        />
    </div>
</div>
