@props([
    'voterCount',
    'totalVoted',
    'currentSupply',
    'missedBlocks',
    'delegatesMissed',
])

<x-page-headers.generic
    :title="trans('pages.delegates.title')"
    :subtitle="trans('pages.delegates.subtitle')"
>
    <div class="flex flex-col flex-1 space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2 md:space-x-3">
        <x-page-headers.delegates.missed-blocks
            :missed-blocks="$missedBlocks"
            :delegates-missed="$delegatesMissed"
        />
        <x-page-headers.delegates.voting
            :voter-count="$voterCount"
            :total-voted="$totalVoted"
            :current-supply="$currentSupply"
        />
    </div>

    <x-page-headers.delegates.explore />
</x-page-headers.generic>
