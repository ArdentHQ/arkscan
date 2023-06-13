@props([
    'model',
    'wallet' => null,
])

@php
    $isReceived = $wallet && ! $model->isSent($wallet->address());
    $isSent = $wallet && $model->isSent($wallet->address());

    $amount = $model->amount();
    $amountFiat = $model->amountFiat(true);

    if ($wallet) {
        if ($isReceived) {
            $amount = $model->amountReceived($wallet?->address());
            $amountFiat = $model->amountReceivedFiat($wallet?->address());
        } else if ($wallet && $model->isSentToSelf($wallet->address())) {
            $amount = $model->amountExcludingItself();
            $amountFiat = $model->amountFiatExcludingItself();
        }
    }
@endphp

<div class="md:space-y-1 md-lg:space-y-0">
    <x-general.encapsulated.amount-fiat-tooltip
        :amount="$amount"
        :fiat="$amountFiat"
        :is-received="$isReceived"
        :is-sent="$isSent"
    />

    <x-tables.rows.desktop.encapsulated.fee
        :model="$model"
        class="hidden text-xs md:block text-theme-secondary-700 md-lg:hidden"
        without-styling
    />
</div>
