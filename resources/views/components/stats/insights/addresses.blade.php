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
        {{-- TODO: proper styling --}}
        <x-stats.insights.container :title="trans('pages.statistics.insights.addresses.holdings')">
            @foreach($holdings as $key => $values)
                <x-stats.insights.row :title="trans('pages.statistics.insights.addresses.header.addresses')">
                    <x-number>{{ $values['count'] }}</x-number>
                </x-stats.insights.row>
            @endforeach
        </x-stats.insights.container>
    </div>
</div>
