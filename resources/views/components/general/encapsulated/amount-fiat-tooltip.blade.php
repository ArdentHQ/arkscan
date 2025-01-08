@props([
    'transaction' => null,
    'wallet' => null,
    'isSent' => false,
    'isReceived' => false,
    'fiat' => null,
    'amount' => null,
    'amountForItself' => null,
    'class' => 'text-sm',
    'withoutStyling' => false,
])

@php
    $class = ['inline-flex items-center font-semibold', $class];
    $sentToSelfClass = null;

    $shouldShowFiat = $transaction && $transaction->shouldShowFiat();

    $isSentToSelf = $amountForItself !== null && $amountForItself > 0;
    if (! $withoutStyling) {
        if(! $isSent && ! $isReceived) {
            $class[] = 'text-theme-secondary-900 dark:text-theme-dark-50';
        }

        if($isSent || $isReceived) {
            $class[] = 'flex whitespace-nowrap rounded border';

            if ($isSentToSelf) {
                $class[] = 'pr-1.5';
            } else {
                $class[] = 'px-1.5 py-0.5';
            }
        }

        if ($wallet && $transaction && $transaction->isSentToSelf($wallet->address())) {
            $class[] = 'fiat-tooltip-sent text-theme-secondary-700 bg-theme-secondary-200 border-theme-secondary-200 dark:bg-transparent dark:border-theme-dark-700 dark:text-theme-dark-200 dim:border-theme-dim-700 dim:text-theme-dim-200 encapsulated-badge';

            $isSent = false;
            $isSentToSelf = true;
        } else {
            if ($isSent) {
                $class[] = 'fiat-tooltip-sent text-theme-orange-dark bg-theme-orange-light border-theme-orange-light dark:bg-transparent dark:border-[#AA6868] dark:text-[#F39B9B] dim:border-[#AB8282] dim:text-[#CAA0A0]';
            }

            if ($isReceived) {
                $class[] = 'fiat-tooltip-received text-theme-success-700 bg-theme-success-100 border-theme-success-100 dark:bg-transparent dark:border-theme-success-700 dark:text-theme-success-500';
            }
        }
    }
@endphp

<span {{ $attributes->class($class) }}>
    @if($amountForItself !== null && $amountForItself > 0)
        <div
            class="flex items-center px-1.5 mr-1.5 h-full py-[4.5px] text-[#A56D4C] bg-[#F6DFB5] dim:bg-[#AB8282] dark:bg-[#AA6868] dark:text-theme-dark-50"
            data-tippy-content="{{ trans('general.fiat_excluding_self', [
                'amount' => ExplorerNumberFormatter::currency($amountForItself, Network::currency())
            ]) }}"
        >
            <x-ark-icon
                name="hint-small"
                size="xs"
            />
        </div>
    @endif

    <span
        @if(Network::canBeExchanged() && $shouldShowFiat)
            data-tippy-content="{{ $fiat }}"
        @endif
    >
        {{ $isSent && ! $isSentToSelf ? '-' : ($isReceived ? '+' : '')}}

        @if (is_numeric($amount))
            {{ ExplorerNumberFormatter::networkCurrency($amount) }}
        @else
            {{ $amount }}
        @endif
    </span>
</span>
