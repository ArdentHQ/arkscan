@props([
    'model',
    'wallet' => null,
])

<div class="items-center">
    @lang('labels.amount')

    <x-general.amount-fiat-tooltip
        :amount="$model->amountReceived($wallet?->address())"
        :fiat="$model->amountReceivedFiat($wallet?->address())"
        is-received
    />
</div>
