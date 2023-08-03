@props([
    'statistics',
    'voterCount',
    'totalVoted',
    'currentSupply',
])

<div class="flex flex-col px-6 pt-8 pb-6 space-y-6 font-semibold md:px-10 md:mx-auto md:max-w-7xl">
    <div class="flex flex-col space-y-1.5">
        <h1 class="mb-0 text-lg md:text-2xl leading-5.25 md:leading-[1.8125rem]">
            @lang('pages.delegates.title')
        </h1>

        <span class="text-xs text-theme-secondary-500 leading-3.75 dark:text-theme-dark-500">
            @lang('pages.delegates.subtitle')
        </span>
    </div>

    <div class="flex flex-col space-y-2 sm:space-y-3 xl:flex-row xl:space-y-0 xl:space-x-3">
        <div class="flex flex-col flex-1 space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2 md:space-x-3">
            <x-page-headers.delegates.missed-blocks :statistics="$statistics" />
            <x-page-headers.delegates.voting
                :voter-count="$voterCount"
                :total-voted="$totalVoted"
                :current-supply="$currentSupply"
            />
        </div>

        <x-page-headers.delegates.explore />
    </div>
</div>
