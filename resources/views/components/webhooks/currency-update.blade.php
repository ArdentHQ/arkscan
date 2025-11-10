@props(['currency'])

@if (config('broadcasting.default') === 'reverb')
    <div
        x-data="{
            currentCurrency: '{{ $currency }}',

            init() {
                {{-- TODO: Remove this whole file once we have fully migrated to Inertia --}}
                if (!window.Webhook) return;
                
                Webhook.listen('currency-update.{{ $currency }}', 'CurrencyUpdate', 'reloadPriceTicker');

                Livewire.on('currencyChanged', (currency) => {
                    Webhook.remove(`currency-update.${this.currentCurrency}`, 'CurrencyUpdate', 'reloadPriceTicker');
                    Webhook.listen(`currency-update.${currency}`, 'CurrencyUpdate', 'reloadPriceTicker');

                    this.currentCurrency = currency;
                });
            },
        }"
    ></div>
@endif
