<div wire:key="event:currency-update:{{ $currency }}">
    <script>
        // console.log('asd');
        window.addEventListener('DOMContentLoaded', function () {
            Webhook.listen('currency-update.{{ $currency }}', 'CurrencyUpdate', 'reloadPriceTicker');

            let currentCurrency = '{{ $currency }}';
            Livewire.on('currencyChanged', function (currency) {
                Webhook.remove(`currency-update.${currentCurrency}`, 'CurrencyUpdate', 'reloadPriceTicker');
                Webhook.listen(`currency-update.${currency}`, 'CurrencyUpdate', 'reloadPriceTicker');

                currentCurrency = currency;
            });
        });
    </script>
</div>
