@props([
    'isSent' => false,
    'isReceived' => false,
    'fiat' => null,
    'amount' => null,
    'amountForItself' => null,
])

@php
    $class = '';

    if($isSent || $isReceived) {
        $class .= ' flex px-1.5 py-1 font-semibold whitespace-nowrap rounded border-2';
    }

    if($isSent) {
        $class .= ' fiat-tooltip-sent text-theme-danger-400 border-theme-danger-100 dark:border-theme-danger-400';
    }

    if($isReceived) {
        $class .= ' fiat-tooltip-received text-theme-success-600 border-theme-success-200 dark:border-theme-success-600';
    }
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>
    @if($amountForItself !== null && $amountForItself > 0)
        <span class="fiat-hint" data-tippy-content="{{ trans('general.fiat_excluding_itself', [
            'amount' => ExplorerNumberFormatter::currency($amountForItself, Network::currency())
        ]) }}">
            <x-ark-icon name="hint-small" size="xs" />
        </span>
    @endif

    <span @if(Network::canBeExchanged()) data-tippy-content="{{ $fiat }}" @endif>
        {{ $isSent ? '-' : ($isReceived ? '+' : '')}}
        <x-currency :currency="Network::currency()">{{ $amount }}</x-currency>
    </span>
</span>
