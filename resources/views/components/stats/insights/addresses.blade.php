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
                {{-- Mobile --}}
                <div class="flex md:hidden">
                    <div class="flex flex-col space-y-2 pt-3">
                        <span>&gt; <x-number>{{ $values['grouped'] }}</x-number> {{ Network::currency() }}</span>

                        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                            <x-number>{{ $values['count'] }}</x-number>
                        </span>
                    </div>
                </div>
                {{-- Desktop --}}
                <div class="hidden md:flex w-full justify-between">
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
