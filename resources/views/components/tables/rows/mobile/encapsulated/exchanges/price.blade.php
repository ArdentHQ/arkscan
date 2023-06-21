@props(['exchange'])

<div class="flex flex-col space-y-2 text-sm font-semibold flex-1">
    <span class="text-theme-secondary-600 dark:text-theme-secondary-500">
        @lang('general.exchange.price')
    </span>

    @if ($exchange->price)
        <span class="text-theme-secondary-900 dark:text-theme-secondary-200">
            {{ ExchangeRate::convertFiatToCurrency($exchange->price, 'USD', Settings::currency()) }}
        </span>
    @else
        <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
            @lang('general.na')
        </span>
    @endif
</div>
