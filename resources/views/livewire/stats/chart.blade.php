@if($show)
    <div
        class="mt-2 md:mt-6 space-y-2"
        wire:poll.{{ $refreshInterval }}s
    >
        <x-general.card
            class="flex flex-col"
            with-border
        >
            <div class="flex flex-col sm:flex-row sm:justify-between space-y-3 sm:space-y-0">
                <div class="inline-flex items-end space-x-2 sm:space-x-3">
                    <div class="flex flex-col">
                        <div class="sm:hidden mb-2 text-sm font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
                            @lang('pages.statistics.chart.current_price')
                        </div>

                        <span class="text-lg font-bold leading-5.25 md:!leading-[29px] sm:text-3xl text-theme-secondary-900 dark:text-theme-secondary-200">
                            {{ $mainValueFiat }}
                        </span>
                    </div>

                    <span @class([
                        'hidden sm:inline-flex px-1 py-px items-center text-xs font-semibold rounded leading-3.75 mb-[3px]',
                        'border border-transparent bg-theme-success-100 dark:bg-transparent dark:border-theme-success-700 text-theme-success-600 dark:text-theme-success-500' => $mainValueVariation === 'up',
                        'border border-transparent bg-theme-danger-100 dark:bg-transparent dark:border-theme-danger-400 text-theme-danger-400 dark:text-theme-danger-300' => $mainValueVariation === 'down',
                    ])>
                        <span>
                            @if ($mainValueVariation === 'up')
                                +
                            @else
                                -
                            @endif
                        </span>

                        <x-percentage>{{ $mainValuePercentage }}</x-percentage>
                    </span>
                </div>

                <div class="flex flex-1 sm:block sm:flex-none">
                    <a
                        href="{{ route('exchanges') }}"
                        class="button-secondary px-4 py-1.5 w-full"
                    >
                        <div class="flex items-center space-x-2 justify-center">
                            <span class="leading-5">@lang('pages.statistics.exchanges')</span>

                            <x-ark-icon
                                name="arrows.chevron-right-small"
                                size="xs"
                            />
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-4 sm:pt-4 md:pt-6 md:mt-6 sm:border-t lg:w-full border-theme-secondary-300 dark:border-theme-secondary-800">
                <div class="sm:flex sm:items-end lg:items-center sm:justify-between">
                    <div class="sm:flex sm:pt-0 lg:flex-1 lg:mt-0 w-full">
                        <div class="hidden sm:block mt-3 sm:mt-0">
                            <h3 class="mb-0 text-sm font-semibold leading-none text-theme-secondary-700 dark:text-theme-dark-200">
                                @lang('pages.statistics.chart.market-cap')
                            </h3>

                            <p class="mt-2 text-base font-semibold leading-5">
                                @if ($marketCapValue)
                                    <span class="text-theme-secondary-900 dark:text-theme-dark-50 leading-5">
                                        {{ $marketCapValue }}

                                        @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
                                            {{ Settings::currency() }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-theme-secondary-500 dark:text-theme-secondary-700 leading-5">
                                        @lang('general.na')
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="hidden sm:block mt-4 sm:pl-6 sm:mt-0 sm:ml-6 sm:border-l sm:border-theme-secondary-300 dark:border-theme-secondary-800">
                            <h3 class="mb-0 text-sm font-semibold leading-none text-theme-secondary-700 dark:text-theme-dark-200">
                                @lang('pages.statistics.chart.min-price')
                            </h3>

                            <p class="mt-2 text-base font-semibold text-theme-secondary-900 dark:text-theme-dark-50 leading-5">
                                {{ $minPriceValue }}
                            </p>
                        </div>

                        <div class="hidden sm:block mt-4 sm:pl-6 sm:mt-0 sm:ml-6 sm:border-l sm:border-theme-secondary-300 dark:border-theme-secondary-800">
                            <h3 class="mb-0 text-sm font-semibold leading-none text-theme-secondary-700 dark:text-theme-dark-200">
                                @lang('pages.statistics.chart.max-price')
                            </h3>

                            <p class="mt-2 text-base font-semibold text-theme-secondary-900 dark:text-theme-dark-50 leading-5">
                                {{ $maxPriceValue }}
                            </p>
                        </div>
                    </div>

                    <x-stats.periods-selector
                        wire:model="period"
                        :selected="$period"
                        :options="$options"
                        class="hidden sm:block"
                    />
                </div>

                <div class="-mx-4 -mb-4 md:-mx-6 md:-mb-6 p-4 sm:hidden bg-theme-secondary-100 dark:bg-theme-dark-950 rounded-b md:rounded-b-xl">
                    <x-stats.periods-selector
                        wire:model="period"
                        :selected="$period"
                        :options="$options"
                        class="sm:hidden"
                    />

                    <x-ark-chart
                        class="w-full h-[240px]"
                        canvas-class="max-w-full"
                        id="stats-chart-mobile"
                        :data="$datasets->toJson()"
                        labels="[{{ $labels->map(fn ($l) => 'dayjs('.$l.' * 1000).toDate()')->join(',') }}]"
                        :theme="$chartTheme"
                        height="240"
                        :width="null"
                        tooltip-handler="chartTooltip"
                        has-date-time-labels
                        tooltips
                        grid
                        :currency="Settings::currency()"
                        :y-padding="10"
                        :x-padding="0"
                        show-crosshair
                        :date-unit-override="$this->dateUnit"
                    />
                </div>

                <div class="hidden mt-6 -mx-4 -mb-4 md:-mx-6 md:-mb-6 p-3 sm:block bg-theme-secondary-100 dark:bg-theme-dark-950 rounded-b md:rounded-b-xl">
                    <x-ark-chart
                        class="w-full h-auto"
                        canvas-class="max-w-full"
                        id="stats-chart"
                        :data="$datasets->toJson()"
                        labels="[{{ $labels->map(fn ($l) => 'dayjs('.$l.' * 1000).toDate()')->join(',') }}]"
                        :theme="$chartTheme"
                        height="288"
                        :width="null"
                        tooltip-handler="chartTooltip"
                        has-date-time-labels
                        tooltips
                        grid
                        :currency="Settings::currency()"
                        :y-padding="10"
                        :x-padding="0"
                        show-crosshair
                        :date-unit-override="$this->dateUnit"
                    />
                </div>
            </div>
        </x-general.card>

        <x-general.card
            class="p-4 sm:hidden space-y-4 divide-y divide-theme-secondary-300 dark:divide-theme-dark-700"
            with-border
        >
            <div class="space-y-2">
                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('pages.statistics.chart.market-cap')
                </div>

                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50">
                    {{ $marketCapValue }}
                </div>
            </div>

            <div class="space-y-2 pt-4">
                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('pages.statistics.chart.min-price')
                </div>

                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50">
                    {{ $minPriceValue }}
                </div>
            </div>

            <div class="space-y-2 pt-4">
                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('pages.statistics.chart.max-price')
                </div>

                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50">
                    {{ $maxPriceValue }}
                </div>
            </div>
        </x-general.card>
    </div>
@endif

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('message.processed', () => {
                let pos = sessionStorage.getItem('scrollPos');
                setTimeout(() => window.scrollTo(0, parseInt(pos)), 50);
            });
        });

        window.addEventListener('scroll', function () {
            sessionStorage.setItem('scrollPos', window.scrollY.toString());
        });
    </script>
    <script src="{{ mix('js/chart-tooltip.js')}}"></script>
@endpush
