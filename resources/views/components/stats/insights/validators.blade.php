@props(['details'])

<div
    :class="{
        'hidden md:block': tab !== 'validators',
    }"
    x-cloak
>
    <div class="hidden px-6 font-semibold md:block md:px-10 md:mx-auto md:max-w-7xl text-theme-secondary-900 dark:text-theme-dark-50">
        @lang('pages.statistics.insights.validators.title')
    </div>

    <div>
        <x-stats.insights.container full-width>
            {{-- Mobile --}}
            <x-stats.insights.mobile.validator-row
                :title="trans('pages.statistics.insights.validators.header.most_unique_voters')"
                :value-title="trans('pages.statistics.insights.validators.header.voters')"
                :value="$details->mostUniqueVoters?->voterCount()"
                :model="$details->mostUniqueVoters"
            />

            <x-stats.insights.mobile.validator-row
                :title="trans('pages.statistics.insights.validators.header.least_unique_voters')"
                :value-title="trans('pages.statistics.insights.validators.header.voters')"
                :value="$details->leastUniqueVoters?->voterCount()"
                :model="$details->leastUniqueVoters"
            />

            @if ($details->oldestActiveValidator !== null)
                <x-stats.insights.mobile.validator-row
                    :title="trans('pages.statistics.insights.validators.header.oldest_active_validator')"
                    :value-title="trans('pages.statistics.insights.validators.header.registered')"
                    :value="$details->oldestActiveValidator->value()"
                    :model="$details->oldestActiveValidator->wallet()"
                />
            @endif

            @if ($details->newestActiveValidator !== null)
                <x-stats.insights.mobile.validator-row
                    :title="trans('pages.statistics.insights.validators.header.newest_active_validator')"
                    :value-title="trans('pages.statistics.insights.validators.header.registered')"
                    :value="$details->newestActiveValidator->value()"
                    :model="$details->newestActiveValidator->wallet()"
                />
            @endif

            <x-stats.insights.mobile.validator-row
                :title="trans('pages.statistics.insights.validators.header.most_blocks_forged')"
                :value-title="trans('pages.statistics.insights.validators.header.blocks')"
                :value="$details->mostBlocksForged?->forgedBlocks()"
                :model="$details->mostBlocksForged"
            />

            {{-- Desktop --}}
            <x-stats.insights.desktop.validator-row
                :title="trans('pages.statistics.insights.validators.header.most_unique_voters')"
                :value-title="trans('pages.statistics.insights.validators.header.voters')"
                :value="$details->mostUniqueVoters?->voterCount()"
                :model="$details->mostUniqueVoters"
            />

            <x-stats.insights.desktop.validator-row
                :title="trans('pages.statistics.insights.validators.header.least_unique_voters')"
                :value-title="trans('pages.statistics.insights.validators.header.voters')"
                :value="$details->leastUniqueVoters?->voterCount()"
                :model="$details->leastUniqueVoters"
            />

            @if ($details->oldestActiveValidator !== null)
                <x-stats.insights.desktop.validator-row
                    :title="trans('pages.statistics.insights.validators.header.oldest_active_validator')"
                    :value-title="trans('pages.statistics.insights.validators.header.registered')"
                    :value="$details->oldestActiveValidator->value()"
                    :model="$details->oldestActiveValidator->wallet()"
                />
            @endif

            @if ($details->newestActiveValidator !== null)
                <x-stats.insights.desktop.validator-row
                    :title="trans('pages.statistics.insights.validators.header.newest_active_validator')"
                    :value-title="trans('pages.statistics.insights.validators.header.registered')"
                    :value="$details->newestActiveValidator->value()"
                    :model="$details->newestActiveValidator->wallet()"
                />
            @endif

            <x-stats.insights.desktop.validator-row
                :title="trans('pages.statistics.insights.validators.header.most_blocks_forged')"
                :value-title="trans('pages.statistics.insights.validators.header.blocks')"
                :value="$details->mostBlocksForged?->forgedBlocks()"
                :model="$details->mostBlocksForged"
            />
        </x-stats.insights.container>
    </div>
</div>
