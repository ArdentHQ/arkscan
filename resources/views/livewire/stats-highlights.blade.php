<div wire:poll.{{ $refreshInterval }}s class="flex gap-5 w-full md:grid md:grid-cols-2 md:grid-rows-2 xl:grid-cols-4 xl:grid-rows-1">
    <x-stats.highlight icon="stacked-coins" :title="trans('pages.statistics.highlights.total-supply')" :value="$totalSupply" />
    <x-stats.highlight icon="checkmark-box" :title="trans('pages.statistics.highlights.voting', ['percent' => $votingPercent])" :value="$votingValue" />
    <x-stats.highlight icon="delegate_registration" :title="trans('pages.statistics.highlights.registered-delegates')" :value="$delegates" />
    <x-stats.highlight icon="wallet" :title="trans('pages.statistics.highlights.wallets')" :value="$wallets" />
</div>
