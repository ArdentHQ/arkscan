@props([
    'model',
    'wallet' => null,
    'isSent' => null,
    'isReceived' => null,
])

<div class="space-y-2 sm:flex sm:flex-col sm:justify-center">
    <div class="text-sm font-semibold dark:text-theme-secondary-500">
        @lang('tables.transactions.amount', ['currency' => Network::currency()])
    </div>

    <div class="inline-block">
        <x-tables.rows.desktop.encapsulated.amount
            :model="$model"
            :is-received="(($wallet && $model->isReceived($wallet->address())) || $isReceived === true) && $isSent !== true"
            :is-sent="(($wallet && $model->isSent($wallet->address())) || $isSent === true) && $isReceived !== true"
        />
    </div>
</div>
