@props([
    'model',
    'isReceived' => false,
    'isSent' => false,
])

<div class="md:space-y-1 md-lg:space-y-0">
    <x-general.amount-fiat-tooltip
        :amount="$model->amount()"
        :fiat="$model->amountFiat(true)"
        :is-received="$isReceived"
        :is-sent="$isSent"
    />

    <x-tables.rows.desktop.encapsulated.fee
        :model="$model"
        class="hidden text-xs md:block text-theme-secondary-700 md-lg:hidden"
        without-styling
    />
</div>
