@props([
    'model',
    'wallet' => null,
    'withoutFee' => false,
    'withNetworkCurrency' => false,
    'breakpoint' => 'md-lg',
])

@php
    $isReceived = $wallet && ! $model->isSent($wallet->address());
    $isSent = $wallet && $model->isSent($wallet->address());

    $amount = $model->amount();
    $amountFiat = $model->amountFiat(true);

    if ($wallet && ($isReceived || $model->isSentToSelf($wallet->address()))) {
        $amount = $model->amountReceived($wallet?->address());
        $amountFiat = $model->amountReceivedFiat($wallet?->address());
    }

    $feeBreakpointClass = [
        'md-lg' => 'md-lg:hidden',
        'lg' => 'lg:hidden',
        'xl' => 'xl:hidden',
    ][$breakpoint] ?? 'md-lg:hidden';

    $containerBreakpointClass = [
        'md-lg' => 'md-lg:space-y-0',
        'lg' => 'lg:space-y-0',
        'xl' => 'xl:space-y-0',
    ][$breakpoint] ?? 'md-lg:space-y-0';
@endphp

<div @class([
    'flex flex-col md:space-y-1',
    $containerBreakpointClass,
])>
    <div class="inline-block leading-4.25">
        <x-general.encapsulated.amount-fiat-tooltip
            :amount="$amount"
            :fiat="$amountFiat"
            :is-received="$isReceived"
            :is-sent="$isSent"
            :transaction="$model"
            :wallet="$wallet"
        />

        @if ($withNetworkCurrency)
            <span class="text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-200">
                {{ Network::currency() }}
            </span>
        @endif
    </div>

    @unless ($withoutFee)
        <x-tables.rows.desktop.encapsulated.fee
            :model="$model"
            :class="Arr::toCssClasses([
                'hidden text-xs md:block text-theme-secondary-700 dark:text-theme-dark-200',
                $feeBreakpointClass,
            ])"
            without-styling
        />
    @endunless
</div>
