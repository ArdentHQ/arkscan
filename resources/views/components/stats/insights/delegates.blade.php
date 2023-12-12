@props(['details'])

<div
    :class="{
        'hidden md:block': tab !== 'delegates',
    }"
    x-cloak
>
    <div class="hidden px-6 font-semibold md:block md:px-10 md:mx-auto md:max-w-7xl text-theme-secondary-900 dark:text-theme-dark-50">
        @lang('pages.statistics.insights.delegates.title')
    </div>

    <div>
        <x-stats.insights.container full-width>
            <x-stats.insights.delegate-row
                :title="trans('pages.statistics.insights.delegates.header.most_unique_voters')"
                :value-title="trans('pages.statistics.insights.delegates.header.voters')"
                :value="$details['most_unique_voters']?->voterCount()"
                :model="$details['most_unique_voters']"
            />

            <x-stats.insights.delegate-row
                :title="trans('pages.statistics.insights.delegates.header.least_unique_voters')"
                :value-title="trans('pages.statistics.insights.delegates.header.voters')"
                :value="$details['least_unique_voters']?->voterCount()"
                :model="$details['least_unique_voters']"
            />

            @if($details['oldest_active_delegate'] !== null)
                <x-stats.insights.delegate-row
                    :title="trans('pages.statistics.insights.delegates.header.oldest_active_delegate')"
                    :value-title="trans('pages.statistics.insights.delegates.header.registered')"
                    :value="$details['oldest_active_delegate']['value']"
                    :model="$details['oldest_active_delegate']['delegate']"
                />
            @endif

            @if($details['newest_active_delegate'] !== null)
                <x-stats.insights.delegate-row
                    :title="trans('pages.statistics.insights.delegates.header.newest_active_delegate')"
                    :value-title="trans('pages.statistics.insights.delegates.header.registered')"
                    :value="$details['newest_active_delegate']['value']"
                    :model="$details['newest_active_delegate']['delegate']"
                />
            @endif

            <x-stats.insights.delegate-row
                :title="trans('pages.statistics.insights.delegates.header.most_blocks_forged')"
                :value-title="trans('pages.statistics.insights.delegates.header.blocks')"
                :value="$details['most_blocks_forged']?->forgedBlocks()"
                :model="$details['most_blocks_forged']"
            />
        </x-stats.insights.container>
    </div>
</div>
