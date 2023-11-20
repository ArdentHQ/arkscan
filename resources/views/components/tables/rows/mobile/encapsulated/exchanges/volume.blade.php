@props(['exchange'])

<div class="flex flex-col space-y-2 text-sm font-semibold">
    <span class="text-theme-secondary-600 leading-4.25 dark:text-theme-dark-500">
        @lang('tables.exchanges.volume')
    </span>

    @if ($exchange->volume)
        <span class="text-theme-secondary-900 leading-4.25 dark:text-theme-dark-200">
            {{ ExchangeRate::convertFiatToCurrency($exchange->volume, 'USD', Settings::currency(), 2) }}
        </span>
    @else
        <span class="text-theme-secondary-500 leading-4.25 dark:text-theme-dark-700">
            @lang('general.na')
        </span>
    @endif
</div>
