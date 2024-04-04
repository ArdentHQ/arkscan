@props([
    'voterCount',
    'totalVoted',
    'missedBlocks',
    'validatorsMissed',
    'votesPercentage',
])

<x-page-headers.generic
    :title="trans('pages.validators.title')"
    :subtitle="trans('pages.validators.subtitle')"
>
    <div class="flex flex-col flex-1 space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2 md:space-x-3">
        <x-page-headers.validators.missed-blocks
            :missed-blocks="$missedBlocks"
            :validators-missed="$validatorsMissed"
        />
        <x-page-headers.validators.voting
            :voter-count="$voterCount"
            :total-voted="$totalVoted"
            :votes-percentage="$votesPercentage"
        />
    </div>

    <x-page-headers.validators.explore />
</x-page-headers.generic>
