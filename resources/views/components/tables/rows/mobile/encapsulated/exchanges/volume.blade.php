@props(['exchange'])

<div class="flex flex-col space-y-2 text-sm font-semibold">
    <span class="text-theme-secondary-600 dark:text-theme-secondary-500">
        @lang('general.exchange.volume')
    </span>

    @if ($exchange->volume)
        <span class="text-theme-secondary-900 dark:text-theme-secondary-200">
            {{ ExchangeRate::convertFiatToCurrency($exchange->volume, 'USD', Settings::currency(), 2) }}
        </span>
    @else
        <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
            @lang('general.na')
        </span>
    @endif
</div>
