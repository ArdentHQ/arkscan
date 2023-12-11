@props([
    'holdings',
])

<div
    :class="{
        'hidden md:block': tab !== 'addresses',
    }"
    x-cloak
>
    <div class="hidden px-6 font-semibold md:block md:px-10 md:mx-auto md:max-w-7xl text-theme-secondary-900 dark:text-theme-dark-50">
        @lang('pages.statistics.insights.addresses.title')
    </div>

    <div>
        <x-stats.insights.container :title="trans('pages.statistics.insights.addresses.holdings')">
            @foreach($holdings as $key => $values)
                <div class="w-full flex justify-between">
                    <div class="flex flex-1">
                        <span>&gt; <x-number>{{ $values['grouped'] }}</x-number> {{ Network::currency() }}</span>
                    </div>
                    <div class="flex flex-1 justify-between">
                        <span>@lang('pages.statistics.insights.addresses.header.addresses'):</span>
                        <span class="text-theme-secondary-900 dark:text-theme-dark-50"><x-number>{{ $values['count'] }}</x-number></span>
                    </div>
                </div>
            @endforeach
        </x-stats.insights.container>
    </div>
</div>
