@props([
    'voterCount',
    'totalVoted',
    'currentSupply',
])

<x-page-headers.delegates.header-item
    :title="trans('pages.delegates.voting_x_addresses', ['count' => number_format($voterCount)])"
    :attributes="$attributes"
>
    <div class="flex items-center space-x-2">
        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
            <x-currency :currency="Network::currency()">
                {{ ExplorerNumberFormatter::networkCurrency($totalVoted, 0) }}
            </x-currency>
        </span>

        <x-general.badge class="py-px">
            <x-percentage>{{ ($totalVoted / $currentSupply) * 100 }}</x-percentage>
        </x-general.badge>
    </div>
</x-delegates.header-item>
