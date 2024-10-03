<div>
    <x-stats.insights.transactions :data="$transactionDetails" />

    @if (Network::canBeExchanged())
        <x-stats.insights.marketdata :data="$marketData" />
    @endif

    <x-stats.insights.validators :details="$validatorDetails" />

    <x-stats.insights.addresses
        :holdings="$addressHoldings"
        :unique="$uniqueAddresses"
    />

    <x-stats.insights.annual
        :years="$annualData"
    />

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            Livewire.on('currencyChanged', () => {
                @this.updateData();
            });
        });
    </script>
</div>
