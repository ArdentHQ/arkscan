@props(['details'])

<div
    :class="{
        'hidden md:block': tab !== 'transactions',
    }"
    x-cloak
>
    <div class="hidden md:block px-6 md:px-10 md:mx-auto md:max-w-7xl font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
        @lang('pages.statistics.insights.transactions.title')
    </div>

    <div>
        <x-stats.insights.container :title="trans('pages.statistics.insights.transactions.all_time')">
            @foreach($details as $detailKey => $detail)
                <x-stats.insights.row :title="trans('pages.statistics.insights.transactions.header.'.$detailKey)">
                    <x-number>{{ $detail }}</x-number>
                </x-stats.insights.row>
            @endforeach
        </x-stats.insights.container>
    </div>
</div>
