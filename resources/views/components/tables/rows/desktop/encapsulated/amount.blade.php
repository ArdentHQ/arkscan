@props([
    'model',
    'wallet' => null,
    'withoutFee' => false,
    'withNetworkCurrency' => false,
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
    <div class="inline-block">
        <x-general.encapsulated.amount-fiat-tooltip
            :amount="$amount"
            :fiat="$amountFiat"
            :is-received="$isReceived"
            :is-sent="$isSent"
        />

        @if ($withNetworkCurrency)
            <span class="text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-secondary-200">
                {{ Network::currency() }}
            </span>
        @endif
    </div>

    @unless ($withoutFee)
        <x-tables.rows.desktop.encapsulated.fee
            :model="$model"
            class="hidden text-xs md:block text-theme-secondary-700 md-lg:hidden"
            without-styling
        />
    @endunless
</div>
