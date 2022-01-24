<div class="overflow-auto dark:bg-black bg-theme-secondary-100">
    <div class="py-8 content-container-full-width">
        <div class="px-8 md:px-10 md:w-full">
            <div wire:poll.{{ $refreshInterval }}s class="flex gap-3 w-full md:grid md:grid-cols-2 md:grid-rows-2 xl:grid-cols-4 xl:grid-rows-1">
                <x-stats.stat :label="trans('pages.statistics.highlights.total-supply')" icon="money-coins">
                    <x-currency :currency="Network::currency()">{{ $totalSupply }}</x-currency>
                </x-stats.stat>

                <x-stats.stat :label="trans('pages.statistics.highlights.voting', ['percent' => $votingPercent])" icon="check-mark-box">
                   <x-currency :currency="Network::currency()">{{ $votingValue }}</x-currency>
                </x-stats.stat>

                <x-stats.stat :label="trans('pages.statistics.highlights.registered-delegates')" icon="transaction.delegate-registration">
                    {{ $delegates }}
                </x-stats.stat>

                <x-stats.stat :label="trans('pages.statistics.highlights.wallets')" icon="wallet">
                    {{ $wallets }}
                </x-stats.stat>
            </div>
        </div>
    </div>
</div>
