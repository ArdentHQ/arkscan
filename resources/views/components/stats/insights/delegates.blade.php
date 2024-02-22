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
            {{-- Mobile --}}
            <x-stats.insights.mobile.delegate-row
                :title="trans('pages.statistics.insights.delegates.header.most_unique_voters')"
                :value-title="trans('pages.statistics.insights.delegates.header.voters')"
                :value="$details->mostUniqueVoters?->voterCount()"
                :model="$details->mostUniqueVoters"
            />

            <x-stats.insights.mobile.delegate-row
                :title="trans('pages.statistics.insights.delegates.header.least_unique_voters')"
                :value-title="trans('pages.statistics.insights.delegates.header.voters')"
                :value="$details->leastUniqueVoters?->voterCount()"
                :model="$details->leastUniqueVoters"
            />

            @if ($details->oldestActiveDelegate !== null)
                <x-stats.insights.mobile.delegate-row
                    :title="trans('pages.statistics.insights.delegates.header.oldest_active_delegate')"
                    :value-title="trans('pages.statistics.insights.delegates.header.registered')"
                    :value="$details->oldestActiveDelegate->value()"
                    :model="$details->oldestActiveDelegate->wallet()"
                />
            @endif

            @if ($details->newestActiveDelegate !== null)
                <x-stats.insights.mobile.delegate-row
                    :title="trans('pages.statistics.insights.delegates.header.newest_active_delegate')"
                    :value-title="trans('pages.statistics.insights.delegates.header.registered')"
                    :value="$details->newestActiveDelegate->value()"
                    :model="$details->newestActiveDelegate->wallet()"
                />
            @endif

            <x-stats.insights.mobile.delegate-row
                :title="trans('pages.statistics.insights.delegates.header.most_blocks_forged')"
                :value-title="trans('pages.statistics.insights.delegates.header.blocks')"
                :value="$details->mostBlocksForged?->forgedBlocks()"
                :model="$details->mostBlocksForged"
            />

            {{-- Desktop --}}
            <x-stats.insights.desktop.delegate-row
                :title="trans('pages.statistics.insights.delegates.header.most_unique_voters')"
                :value-title="trans('pages.statistics.insights.delegates.header.voters')"
                :value="$details->mostUniqueVoters?->voterCount()"
                :model="$details->mostUniqueVoters"
            />

            <x-stats.insights.desktop.delegate-row
                :title="trans('pages.statistics.insights.delegates.header.least_unique_voters')"
                :value-title="trans('pages.statistics.insights.delegates.header.voters')"
                :value="$details->leastUniqueVoters?->voterCount()"
                :model="$details->leastUniqueVoters"
            />

            @if ($details->oldestActiveDelegate !== null)
                <x-stats.insights.desktop.delegate-row
                    :title="trans('pages.statistics.insights.delegates.header.oldest_active_delegate')"
                    :value-title="trans('pages.statistics.insights.delegates.header.registered')"
                    :value="$details->oldestActiveDelegate->value()"
                    :model="$details->oldestActiveDelegate->wallet()"
                />
            @endif

            @if ($details->newestActiveDelegate !== null)
                <x-stats.insights.desktop.delegate-row
                    :title="trans('pages.statistics.insights.delegates.header.newest_active_delegate')"
                    :value-title="trans('pages.statistics.insights.delegates.header.registered')"
                    :value="$details->newestActiveDelegate->value()"
                    :model="$details->newestActiveDelegate->wallet()"
                />
            @endif

            <x-stats.insights.desktop.delegate-row
                :title="trans('pages.statistics.insights.delegates.header.most_blocks_forged')"
                :value-title="trans('pages.statistics.insights.delegates.header.blocks')"
                :value="$details->mostBlocksForged?->forgedBlocks()"
                :model="$details->mostBlocksForged"
            />
        </x-stats.insights.container>
    </div>
</div>
