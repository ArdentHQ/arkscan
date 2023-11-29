@props(['exchange'])

<x-tables.rows.mobile.encapsulated.cell
    :attributes="$attributes"
    :label="trans('tables.exchanges.price')"
>
    @if ($exchange->price)
        <span class="text-theme-secondary-900 leading-4.25 dark:text-theme-dark-50">
            {{ ExchangeRate::convertFiatToCurrency($exchange->price, 'USD', Settings::currency()) }}
        </span>
    @else
        <span class="text-theme-secondary-500 leading-4.25 dark:text-theme-dark-500">
            @lang('general.na')
        </span>
    @endif
</x-tables.rows.mobile.encapsulated.cell>
