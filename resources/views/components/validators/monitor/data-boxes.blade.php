@props([
    'statistics',
    'height',
])

@php ($isLoading = ! $this->isReady || count($statistics) === 0)

<div
    id="statistics-list"
    class="grid grid-cols-1 gap-2 w-full sm:grid-cols-2 md:gap-3 xl:grid-cols-4"
    wire:key="poll_statistics"
>
    <x-general.card class="flex items-center space-x-6">
        <x-validators.monitor.stat
            :title="trans('pages.validator-monitor.stats.forging')"
            :value="Arr::get($statistics, 'performances.forging')"
            color="bg-theme-success-700 dark:bg-theme-success-500"
            :loading="$isLoading"
        />

        <x-validators.monitor.stat
            :title="trans('pages.validator-monitor.stats.missed')"
            :value="Arr::get($statistics, 'performances.missed')"
            color="bg-theme-warning-700 dark:bg-theme-warning-400"
            :loading="$isLoading"
        />

        <x-validators.monitor.stat
            :title="trans('pages.validator-monitor.stats.not_forging')"
            :value="Arr::get($statistics, 'performances.missing')"
            color="bg-theme-danger-600 dark:bg-theme-danger-400"
            :loading="$isLoading"
        />
    </x-general.card>

    <x-general.card>
        <x-general.detail
            :title="trans('pages.validator-monitor.stats.current_height')"
            :loading="$isLoading"
        >
            <x-number>{{ $height }}</x-number>
        </x-general.detail>
    </x-general.card>

    <x-general.card>
        <x-general.detail
            :title="trans('pages.validator-monitor.stats.current_round')"
            :loading="$isLoading"
        >
            {{ Arr::get($statistics, 'blockCount') }}
        </x-general.detail>
    </x-general.card>

    <x-general.card>
        <x-general.detail
            :title="trans('pages.validator-monitor.stats.next_slot')"
            :loading="$isLoading"
        >
            @if (Arr::get($statistics, 'nextValidator') !== null)
                <a
                    href="{{ route('wallet', $statistics['nextValidator']->model()) }}"
                    class="link"
                >
                    <x-truncate-middle>{{ $statistics['nextValidator']->address() }}</x-truncate-middle>
                </a>
            @else
                <span class="text-theme-secondary-500 dark:text-theme-dark-700">
                    @lang('general.na')
                </span>
            @endif
        </x-general.detail>
    </x-general.card>
</div>
