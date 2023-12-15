@props([
    'details',
    'averages',
    'records',
])

<div
    :class="{
        'hidden md:block': tab !== 'transactions',
    }"
    x-cloak
>
    <div class="hidden px-6 font-semibold md:block md:px-10 md:mx-auto md:max-w-7xl text-theme-secondary-900 dark:text-theme-dark-50">
        @lang('pages.statistics.insights.transactions.title')
    </div>

    <div>
        <x-stats.insights.container :title="trans('pages.statistics.insights.transactions.all_time')" apply-spacing>
            @foreach($details as $key => $detail)
                <x-stats.insights.row :title="trans('pages.statistics.insights.transactions.header.'.$key)">
                    <x-number>{{ $detail }}</x-number>
                </x-stats.insights.row>
            @endforeach
        </x-stats.insights.container>

        <x-stats.insights.container :title="trans('pages.statistics.insights.transactions.daily_averages')" apply-spacing>
            @foreach($averages as $key => $detail)
                <x-stats.insights.row :title="trans('pages.statistics.insights.transactions.header.'.$key)">
                    {{ $detail }}
                </x-stats.insights.row>
            @endforeach
        </x-stats.insights.container>

        <x-stats.insights.container
            :title="trans('pages.statistics.insights.transactions.records')"
            full-width
        >
            @foreach($records as $key => $model)

                <x-stats.insights.mobile.transaction-record-row :key="$key" :model="$model" />

                <x-stats.insights.desktop.transaction-record-row :key="$key" :model="$model" />

            @endforeach
        </x-stats.insights.container>
    </div>
</div>
