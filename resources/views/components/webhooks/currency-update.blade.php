@props(['currency'])

<div
    x-data="{
        currentCurrency: '{{ $currency }}',

        init() {
            Webhook.listen('currency-update.{{ $currency }}', 'CurrencyUpdate', 'reloadPriceTicker');

            Livewire.on('currencyChanged', (currency) => {
                Webhook.remove(`currency-update.${this.currentCurrency}`, 'CurrencyUpdate', 'reloadPriceTicker');
                Webhook.listen(`currency-update.${currency}`, 'CurrencyUpdate', 'reloadPriceTicker');

                this.currentCurrency = currency;
            });
        },
    }"
></div>
