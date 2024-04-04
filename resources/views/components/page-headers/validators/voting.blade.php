@props([
    'voterCount',
    'totalVoted',
    'votesPercentage',
])

<x-page-headers.header-item
    :title="trans('pages.validators.voting_x_addresses', ['count' => number_format($voterCount)])"
    :attributes="$attributes"
>
    <div class="flex items-center space-x-2">
        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
            <x-currency
                :currency="Network::currency()"
                :decimals="0"
            >
                {{ $totalVoted }}
            </x-currency>
        </span>

        <x-general.badge class="py-px text-theme-secondary-700">
            <x-percentage>{{ $votesPercentage }}</x-percentage>
        </x-general.badge>
    </div>
</x-page-headers.header-item>
