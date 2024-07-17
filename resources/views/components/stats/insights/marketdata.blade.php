@props(['data'])

@php ($isFiat = ExplorerNumberFormatter::isFiat(Settings::currency()))

<div
    :class="{
        'hidden md:block': tab !== 'market_data',
    }"
    x-cloak
>
    <div class="hidden px-6 font-semibold md:block md:px-10 md:mx-auto md:max-w-7xl text-theme-secondary-900 dark:text-theme-dark-50">
        @lang('pages.statistics.insights.market_data.title')
    </div>

    <div>
        <x-stats.insights.container :title="trans('pages.statistics.insights.market_data.price')" full-width>
            @foreach (['daily', 'atl', 'ath'] as $item) {{-- Mind the lack of "52w" here --}}
                {{-- Mobile --}}
                <div class="flex md:hidden">
                    <div @class([
                        'flex flex-col justify-between pt-3 space-y-3 w-full',
                        'sm:flex-row sm:space-y-0' => $isFiat,
                        'md:flex-row md:space-y-0' => ! $isFiat
                    ])>
                        <div class="flex flex-col space-y-2">
                            <span>
                                @lang('pages.statistics.insights.market_data.header.'.$item)
                            </span>

                            <div>
                                @if($item === 'daily')
                                    <x-loading.text
                                        wire:loading
                                        wire:target="updateData"
                                        wrapper-class="flex"
                                        width="w-[100px]"
                                    />

                                    <span
                                        class="text-theme-secondary-900 dark:text-theme-dark-50"
                                        wire:loading.remove
                                        wire:target="updateData"
                                    >
                                        {{ $data->prices->dailyLow() }}
                                        -
                                        {{ $data->prices->dailyHigh() }}
                                    </span>
                                @else
                                    <x-loading.text
                                        wire:loading
                                        wire:target="updateData"
                                        wrapper-class="flex"
                                    />

                                    <span
                                        class="text-theme-secondary-900 dark:text-theme-dark-50"
                                        wire:loading.remove
                                        wire:target="updateData"
                                    >
                                        {{ $data->prices->{$item.'Value'}() }}
                                    </span>
                                @endif
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col space-y-2 w-[130px]">
                            @if($item === 'daily')
                                <span>
                                    @lang('pages.statistics.insights.market_data.header.year')
                                </span>

                                <span class="flex text-theme-secondary-900 dark:text-theme-dark-50">
                                    <x-loading.text
                                        wire:loading
                                        wire:target="updateData"
                                        wrapper-class="flex"
                                        width="w-[100px]"
                                    />

                                    <span
                                        wire:loading.remove
                                        wire:target="updateData"
                                    >
                                        {{ $data->prices->yearLow() }}
                                        -
                                        {{ $data->prices->yearHigh() }}
                                    </span>
                                </span>
                            @elseif($item === 'atl' || $item === 'ath')
                                <span>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </span>

                                <div class="flex text-theme-secondary-900 dark:text-theme-dark-50">
                                    <x-loading.text
                                        wire:loading
                                        wire:target="updateData"
                                        wrapper-class="flex"
                                    />

                                    <span
                                        wire:loading.remove
                                        wire:target="updateData"
                                    >
                                        {{ $data->prices->{$item.'Date'}() }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach (['daily', 'year', 'atl', 'ath'] as $item)
                {{-- Desktop --}}
                <div @class([
                    'hidden justify-between w-full md:flex',
                    'xl:w-[770px]' => $isFiat,
                    'xl:w-[950px]' => ! $isFiat,
                ])>
                    <div @class([
                        'flex',
                        'flex-1' => $isFiat,
                        'flex-1 md-lg:flex-none md-lg:w-[150px]' => ! $isFiat,
                     ])">
                        @lang('pages.statistics.insights.market_data.header.'.$item)
                    </div>

                    <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                        <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                            @if($item === 'daily' || $item === 'year')
                                <x-loading.text
                                    wire:loading
                                    wire:target="updateData"
                                    wrapper-class="flex"
                                    width="w-[120px]"
                                />

                                <span
                                    wire:loading.remove
                                    wire:target="updateData"
                                >
                                    {{ $data->prices->{$item.'Low'}() }}
                                    -
                                    {{ $data->prices->{$item.'High'}() }}
                                </span>
                            @else
                                <x-loading.text
                                    wire:loading
                                    wire:target="updateData"
                                    wrapper-class="flex"
                                />

                                <span
                                    wire:loading.remove
                                    wire:target="updateData"
                                >
                                    {{ $data->prices->{$item.'Value'}() }}
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                            @if($item === 'atl' || $item === 'ath')
                                <div>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </div>

                                <div class="flex text-theme-secondary-900 dark:text-theme-dark-50">
                                    <x-loading.text
                                        wire:loading
                                        wire:target="updateData"
                                        wrapper-class="flex"
                                        width="w-[100px]"
                                    />

                                    <span
                                        wire:loading.remove
                                        wire:target="updateData"
                                    >
                                        {{ $data->prices->{$item.'Date'}() }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </x-stats.insights.container>

        <x-stats.insights.container :title="trans('pages.statistics.insights.market_data.exchanges_volume')" full-width>
            @foreach (['today_volume', 'atl', 'ath'] as $item)
                {{-- Mobile --}}
                <div class="flex md:hidden">
                    <div class="flex flex-col justify-between pt-3 space-y-3 w-full sm:flex-row sm:space-y-0">
                        <div class="flex flex-col space-y-2">
                            <span>
                                @lang('pages.statistics.insights.market_data.header.'.$item)
                            </span>

                            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                <x-loading.text
                                    wire:loading
                                    wire:target="updateData"
                                    wrapper-class="flex"
                                    width="w-[100px]"
                                />

                                <span
                                    wire:loading.remove
                                    wire:target="updateData"
                                >
                                    {{ $data->volume->{Str::camel($item.'Value')}() }}
                                </span>
                            </span>
                        </div>

                        @if($item === 'atl' || $item === 'ath')
                            <div class="flex flex-col space-y-2 w-[130px]">
                                <span>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </span>

                                <div class="flex text-theme-secondary-900 dark:text-theme-dark-50">
                                    <x-loading.text
                                        wire:loading
                                        wire:target="updateData"
                                        wrapper-class="flex"
                                    />

                                    <span
                                        wire:loading.remove
                                        wire:target="updateData"
                                    >
                                        {{ $data->volume->{$item.'Date'}() }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Desktop --}}
                <div @class([
                    'hidden justify-between w-full md:flex',
                    'xl:w-[770px]' => $isFiat,
                    'xl:w-[950px]' => ! $isFiat,
                ])>
                    <div @class([
                        'flex',
                        'flex-1' => $isFiat,
                        'flex-1 md-lg:flex-none md-lg:w-[150px]' => ! $isFiat,
                     ])">
                        @lang('pages.statistics.insights.market_data.header.'.$item)
                    </div>

                    <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                        <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                            <x-loading.text
                                wire:loading
                                wire:target="updateData"
                                wrapper-class="flex"
                                width="w-[120px]"
                            />

                            <span
                                wire:loading.remove
                                wire:target="updateData"
                            >
                                {{ $data->volume->{Str::camel($item.'Value')}() }}
                            </span>
                        </div>

                        <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                            @if($item === 'atl' || $item === 'ath')
                                <div>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </div>

                                <div class="flex text-theme-secondary-900 dark:text-theme-dark-50">
                                    <x-loading.text
                                        wire:loading
                                        wire:target="updateData"
                                        wrapper-class="flex"
                                        width="w-[100px]"
                                    />

                                    <span
                                        wire:loading.remove
                                        wire:target="updateData"
                                    >
                                        {{ $data->volume->{$item.'Date'}() }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </x-stats.insights.container>

        <x-stats.insights.container :title="trans('pages.statistics.insights.market_data.market_cap')" full-width>
            @foreach (['today_value', 'atl', 'ath'] as $item)
                {{-- Mobile --}}
                <div class="flex md:hidden">
                    <div class="flex flex-col justify-between pt-3 space-y-3 w-full sm:flex-row sm:space-y-0">
                        <div class="flex flex-col space-y-2">
                            <span>
                                @lang('pages.statistics.insights.market_data.header.'.$item)
                            </span>

                            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                                <x-loading.text
                                    wire:loading
                                    wire:target="updateData"
                                    wrapper-class="flex"
                                    width="w-[100px]"
                                />

                                <span
                                    wire:loading.remove
                                    wire:target="updateData"
                                >
                                    {{ $data->caps->{Str::camel($item.'Value')}() }}
                                </span>
                            </span>
                        </div>

                        @if($item === 'atl' || $item === 'ath')
                            <div class="flex flex-col space-y-2 w-[130px]">
                                <span>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </span>

                                <div class="flex text-theme-secondary-900 dark:text-theme-dark-50">
                                    <x-loading.text
                                        wire:loading
                                        wire:target="updateData"
                                        wrapper-class="flex"
                                    />

                                    <span
                                        wire:loading.remove
                                        wire:target="updateData"
                                    >
                                        {{ $data->caps->{$item.'Date'}() }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Desktop --}}
                <div @class([
                    'hidden justify-between w-full md:flex',
                    'xl:w-[770px]' => $isFiat,
                    'xl:w-[950px]' => ! $isFiat,
                ])>
                    <div @class([
                        'flex',
                        'flex-1' => $isFiat,
                        'flex-1 md-lg:flex-none md-lg:w-[150px]' => ! $isFiat,
                     ])">
                        @lang('pages.statistics.insights.market_data.header.'.$item)
                    </div>

                    <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                        <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                            <x-loading.text
                                wire:loading
                                wire:target="updateData"
                                wrapper-class="flex"
                                width="w-[140px]"
                            />

                            <span
                                wire:loading.remove
                                wire:target="updateData"
                            >
                                {{ $data->caps->{Str::camel($item.'Value')}() }}
                            </span>
                        </div>

                        <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
                            @if($item === 'atl' || $item === 'ath')
                                <div>
                                    @lang('pages.statistics.insights.market_data.header.date'):
                                </div>

                                <div class="flex text-theme-secondary-900 dark:text-theme-dark-50">
                                    <x-loading.text
                                        wire:loading
                                        wire:target="updateData"
                                        wrapper-class="flex"
                                        width="w-[100px]"
                                    />

                                    <span
                                        wire:loading.remove
                                        wire:target="updateData"
                                    >
                                        {{ $data->caps->{$item.'Date'}() }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </x-stats.insights.container>
    </div>
</div>
