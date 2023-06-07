@props([
    'model',
    'wallet' => null,
])

<div class="items-center">
    <span class="font-semibold">
        @lang('labels.amount')
    </span>

    <x-general.amount-fiat-tooltip
        :amount="$model->amountReceived($wallet?->address())"
        :fiat="$model->amountReceivedFiat($wallet?->address())"
        is-received
    />
</div>
