@props([
    'transaction' => null,
    'wallet' => null,
    'isSent' => false,
    'isReceived' => false,
    'fiat' => null,
    'amount' => null,
    'amountForItself' => null,
    'class' => 'text-sm leading-4.25',
    'withoutStyling' => false,
])

@php
    $class = ['font-semibold', $class];

    $isSentToSelf = false;
    if (! $withoutStyling) {
        if ($transaction && $wallet) {
            $isSentToSelf = $transaction->isSentToSelf($wallet->address());
        }
        if(! $isSent && ! $isReceived) {
            $class[] = 'text-theme-secondary-900 dark:text-theme-dark-50';
        }

        if($isSent || $isReceived) {
            $class[] = 'flex px-1.5 py-0.5 whitespace-nowrap rounded border';
        }

        if($isSent && ! $isSentToSelf) {
            $class[] = 'fiat-tooltip-sent text-theme-orange-dark bg-theme-orange-light border-theme-orange-light dark:bg-transparent dark:border-[#AA6868] dark:text-[#F39B9B]';
        }

        if($isReceived || $isSentToSelf) {
            $class[] = 'fiat-tooltip-received text-theme-success-700 bg-theme-success-100 border-theme-success-100 dark:bg-transparent dark:border-theme-success-700 dark:text-theme-success-500';
        }
    }
@endphp

<span {{ $attributes->class($class) }}>
    @if($amountForItself !== null && $amountForItself > 0)
        <span
            class="fiat-hint"
            data-tippy-content="{{ trans('general.fiat_excluding_itself', [
                'amount' => ExplorerNumberFormatter::currency($amountForItself, Network::currency())
            ]) }}"
        >
            <x-ark-icon
                name="hint-small"
                size="xs"
            />
        </span>
    @endif

    <span
        @if(Network::canBeExchanged())
            data-tippy-content="{{ $fiat }}"
        @endif
    >
        {{ $isSent && ! $isSentToSelf ? '-' : ($isReceived || $isSentToSelf ? '+' : '')}}

        @if (is_numeric($amount))
            {{ ExplorerNumberFormatter::networkCurrency($amount) }}
        @else
            {{ $amount }}
        @endif
    </span>
</span>
