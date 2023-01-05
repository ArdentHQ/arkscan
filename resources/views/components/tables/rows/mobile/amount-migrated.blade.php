@props([
    'model',
])

<div class="items-center">
    @lang('labels.amount')

    <span class="flex px-1.5 py-1 font-semibold whitespace-nowrap rounded border-2 fiat-tooltip-sent text-theme-hint-600 border-theme-hint-100 dark:border-theme-hint-400">
        <span class="fiat-hint-migration" data-tippy-content="@lang ('pages.wallet.migration')">
            <x-ark-icon name="hint-small" size="xs" />
        </span>

        <span @if(Network::canBeExchanged()) data-tippy-content="{{ $model->amountFiat() }}" @endif>
            -
            <x-currency :currency="Network::currency()">{{ $model->amount() }}</x-currency>
        </span>
    </span>
</div>
