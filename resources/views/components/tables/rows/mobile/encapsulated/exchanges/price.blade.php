@props(['exchange'])

<div class="flex flex-col flex-1 space-y-2 text-sm font-semibold">
    <span class="text-theme-secondary-600 dark:text-theme-secondary-500 leading-4.25">
        @lang('tables.exchanges.price')
    </span>

    @if ($exchange->price)
        <span class="text-theme-secondary-900 dark:text-theme-secondary-200 leading-4.25">
            {{ ExchangeRate::convertFiatToCurrency($exchange->price, 'USD', Settings::currency()) }}
        </span>
    @else
        <span class="text-theme-secondary-500 dark:text-theme-secondary-700 leading-4.25">
            @lang('general.na')
        </span>
    @endif
</div>
