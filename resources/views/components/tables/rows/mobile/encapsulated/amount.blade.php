@props([
    'model',
    'wallet' => null,
    'isSent' => null,
    'isReceived' => null,
])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center') }}>
    <div class="text-sm font-semibold leading-[17px] dark:text-theme-secondary-500">
        @lang('tables.transactions.amount', ['currency' => Network::currency()])
    </div>

    <div class="inline-block">
        <x-tables.rows.desktop.encapsulated.amount
            :model="$model"
            :wallet="$wallet"
        />
    </div>
</div>
